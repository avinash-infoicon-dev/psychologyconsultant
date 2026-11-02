<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeSlider extends Model
{
    use HasCmsTranslations;

    protected $fillable = [
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(HomeSliderTranslation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getSubtitleAttribute(): ?string
    {
        return $this->translate()?->subtitle;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->translate()?->title;
    }

    public function getButtonTextAttribute(): ?string
    {
        return $this->translate()?->button_text;
    }

    public function getButtonUrlAttribute(): ?string
    {
        return $this->translate()?->button_url;
    }

    public function getImageUrlAttribute(): ?string
    {
        $current = $this->translate();
        if ($current?->image_url) {
            return $current->image_url;
        }

        if ($this->relationLoaded('translations')) {
            foreach ($this->translations as $translation) {
                if ($translation->image_url) {
                    return $translation->image_url;
                }
            }
        }

        return null;
    }
}
