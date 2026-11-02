<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProfessionalDocument extends Model
{
    protected $fillable = [
        'professional_id',
        'title',
        'document',
        'document_type',
        'is_verified',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->document) {
            return null;
        }

        if (Str::startsWith($this->document, ['http://', 'https://', '//'])) {
            return $this->document;
        }

        return asset(ltrim($this->document, '/'));
    }
}
