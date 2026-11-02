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

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', $fallback)
                ?? $this->translations->first();
        }

        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', $fallback)->first()
            ?? $this->translations()->first();
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
