<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\Plan;

class LocalizedNotification
{
    /** @var list<array{key: string, template: string}>|null */
    private static ?array $templates = null;

    public static function title(Notification $notification): string
    {
        return self::line($notification->title, $notification->data['i18n']['title'] ?? null, $notification->data['i18n']['replace'] ?? []);
    }

    public static function body(Notification $notification): string
    {
        return self::line($notification->body, $notification->data['i18n']['body'] ?? null, $notification->data['i18n']['replace'] ?? []);
    }

    /**
     * @param  array<string, mixed>|null  $explicit
     * @param  array<string, mixed>  $replace
     */
    public static function line(?string $stored, ?array $explicit = null, array $replace = []): string
    {
        $stored = (string) $stored;

        if (is_array($explicit) && isset($explicit['key']) && is_string($explicit['key'])) {
            $params = self::localizeParams(array_merge(
                is_array($explicit['replace'] ?? null) ? $explicit['replace'] : [],
                $replace
            ));

            return __($explicit['key'], $params);
        }

        $matched = self::match($stored);
        if ($matched !== null) {
            return __($matched['key'], self::localizeParams($matched['replace']));
        }

        $composite = self::composite($stored);
        if ($composite !== null) {
            return $composite;
        }

        return $stored;
    }

    private static function composite(string $stored): ?string
    {
        foreach (self::templates() as $item) {
            if (str_contains($item['template'], ':')) {
                continue;
            }

            $prefix = $item['template'].' ';
            if (! str_starts_with($stored, $prefix)) {
                continue;
            }

            $rest = substr($stored, strlen($prefix));
            $restMatched = self::match($rest);
            $restText = $restMatched !== null
                ? __($restMatched['key'], self::localizeParams($restMatched['replace']))
                : $rest;

            return __($item['key']).' '.$restText;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $replace
     * @return array<string, mixed>
     */
    private static function localizeParams(array $replace): array
    {
        if (isset($replace['plan']) && is_string($replace['plan'])) {
            $replace['plan'] = Plan::localizeKnownName($replace['plan']);
        }

        return $replace;
    }

    /**
     * @return array{key: string, replace: array<string, string>}|null
     */
    private static function match(string $stored): ?array
    {
        if ($stored === '') {
            return null;
        }

        foreach (self::templates() as $item) {
            $params = self::matchTemplate($stored, $item['template']);
            if ($params !== null) {
                return ['key' => $item['key'], 'replace' => $params];
            }
        }

        return null;
    }

    /**
     * @return array<string, string>|null
     */
    private static function matchTemplate(string $stored, string $template): ?array
    {
        if (! str_contains($template, ':')) {
            return $stored === $template ? [] : null;
        }

        $pattern = preg_quote($template, '/');
        $pattern = preg_replace('/\\\\:([A-Za-z0-9_]+)/', '(?<$1>.+)', $pattern) ?? $pattern;

        if (! preg_match('/^'.$pattern.'$/u', $stored, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * @return list<array{key: string, template: string}>
     */
    private static function templates(): array
    {
        if (self::$templates !== null) {
            return self::$templates;
        }

        $items = [];
        foreach (['en', 'fr_CH', 'fr', 'de', 'it'] as $locale) {
            $group = trans('frontend.notifications', [], $locale);
            if (is_array($group)) {
                self::collect($items, 'frontend.notifications', $group);
            }

            foreach (['accepted', 'rejected', 'request_accepted_notification'] as $key) {
                $full = 'relationship.'.$key;
                $value = trans($full, [], $locale);
                if (is_string($value) && $value !== $full && $value !== '') {
                    $items[] = ['key' => $full, 'template' => $value];
                }
            }
        }

        usort($items, fn (array $a, array $b) => strlen($b['template']) <=> strlen($a['template']));

        return self::$templates = $items;
    }

    /**
     * @param  list<array{key: string, template: string}>  $items
     * @param  array<string, mixed>  $group
     */
    private static function collect(array &$items, string $prefix, array $group): void
    {
        foreach ($group as $key => $value) {
            $full = $prefix.'.'.$key;
            if (is_array($value)) {
                self::collect($items, $full, $value);

                continue;
            }

            if (! is_string($value) || $value === '' || $value === $full) {
                continue;
            }

            $items[] = ['key' => $full, 'template' => $value];
        }
    }
}
