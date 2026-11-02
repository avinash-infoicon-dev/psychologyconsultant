<?php

namespace App\Services\Messaging;

use App\Events\ExchangeBalanceUpdated;
use App\Exceptions\ChannelNotAllowedException;
use App\Exceptions\ExchangeMessageLimitException;
use App\Exceptions\QuotaExceededException;
use App\Models\Conversation;
use App\Models\Credit;
use App\Models\Exchange;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Support\MessagingLimits;

class ExchangeQuotaService
{
    public function __construct(protected ExchangeLedgerService $ledger) {}

    /**
     * Call before creating a patient message.
     *
     * Entitlement order (§6.2):
     * 1) Active subscription with remaining exchanges
     * 2) Credit balance (on-demand / additional packs)
     * 3) Otherwise QuotaExceededException
     *
     * When the patient has no entitlement left (quota 0 AND no credits),
     * every further patient message is blocked — including follow-ups in
     * an open exchange — and the UI redirects to buy more.
     *
     * Exchange units are still only *consumed* when starting a new exchange
     * (patient message after psych reply, or the first message).
     *
     * @throws QuotaExceededException
     */
    public function consumeIfStartingExchange(User $sender, Conversation $conversation): void
    {
        // Legacy: consumed a unit on the patient send that started an exchange.
        // $this->legacyConsumeOnPatientSend($sender, $conversation);

        $this->assertPatientCanSend($sender, $conversation);
    }

    /**
     * @throws QuotaExceededException
     */
    public function assertPatientCanSend(User $sender, Conversation $conversation): void
    {
        if (! $sender->isPatient()) {
            return;
        }

        $patient = $conversation->patient;
        $patient->unsetRelation('subscriptions');

        if ($this->pendingExchange($conversation)) {
            return;
        }

        $held = Exchange::query()
            ->where('patient_id', $patient->id)
            ->where('status', Exchange::STATUS_PENDING)
            ->count();

        $subscription = $patient->activeSubscription();
        if ($subscription) {
            $available = max(0, $subscription->remaining_exchanges - $held);
            if ($available < 1) {
                throw new QuotaExceededException();
            }

            return;
        }

        if ($patient->creditBalance() <= $held) {
            throw new QuotaExceededException();
        }
    }

    public function openOrContinueExchange(User $sender, Conversation $conversation, Message $message): ?Exchange
    {
        if (! $sender->isPatient()) {
            return null;
        }

        $conversation->loadMissing(['patient', 'psychologist']);
        Conversation::query()->whereKey($conversation->id)->lockForUpdate()->first();

        $patient = $conversation->patient;
        $patient?->unsetRelation('subscriptions');
        $subscription = $patient?->activeSubscription();

        $pending = $this->pendingExchange($conversation, lock: true);
        if ($pending) {
            $this->ensureSnapshotLimits($pending, $conversation);

            return $pending;
        }

        $plan = $subscription?->plan;
        if ($subscription && ! $plan) {
            $subscription->loadMissing('plan');
            $plan = $subscription->plan;
        }

        return Exchange::create([
            'conversation_id' => $conversation->id,
            'patient_id' => $conversation->patient_id,
            'psychologist_id' => $conversation->psychologist_id,
            'subscription_id' => $subscription?->id,
            'patient_message_id' => $message->id,
            'status' => Exchange::STATUS_PENDING,
            'patient_message_limit' => MessagingLimits::snapshotMessagesPerExchange(
                $plan,
                MessagingLimits::ROLE_PATIENT
            ),
            'psychologist_message_limit' => MessagingLimits::snapshotMessagesPerExchange(
                $plan,
                MessagingLimits::ROLE_PSYCHOLOGIST
            ),
            'patient_messages_used' => 0,
            'psychologist_messages_used' => 0,
        ]);
    }

    public function completePendingExchange(User $sender, Conversation $conversation, Message $message): ?Exchange
    {
        if ($sender->isPatient()) {
            return null;
        }

        Conversation::query()->whereKey($conversation->id)->lockForUpdate()->first();
        $conversation->loadMissing('patient');

        $exchange = $this->pendingExchange($conversation, lock: true);
        if (! $exchange || $exchange->isCompleted()) {
            return null;
        }

        $exchange->update([
            'psychologist_message_id' => $message->id,
            'status' => Exchange::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $this->consumeCompletedExchange($conversation, $exchange);
        $this->maybeNotifyQuotaLow($conversation->patient);
        $this->broadcastBalance($conversation->patient);

        return $exchange->fresh();
    }

    /**
     * @throws ExchangeMessageLimitException
     */
    public function assertExchangeMessageAllowed(User $sender, Conversation $conversation): void
    {
        Conversation::query()->whereKey($conversation->id)->lockForUpdate()->first();

        $exchange = $this->targetExchangeForMessage($sender, $conversation, lock: true);
        if (! $exchange) {
            return;
        }

        $this->ensureSnapshotLimits($exchange, $conversation);

        // Old: snapshot only — admin 0 (unlimited) could not uncap an in-progress exchange.
        // $limit = $sender->isPatient()
        //     ? (int) $exchange->patient_message_limit
        //     : (int) $exchange->psychologist_message_limit;
        $limit = $this->effectiveRoleMessageLimit($sender, $exchange, $conversation);

        if ($limit < 1) {
            return;
        }

        $used = $sender->isPatient()
            ? (int) $exchange->patient_messages_used
            : (int) $exchange->psychologist_messages_used;

        if ($used >= $limit) {
            throw new ExchangeMessageLimitException();
        }
    }

    /**
     * @throws ExchangeMessageLimitException
     */
    public function recordExchangeMessage(User $sender, Conversation $conversation): void
    {
        $exchange = $this->targetExchangeForMessage($sender, $conversation, lock: true);
        if (! $exchange) {
            return;
        }

        $this->ensureSnapshotLimits($exchange, $conversation);
        $exchange->refresh();

        $usedColumn = $sender->isPatient() ? 'patient_messages_used' : 'psychologist_messages_used';
        // Old: $limit = (int) $exchange->{patient|psychologist}_message_limit;
        $limit = $this->effectiveRoleMessageLimit($sender, $exchange, $conversation);

        // Unlimited (snapshotted 0): still count for audit, never cap.
        if ($limit < 1) {
            $exchange->increment($usedColumn);

            return;
        }

        $updated = Exchange::query()
            ->whereKey($exchange->id)
            ->where($usedColumn, '<', $limit)
            ->increment($usedColumn);

        if ($updated === 0) {
            throw new ExchangeMessageLimitException();
        }
    }

    /**
     * @return array{used: int, limit: int, remaining: int, at_cap: bool}|null
     */
    public function currentExchangeMessageUsage(?Conversation $conversation, User $user): ?array
    {
        if (! $conversation) {
            return null;
        }

        $exchange = $this->pendingExchange($conversation);
        if (! $exchange && ! $user->isPatient()) {
            $exchange = $this->latestCompletedExchange($conversation);
        }

        $plan = $this->patientPlan($conversation->patient);

        if (! $exchange) {
            if (! $user->isPatient()) {
                return null;
            }

            $limit = MessagingLimits::patientMessagesPerExchange($plan);
            if ($limit === null) {
                return null;
            }

            return [
                'used' => 0,
                'limit' => $limit,
                'remaining' => $limit,
                'at_cap' => false,
            ];
        }

        // Old: UI used the frozen snapshot even after admin set global/plan 0 (unlimited).
        // $limit = $user->isPatient()
        //     ? $exchange->patient_message_limit
        //     : $exchange->psychologist_message_limit;
        $limit = $this->effectiveRoleMessageLimit($user, $exchange, $conversation);

        if ($limit < 1) {
            return null;
        }

        $used = $user->isPatient()
            ? (int) $exchange->patient_messages_used
            : (int) $exchange->psychologist_messages_used;

        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
            'at_cap' => $used >= $limit,
        ];
    }

    protected function targetExchangeForMessage(User $sender, Conversation $conversation, bool $lock = false): ?Exchange
    {
        $pending = $this->pendingExchange($conversation, $lock);
        if ($pending) {
            return $pending;
        }

        if ($sender->isPatient()) {
            return null;
        }

        return $this->latestCompletedExchange($conversation, $lock);
    }

    /**
     * Cap used for send/UI. Live unlimited (plan/global 0) wins over an older snapshot.
     * Otherwise the snapshotted cap is kept so later admin changes do not rewrite it.
     */
    protected function effectiveRoleMessageLimit(User $sender, Exchange $exchange, Conversation $conversation): int
    {
        $plan = $this->patientPlan($conversation->patient);
        $live = $sender->isPatient()
            ? MessagingLimits::patientMessagesPerExchange($plan)
            : MessagingLimits::psychologistMessagesPerExchange($plan);

        if ($live === null) {
            return 0;
        }

        $snapshot = $sender->isPatient()
            ? $exchange->patient_message_limit
            : $exchange->psychologist_message_limit;

        if ($snapshot !== null && $snapshot !== '') {
            return (int) $snapshot;
        }

        return MessagingLimits::snapshotMessagesPerExchange(
            $plan,
            $sender->isPatient() ? MessagingLimits::ROLE_PATIENT : MessagingLimits::ROLE_PSYCHOLOGIST
        );
    }

    protected function latestCompletedExchange(Conversation $conversation, bool $lock = false): ?Exchange
    {
        $query = Exchange::query()
            ->where('conversation_id', $conversation->id)
            ->where('status', Exchange::STATUS_COMPLETED)
            ->latest('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    protected function ensureSnapshotLimits(Exchange $exchange, Conversation $conversation): void
    {
        $plan = $this->patientPlan($conversation->patient);
        $dirty = false;

        if ($exchange->patient_message_limit === null) {
            $exchange->patient_message_limit = MessagingLimits::snapshotMessagesPerExchange(
                $plan,
                MessagingLimits::ROLE_PATIENT
            );
            $dirty = true;
        }

        if ($exchange->psychologist_message_limit === null) {
            $exchange->psychologist_message_limit = MessagingLimits::snapshotMessagesPerExchange(
                $plan,
                MessagingLimits::ROLE_PSYCHOLOGIST
            );
            $dirty = true;
        }

        if ($dirty) {
            $exchange->save();
        }
    }

    protected function patientPlan(?Patient $patient): ?Plan
    {
        if (! $patient) {
            return null;
        }

        $patient->unsetRelation('subscriptions');
        $subscription = $patient->activeSubscription();
        $subscription?->loadMissing('plan');

        return $subscription?->plan;
    }

    protected function consumeCompletedExchange(Conversation $conversation, Exchange $exchange): void
    {
        $patient = $conversation->patient;
        if (! $patient) {
            return;
        }

        $patient->unsetRelation('subscriptions');
        $subscription = $exchange->subscription_id
            ? Subscription::query()->find($exchange->subscription_id)
            : $patient->activeSubscription();

        $active = $subscription && $subscription->isActive() ? $subscription : null;
        $note = "Conversation #{$conversation->id}";

        // Old: $subscription->recordConsumedExchange() or Credit::create(consumption).
        $this->ledger->recordConsumption($patient, $active, $exchange, $note);
    }

    protected function pendingExchange(Conversation $conversation, bool $lock = false): ?Exchange
    {
        $query = Exchange::query()
            ->where('conversation_id', $conversation->id)
            ->where('status', Exchange::STATUS_PENDING)
            ->latest('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * @return array{
     *     included: int,
     *     consumed: int,
     *     purchased: int,
     *     remaining: int,
     *     renewal_date: ?string,
     *     renewal_label: ?string,
     *     has_subscription: bool
     * }
     */
    public function balanceForPatient(?Patient $patient): array
    {
        if (! $patient) {
            return $this->emptyBalance();
        }

        $patient->unsetRelation('subscriptions');
        $subscription = $patient->activeSubscription();
        if ($subscription) {
            return $subscription->balancePayload();
        }

        $credits = max(0, (int) $patient->creditBalance());

        return [
            'included' => 0,
            'consumed' => 0,
            'purchased' => $credits,
            'remaining' => $credits,
            'total' => $credits,
            'renewal_date' => null,
            'renewal_label' => null,
            'has_subscription' => false,
        ];
    }

    protected function maybeNotifyQuotaLow(?Patient $patient): void
    {
        if (! $patient) {
            return;
        }

        $patient->unsetRelation('subscriptions');
        $subscription = $patient->activeSubscription();
        if (! $subscription) {
            return;
        }

        $remaining = (int) $subscription->remaining_exchanges;
        $total = (int) $subscription->included_exchanges + (int) $subscription->purchased_exchanges;
        $countFloor = (int) config('healthcare.quota.low_remaining_count', 2);
        $percentFloor = (int) config('healthcare.quota.low_remaining_percent', 20);
        $percentRemaining = $total > 0 ? ($remaining / $total) * 100 : 0;

        if ($remaining > $countFloor && ($total < 1 || $percentRemaining > $percentFloor)) {
            return;
        }

        $patient->loadMissing('user');
        if (! $patient->user) {
            return;
        }

        $periodKey = $subscription->current_period_starts_at?->toDateString()
            ?: $subscription->id;
        $payload = \App\Notifications\ExchangeQuotaNotification::low($subscription, $remaining);
        app(\App\Services\NotificationService::class)->sendPayload(
            $patient->user,
            $payload,
            'quota-low:'.$subscription->id.':'.$periodKey,
        );
    }

    protected function broadcastBalance(?Patient $patient): void
    {
        if (! $patient) {
            return;
        }

        $patient->loadMissing('user');
        $userId = (int) ($patient->user_id ?: $patient->user?->id);
        if ($userId < 1) {
            return;
        }

        event(new ExchangeBalanceUpdated($userId, $this->balanceForPatient($patient)));
    }

    /**
     * @return array{
     *     included: int,
     *     consumed: int,
     *     purchased: int,
     *     remaining: int,
     *     renewal_date: ?string,
     *     renewal_label: ?string,
     *     has_subscription: bool
     * }
     */
    protected function emptyBalance(): array
    {
        return [
            'included' => 0,
            'consumed' => 0,
            'purchased' => 0,
            'remaining' => 0,
            'total' => 0,
            'renewal_date' => null,
            'renewal_label' => null,
            'has_subscription' => false,
        ];
    }

    /**
     * Legacy consume-on-patient-send (kept for reference).
     *
     * @throws QuotaExceededException
     */
    protected function legacyConsumeOnPatientSend(User $sender, Conversation $conversation): void
    {
        //f (! $sender->isPatient() || ! $this->startsNewExchange($conversation)) {
        if (! $sender->isPatient()) {
            return;
        }

        //$subscription = $conversation->patient->activeSubscription();
        $patient = $conversation->patient;
        // Avoid a stale in-memory patient relation from earlier in the request.
        $patient->unsetRelation('subscriptions');

        $subscription = $patient->activeSubscription();
        $hasSubQuota = $subscription && $subscription->hasExchangesRemaining();
        $hasCredits = $patient->creditBalance() > 0;

        // No plan exchanges and no credits → block all further patient sends.
        if (! $hasSubQuota && ! $hasCredits) {
            throw new QuotaExceededException();
        }

        // Follow-up in an open exchange does not consume another unit.
        //if ($subscription && $subscription->hasExchangesRemaining()) {
        if (! $this->startsNewExchange($conversation)) {
            return;
        }

        if ($hasSubQuota) {
            $subscription->consumeExchange();

            return;
        }

            //  //  if ($conversation->patient->creditBalance() > 0) {
            //     if ($patient->creditBalance() > 0) {
            //         Credit::create([
            //             //'patient_id' => $conversation->patient_id,
            //             'patient_id' => $patient->id,
            //             'amount' => -1,
            //             'source' => 'consumption',
            //             'note' => "Conversation #{$conversation->id}",
            //         ]);
        
            //         return;
            //     }
        
            //     throw new QuotaExceededException();
        Credit::create([
            'patient_id' => $patient->id,
            'amount' => -1,
            'source' => 'consumption',
            'note' => "Conversation #{$conversation->id}",
        ]);
    }

    /**
     * Whether the patient's *current entitlement source* allows a channel.
     *
     * - Subscription with remaining exchanges → that plan's channels
     * - Else credits → the credit product's channels (On-demand = no audio;
     *   Additional exchange with null channels inherits the active sub plan)
     *
     * Psychologists are always allowed (they reply on the patient's thread).
     */
    public function patientAllowsChannel(User $user, string $channel): bool
    {
        if (! $user->isPatient()) {
            return true;
        }

        $patient = $user->patient;
        if (! $patient) {
            return false;
        }

        $plan = $this->planForMessagingChannels($patient);

        return $plan ? $plan->allowsChannel($channel) : false;
    }

    /**
     * @throws ChannelNotAllowedException
     */
    public function assertChannelAllowed(User $sender, string $channel): void
    {
        if ($this->patientAllowsChannel($sender, $channel)) {
            return;
        }

        throw new ChannelNotAllowedException(
            __('message_api.channel_not_allowed_'.$channel)
        );
    }

    /**
     * Plan that defines allowed channels for the next patient send.
     */
    public function planForMessagingChannels(Patient $patient): ?Plan
    {
        $patient->unsetRelation('subscriptions');
        $subscription = $patient->activeSubscription();
        $hasSubQuota = $subscription && $subscription->hasExchangesRemaining();

        if ($hasSubQuota) {
            return $subscription->plan;
        }

        if ($patient->creditBalance() > 0) {
            $creditPlan = $this->resolveCreditSourcePlan($patient);

            // Additional exchange inherits the active subscription plan's channels.
            if (
                $creditPlan
                && $creditPlan->plan_type === Plan::TYPE_ADDON
                && ($creditPlan->channels === null || $creditPlan->channels === [])
                && $subscription
            ) {
                return $subscription->plan;
            }

            return $creditPlan;
        }

        // No entitlement — still return sub plan for informational UI if present.
        return $subscription?->plan;
    }

    protected function resolveCreditSourcePlan(Patient $patient): ?Plan
    {
        // Prefer newest positive purchase that still has a plan_id.
        $purchase = Credit::query()
            ->where('patient_id', $patient->id)
            ->where('source', 'purchase')
            ->where('amount', '>', 0)
            ->whereNotNull('plan_id')
            ->latest('id')
            ->first();

        if ($purchase?->plan_id) {
            return Plan::query()->find($purchase->plan_id);
        }

        // Legacy credits (before plan_id): recover slug from purchase note.
        $legacy = Credit::query()
            ->where('patient_id', $patient->id)
            ->where('source', 'purchase')
            ->where('amount', '>', 0)
            ->latest('id')
            ->first();

        if ($legacy?->note && preg_match('/plan "([^"]+)"/', $legacy->note, $matches)) {
            $fromNote = Plan::query()->where('slug', $matches[1])->first();
            if ($fromNote) {
                return $fromNote;
            }
        }

        // Safe fallback when using credits of unknown origin: On-demand (no audio).
        return Plan::query()
            ->where('plan_type', Plan::TYPE_ON_DEMAND)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    /**
     * An "exchange" is one patient message + the psychologist's reply. A
     * new exchange starts when the patient sends and the previous message
     * in the thread was from the psychologist (or there is none yet).
     */
    protected function startsNewExchange(Conversation $conversation): bool
    {
        $lastMessage = $conversation->messages()->latest()->first();

        return $lastMessage === null || $lastMessage->sender_id !== $conversation->patient->user_id;
    }
}
