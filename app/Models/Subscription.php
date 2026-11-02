<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    public const STATUS_TRIALING = 'trialing';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAST_DUE = 'past_due';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'patient_id', 'plan_id', 'plan_snapshot', 'pending_plan_id', 'status', 'starts_at', 'ends_at',
        'cancelled_at', 'current_period_starts_at', 'current_period_ends_at',
        'stripe_subscription_id', 'twint_subscription_id',
        'exchanges_used', 'exchanges_remaining',
        'included_exchanges', 'consumed_exchanges', 'purchased_exchanges',
        'auto_renew', 'cancel_at_period_end',
        'last_payment_failed_at', 'last_payment_error',
    ];

    protected function casts(): array
    {
        return [
            'plan_snapshot' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'last_payment_failed_at' => 'datetime',
            'auto_renew' => 'boolean',
            'cancel_at_period_end' => 'boolean',
        ];
    }

    public function getPlanSnapshotModel(): ?Plan
    {
        if (! empty($this->plan_snapshot) && is_array($this->plan_snapshot)) {
            return (new Plan)->forceFill($this->plan_snapshot);
        }

        return $this->plan;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function pendingPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'pending_plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class);
    }

    public function exchangeTransactions(): HasMany
    {
        return $this->hasMany(ExchangeTransaction::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->current_period_ends_at && $this->current_period_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function hasExchangesRemaining(): bool
    {
        return $this->isActive() && $this->remaining_exchanges > 0;
        // Legacy stored column:
        // return $this->isActive() && $this->exchanges_remaining > 0;
    }

    /**
     * remaining = included + purchased - consumed.
     */
    public function getRemainingExchangesAttribute(): int
    {
        return max(
            0,
            (int) $this->included_exchanges
            + (int) $this->purchased_exchanges
            - (int) $this->consumed_exchanges
        );
    }

    /**
     * @return array{
     *     included: int,
     *     consumed: int,
     *     purchased: int,
     *     remaining: int,
     *     total: int,
     *     renewal_date: ?string,
     *     renewal_label: ?string,
     *     has_subscription: bool
     * }
     */
    public function balancePayload(): array
    {
        $included = (int) $this->included_exchanges;
        $purchased = (int) $this->purchased_exchanges;

        return [
            'included' => $included,
            'consumed' => (int) $this->consumed_exchanges,
            'purchased' => $purchased,
            'remaining' => $this->remaining_exchanges,
            'total' => $included + $purchased,
            'renewal_date' => $this->current_period_ends_at?->toDateString(),
            'renewal_label' => $this->current_period_ends_at?->format('d M Y'),
            'has_subscription' => true,
        ];
    }

    public function consumeExchange(): void
    {
        // Legacy: consume on the patient send. Kept for older callers.
        $this->recordConsumedExchange();
        // $this->decrement('exchanges_remaining');
        // $this->increment('exchanges_used');
    }

    /**
     * Consume one completed exchange (psychologist response). Idempotent callers
     * must only invoke this after marking the exchange row completed.
     */
    public function recordConsumedExchange(): void
    {
        $this->increment('consumed_exchanges');
        $this->refresh();
        $this->forceFill([
            'exchanges_used' => (int) $this->consumed_exchanges,
            'exchanges_remaining' => $this->remaining_exchanges,
        ])->save();
    }

    public function isPastDue(): bool
    {
        return $this->status === self::STATUS_PAST_DUE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function allowsLifecycleActions(): bool
    {
        return in_array($this->status, [self::STATUS_TRIALING, self::STATUS_ACTIVE, self::STATUS_PAST_DUE], true);
    }

    public function hasPaymentIssue(): bool
    {
        return $this->isPastDue() || $this->last_payment_failed_at !== null;
    }

    public function statusLabel(): string
    {
        return __('frontend.subscription.statuses.'.$this->status);
    }

    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE, self::STATUS_TRIALING => 'success',
            self::STATUS_PAST_DUE => 'warning',
            default => 'muted',
        };
    }

    /** upgrade | downgrade | change */
    public function changeDirection(Plan $plan): string
    {
        $current = (int) ($this->plan?->price ?? 0);
        $next = (int) $plan->price;

        if ($next > $current) {
            return 'upgrade';
        }

        if ($next < $current) {
            return 'downgrade';
        }

        return 'change';
    }
}
