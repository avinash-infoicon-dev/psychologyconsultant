<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'psychologist_id', 'date_of_birth', 'gender', 'address',
        'city', 'country', 'emergency_contact_name', 'emergency_contact_phone',
        'preferences',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'preferences' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        // Old (status only):
        // return $this->subscriptions()->where('status', 'active')->latest()->first();

        $subscription = $this->subscriptions()
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $subscription) {
            return null;
        }

        // Period ended → treat as expired even if Adminer status was left as "active".
        if ($subscription->current_period_ends_at && $subscription->current_period_ends_at->isPast()) {
            $subscription->update([
                'status' => 'expired',
                'ends_at' => $subscription->ends_at ?? $subscription->current_period_ends_at,
            ]);

            return null;
        }

        return $subscription;
    }

    /**
     * Current billing relationship: active, trial, or past due.
     * Messaging still uses activeSubscription(); this is for the patient
     * subscription page and dummy payment recovery.
     */
    public function lifecycleSubscription(): ?Subscription
    {
        $subscription = $this->subscriptions()
            ->whereIn('status', [
                Subscription::STATUS_TRIALING,
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_PAST_DUE,
            ])
            ->latest()
            ->first();

        if (! $subscription) {
            return null;
        }

        if ($subscription->current_period_ends_at && $subscription->current_period_ends_at->isPast()) {
            $subscription->update([
                'status' => Subscription::STATUS_EXPIRED,
                'ends_at' => $subscription->ends_at ?? $subscription->current_period_ends_at,
            ]);

            return null;
        }

        return $subscription;
    }

    public function latestSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->with(['plan.translations', 'pendingPlan.translations'])
            ->latest()
            ->first();
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class);
    }

    public function exchangeTransactions(): HasMany
    {
        return $this->hasMany(ExchangeTransaction::class);
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function creditBalance(): int
    {
        return (int) $this->credits()->sum('amount');
    }
}
