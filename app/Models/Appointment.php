<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const TYPE_ONLINE = 'online';

    public const TYPE_OFFLINE = 'offline';

    public const DURATIONS = [30, 45, 60];

    protected $fillable = [
        'professional_id',
        'patient_id',
        'relationship_id',
        'client_name',
        'client_email',
        'client_phone',
        'service_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'requested_by',
        'cancelled_by',
        'consultation_type',
        'status',
        'notes',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'duration_minutes' => 'integer',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(PatientPsychologistRelationship::class, 'relationship_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function startsAt(): Carbon
    {
        $date = $this->appointment_date instanceof Carbon
            ? $this->appointment_date->format('Y-m-d')
            : (string) $this->appointment_date;
        $time = substr((string) $this->appointment_time, 0, 8);

        return Carbon::parse(trim($date.' '.$time));
    }

    public function endsAt(): Carbon
    {
        return $this->startsAt()->copy()->addMinutes($this->duration_minutes ?: 50);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true);
    }

    public function isUpcoming(): bool
    {
        return $this->isOpen() && $this->endsAt()->isFuture();
    }
}
