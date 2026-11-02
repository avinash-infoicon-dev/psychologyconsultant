<?php

namespace App\Services\Messaging;

use App\Events\ExchangeBalanceUpdated;
use App\Models\Credit;
use App\Models\Exchange;
use App\Models\ExchangeTransaction;
use App\Models\Patient;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExchangeLedgerService
{
    public function record(array $attributes): ExchangeTransaction
    {
        return ExchangeTransaction::query()->create($attributes);
    }

    public function recordIncluded(Subscription $subscription, int $quantity, string $reason): ExchangeTransaction
    {
        return $this->record([
            'patient_id' => $subscription->patient_id,
            'subscription_id' => $subscription->id,
            'type' => ExchangeTransaction::TYPE_INCLUDED,
            'quantity' => $quantity,
            'reason' => $reason,
        ]);
    }

    public function recordPurchased(Subscription $subscription, int $quantity, string $reason): ExchangeTransaction
    {
        return $this->record([
            'patient_id' => $subscription->patient_id,
            'subscription_id' => $subscription->id,
            'type' => ExchangeTransaction::TYPE_PURCHASED,
            'quantity' => $quantity,
            'reason' => $reason,
        ]);
    }

    public function recordRenewal(Subscription $subscription, int $includedQuantity, int $expiredIncluded, string $reason): void
    {
        if ($expiredIncluded > 0) {
            $this->record([
                'patient_id' => $subscription->patient_id,
                'subscription_id' => $subscription->id,
                'type' => ExchangeTransaction::TYPE_EXPIRED,
                'quantity' => -$expiredIncluded,
                'reason' => $reason,
            ]);
        }

        if ($includedQuantity > 0) {
            $this->record([
                'patient_id' => $subscription->patient_id,
                'subscription_id' => $subscription->id,
                'type' => ExchangeTransaction::TYPE_RENEWAL,
                'quantity' => $includedQuantity,
                'reason' => $reason,
            ]);
        }
    }

    /**
     * Consume one completed exchange. Idempotent per exchange_id.
     */
    public function recordConsumption(Patient $patient, ?Subscription $subscription, ?Exchange $exchange, string $note): void
    {
        if ($exchange) {
            $already = ExchangeTransaction::query()
                ->where('exchange_id', $exchange->id)
                ->where('type', ExchangeTransaction::TYPE_CONSUMED)
                ->exists();

            if ($already) {
                return;
            }
        }

        $bucket = 'purchased';
        if ($subscription) {
            $included = (int) $subscription->included_exchanges;
            $consumed = (int) $subscription->consumed_exchanges;
            $bucket = $consumed < $included ? 'included' : 'purchased';
            // Old: $subscription->recordConsumedExchange() was called from ExchangeQuotaService.
            $subscription->recordConsumedExchange();
        } elseif ($patient->creditBalance() > 0) {
            Credit::query()->create([
                'patient_id' => $patient->id,
                'amount' => -1,
                'source' => 'consumption',
                'note' => $note,
            ]);
        }

        $this->record([
            'patient_id' => $patient->id,
            'subscription_id' => $subscription?->id,
            'exchange_id' => $exchange?->id,
            'type' => ExchangeTransaction::TYPE_CONSUMED,
            'quantity' => -1,
            'reason' => $note.' ('.$bucket.')',
        ]);
    }

    public function addExchanges(Patient $patient, int $quantity, string $reason, User $admin): ExchangeTransaction
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => __('backend.patients.exchanges_qty_positive'),
            ]);
        }

        return DB::transaction(function () use ($patient, $quantity, $reason, $admin) {
            $subscription = $patient->lifecycleSubscription() ?? $patient->activeSubscription();

            if ($subscription) {
                $subscription->increment('purchased_exchanges', $quantity);
                $subscription->refresh();
                $subscription->forceFill([
                    'exchanges_remaining' => $subscription->remaining_exchanges,
                ])->save();
            } else {
                Credit::query()->create([
                    'patient_id' => $patient->id,
                    'amount' => $quantity,
                    'source' => 'admin_adjustment',
                    'note' => $reason,
                ]);
            }

            $row = $this->record([
                'patient_id' => $patient->id,
                'subscription_id' => $subscription?->id,
                'type' => ExchangeTransaction::TYPE_ADMIN_ADJUSTMENT,
                'quantity' => $quantity,
                'reason' => $reason,
                'admin_id' => $admin->id,
            ]);

            $this->broadcast($patient);

            return $row;
        });
    }

    public function adjustBalance(Patient $patient, int $quantity, string $reason, User $admin): ExchangeTransaction
    {
        if ($quantity === 0) {
            throw ValidationException::withMessages([
                'quantity' => __('backend.patients.exchanges_qty_nonzero'),
            ]);
        }

        if ($quantity > 0) {
            return $this->addExchanges($patient, $quantity, $reason, $admin);
        }

        return DB::transaction(function () use ($patient, $quantity, $reason, $admin) {
            $debit = abs($quantity);
            $subscription = $patient->lifecycleSubscription() ?? $patient->activeSubscription();

            if ($subscription) {
                $remaining = (int) $subscription->remaining_exchanges;
                if ($debit > $remaining) {
                    throw ValidationException::withMessages([
                        'quantity' => __('backend.patients.exchanges_debit_exceeds', ['remaining' => $remaining]),
                    ]);
                }

                $fromPurchased = min($debit, (int) $subscription->purchased_exchanges);
                $leftover = $debit - $fromPurchased;

                if ($fromPurchased > 0) {
                    $subscription->decrement('purchased_exchanges', $fromPurchased);
                }
                if ($leftover > 0) {
                    $subscription->increment('consumed_exchanges', $leftover);
                }

                $subscription->refresh();
                $subscription->forceFill([
                    'exchanges_used' => (int) $subscription->consumed_exchanges,
                    'exchanges_remaining' => $subscription->remaining_exchanges,
                ])->save();
            } else {
                $credits = max(0, (int) $patient->creditBalance());
                if ($debit > $credits) {
                    throw ValidationException::withMessages([
                        'quantity' => __('backend.patients.exchanges_debit_exceeds', ['remaining' => $credits]),
                    ]);
                }

                Credit::query()->create([
                    'patient_id' => $patient->id,
                    'amount' => -$debit,
                    'source' => 'admin_adjustment',
                    'note' => $reason,
                ]);
            }

            $row = $this->record([
                'patient_id' => $patient->id,
                'subscription_id' => $subscription?->id,
                'type' => ExchangeTransaction::TYPE_ADMIN_ADJUSTMENT,
                'quantity' => -$debit,
                'reason' => $reason,
                'admin_id' => $admin->id,
            ]);

            $this->broadcast($patient);

            return $row;
        });
    }

    /**
     * @return array{pending: int, completed_month: int, purchased_month: int, admin_adjustments_month: int}
     */
    public function overviewStats(): array
    {
        $monthStart = now()->startOfMonth();
        $ledgerReady = \Illuminate\Support\Facades\Schema::hasTable('exchange_transactions');
        $exchangesReady = \Illuminate\Support\Facades\Schema::hasTable('exchanges');

        return [
            'pending' => $exchangesReady
                ? Exchange::query()->where('status', Exchange::STATUS_PENDING)->count()
                : 0,
            'completed_month' => $exchangesReady
                ? Exchange::query()
                    ->where('status', Exchange::STATUS_COMPLETED)
                    ->where('completed_at', '>=', $monthStart)
                    ->count()
                : 0,
            'purchased_month' => $ledgerReady
                ? (int) ExchangeTransaction::query()
                    ->where('type', ExchangeTransaction::TYPE_PURCHASED)
                    ->where('created_at', '>=', $monthStart)
                    ->sum('quantity')
                : 0,
            'admin_adjustments_month' => $ledgerReady
                ? ExchangeTransaction::query()
                    ->where('type', ExchangeTransaction::TYPE_ADMIN_ADJUSTMENT)
                    ->where('created_at', '>=', $monthStart)
                    ->count()
                : 0,
        ];
    }

    protected function broadcast(Patient $patient): void
    {
        $patient->loadMissing('user');
        $userId = (int) ($patient->user_id ?: $patient->user?->id);
        if ($userId < 1) {
            return;
        }

        $patient->unsetRelation('subscriptions');
        $subscription = $patient->activeSubscription();
        if ($subscription) {
            event(new ExchangeBalanceUpdated($userId, $subscription->balancePayload()));

            return;
        }

        $credits = max(0, (int) $patient->creditBalance());
        event(new ExchangeBalanceUpdated($userId, [
            'included' => 0,
            'consumed' => 0,
            'purchased' => $credits,
            'remaining' => $credits,
            'total' => $credits,
            'renewal_date' => null,
            'renewal_label' => null,
            'has_subscription' => false,
        ]));
    }
}
