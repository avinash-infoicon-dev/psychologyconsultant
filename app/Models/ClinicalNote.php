<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class ClinicalNote extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'patient_id', 'psychologist_id', 'conversation_id', 'content',
        'is_signed', 'signed_at', 'editable_until',
    ];

    protected function casts(): array
    {
        return [
            // Clinical content — always encrypted at rest.
            'content' => 'encrypted',
            'is_signed' => 'boolean',
            'signed_at' => 'datetime',
            'editable_until' => 'datetime',
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

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isEditable(): bool
    {
        return ! $this->is_signed
            && ($this->editable_until === null || $this->editable_until->isFuture());
    }
}
