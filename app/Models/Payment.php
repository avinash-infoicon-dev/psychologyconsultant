<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subscription_id', 'invoice_id', 'amount', 'currency',
        'gateway', 'gateway_transaction_id', 'status', 'refunded_amount',
        'failure_reason', 'gateway_response', 'metadata', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gateway_response' => 'array',
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function isRefundable(): bool
    {
        return $this->status === 'succeeded' && $this->refunded_amount < $this->amount;
    }
}
