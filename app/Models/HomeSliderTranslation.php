<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeSliderTranslation extends Model
{
    protected $fillable = [
        'home_slider_id',
        'locale',
        'subtitle',
        'title',
        'button_text',
        'button_url',
        'image_path',
        'image_original_name',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(HomeSlider::class, 'home_slider_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, ['frontendassets/', 'assets/', 'commonassets/'])) {
            return asset($path);
        }

        $relative = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : ltrim($path, '/');

        return Storage::disk('public')->url($relative);
    }
}
