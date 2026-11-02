<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCmsTranslations
{
    abstract public function translations(): HasMany;

    public function translate(?string $locale = null): ?object
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        // Prefer app default locale over an arbitrary first row (often leftover de/it).
        // Previous: ... ?? $this->translations->first();
        $preferred = array_values(array_unique(array_filter([
            $locale,
            $fallback,
            (string) config('app.locale'),
            'fr_CH',
            'en',
        ])));

        if ($this->relationLoaded('translations')) {
            foreach ($preferred as $code) {
                $hit = $this->translations->firstWhere('locale', $code);
                if ($hit) {
                    return $hit;
                }
            }

            // return $this->translations->first();
            return null;
        }

        foreach ($preferred as $code) {
            $hit = $this->translations()->where('locale', $code)->first();
            if ($hit) {
                return $hit;
            }
        }

        // return $this->translations()->first();
        return null;
    }

    public function syncTranslations(array $translationsByLocale): void
    {
        foreach ($translationsByLocale as $locale => $attributes) {
            if (! is_array($attributes)) {
                continue;
            }

            $this->translations()->updateOrCreate(
                ['locale' => $locale],
                $attributes
            );
        }
    }
}
