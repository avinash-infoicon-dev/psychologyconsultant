<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicMedia
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            // return $path;
            return self::sameOriginPath($path);
        }

        if (Str::startsWith($path, '/')) {
            return $path;
        }

        if (Str::startsWith($path, ['frontendassets/', 'assets/', 'commonassets/'])) {
            // return asset($path);
            return self::sameOriginPath((string) asset($path));
        }

        $relative = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : ltrim($path, '/');

        // return Storage::disk('public')->url($relative);
        return self::sameOriginPath(Storage::disk('public')->url($relative));
    }

    /**
     * Use path-only URLs so CSS backgrounds follow the page scheme (http/https).
     * Absolute APP_URL links (http://host/storage/...) are blocked as mixed content
     * when the site is opened over https.
     */
    protected static function sameOriginPath(string $url): string
    {
        if (Str::startsWith($url, '//')) {
            $url = 'https:'.$url;
        }

        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['path'])) {
            return $url;
        }

        $path = $parts['path'];
        if (! empty($parts['query'])) {
            $path .= '?'.$parts['query'];
        }

        return $path;
    }

    /**
     * Theme/app asset as a same-origin path (avoids https pages blocking http APP_URL files).
     */
    public static function assetHref(string $relativePath): string
    {
        return self::sameOriginPath((string) asset(ltrim($relativePath, '/')));
    }

    public static function versionedAssetHref(string $relativePath): string
    {
        $full = public_path($relativePath);
        $version = is_file($full) ? filemtime($full) : time();

        return self::assetHref($relativePath).'?v='.$version;
    }

    public static function aboutDefault(string $filename): string
    {
        return 'default/about/'.$filename;
    }

    /**
     * Copy bundled About images into storage/app/public/default/about when missing.
     */
    public static function publishAboutDefaults(): void
    {
        $map = [
            'hero-bg.webp' => public_path('frontendassets/assets/img/hero/hero-bg.webp'),
            'about-shape-1.webp' => public_path('frontendassets/assets/img/about/about-shape-1.webp'),
            'video-thumb-1.webp' => public_path('frontendassets/assets/img/video/video-thumb-1.webp'),
            'thumb-1.webp' => public_path('frontendassets/assets/img/about/thumb-1.webp'),
            'cta-thumb-1.webp' => public_path('frontendassets/assets/img/thumb-1.webp'),
        ];

        foreach ($map as $name => $source) {
            $dest = self::aboutDefault($name);
            if (! is_file($source) || Storage::disk('public')->exists($dest)) {
                continue;
            }

            Storage::disk('public')->put($dest, (string) file_get_contents($source));
        }
    }

    public static function isManaged(?string $path, string $prefix): bool
    {
        return is_string($path) && $path !== '' && Str::startsWith($path, $prefix);
    }
}
