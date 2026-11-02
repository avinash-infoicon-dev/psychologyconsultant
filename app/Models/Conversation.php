<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'patient_id', 'psychologist_id', 'relationship_id', 'status', 'last_message_at'];

    protected static function booted(): void
    {
        static::creating(function (Conversation $conversation) {
            $conversation->ensureUuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function save(array $options = []): bool
    {
        $this->ensureUuid();

        return parent::save($options);
    }

    public function ensureUuid(): void
    {
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(Psychologist::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class);
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function latestMessage(): ?Message
    {
        return $this->messages()->latest()->first();
    }

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(PatientPsychologistRelationship::class, 'relationship_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
