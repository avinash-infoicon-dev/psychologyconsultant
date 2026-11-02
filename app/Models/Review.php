<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'professional_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'review',
        'is_approved',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}
