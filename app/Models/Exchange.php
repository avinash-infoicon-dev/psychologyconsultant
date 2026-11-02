<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exchange extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'conversation_id',
        'patient_id',
        'psychologist_id',
        'subscription_id',
        'patient_message_id',
        'psychologist_message_id',
        'status',
        'patient_message_limit',
        'psychologist_message_limit',
        'patient_messages_used',
        'psychologist_messages_used',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'patient_messages_used' => 'integer',
            'psychologist_messages_used' => 'integer',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class, 'psychologist_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function patientMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'patient_message_id');
    }

    public function psychologistMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'psychologist_message_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ExchangeTransaction::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
