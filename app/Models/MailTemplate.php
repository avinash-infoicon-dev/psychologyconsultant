<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'locale',
        'name',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function findFor(string $slug, ?string $locale = null): ?self
    {
        $locale = $locale ?: config('app.locale');

        $template = static::query()
            ->active()
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->first();

        if ($template) {
            return $template;
        }

        return static::query()
            ->active()
            ->where('slug', $slug)
            ->where('locale', config('app.fallback_locale', 'en'))
            ->first()
            ?? static::query()->active()->where('slug', $slug)->first();
    }

    public function render(array $variables = []): array
    {
        $replace = [];
        foreach ($variables as $key => $value) {
            $replace['{{'.$key.'}}'] = (string) $value;
            $replace['{'.$key.'}'] = (string) $value;
        }

        return [
            'subject' => strtr($this->subject, $replace),
            'body' => strtr($this->body, $replace),
        ];
    }
}
