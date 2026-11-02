<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    protected $fillable = [
        'professional_id', 'starts_at', 'ends_at', 'reason', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function isCurrent(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        return $this->starts_at <= $now && $this->ends_at >= $now;
    }
}
