<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeTransaction extends Model
{
    public const TYPE_INCLUDED = 'included';

    public const TYPE_CONSUMED = 'consumed';

    public const TYPE_PURCHASED = 'purchased';

    public const TYPE_ADMIN_ADJUSTMENT = 'admin_adjustment';

    public const TYPE_REFUND = 'refund';

    public const TYPE_EXPIRED = 'expired';

    public const TYPE_RENEWAL = 'renewal';

    public const TYPES = [
        self::TYPE_INCLUDED,
        self::TYPE_CONSUMED,
        self::TYPE_PURCHASED,
        self::TYPE_ADMIN_ADJUSTMENT,
        self::TYPE_REFUND,
        self::TYPE_EXPIRED,
        self::TYPE_RENEWAL,
    ];

    protected $fillable = [
        'patient_id',
        'subscription_id',
        'exchange_id',
        'type',
        'quantity',
        'reason',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
