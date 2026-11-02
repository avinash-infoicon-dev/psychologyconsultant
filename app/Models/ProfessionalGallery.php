<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalGallery extends Model
{
    protected $fillable = [
        'professional_id',
        'image',
        'title',
        'description',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://', '//'])) {
            return $this->image;
        }

        if (\Illuminate\Support\Str::startsWith($this->image, 'storage/')) {
            return asset($this->image);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image);
        }

        return asset(ltrim($this->image, '/'));
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }
}