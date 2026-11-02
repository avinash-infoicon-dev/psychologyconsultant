<?php

namespace App\Support;

use Carbon\CarbonInterface;

class AbsenceReason
{
    /** @var list<string> */
    public const KEYS = ['vacation', 'sick', 'training', 'conference', 'personal', 'other'];

    public static function label(?string $reason): string
    {
        if ($reason === null || trim($reason) === '') {
            return __('frontend.absences.no_reason');
        }

        $key = 'frontend.absences.reasons.'.$reason;
        $translated = __($key);

        return $translated === $key ? $reason : $translated;
    }

    public static function format(?CarbonInterface $at, string $pattern = 'd M Y H:i'): string
    {
        if ($at === null) {
            return '';
        }

        $locale = app()->getLocale();
        $carbon = match (true) {
            str_starts_with($locale, 'fr') => 'fr',
            str_starts_with($locale, 'de') => 'de',
            str_starts_with($locale, 'it') => 'it',
            default => 'en',
        };

        return $at->locale($carbon)->translatedFormat($pattern);
    }
}
