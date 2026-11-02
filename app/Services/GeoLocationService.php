<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Resolves a visitor's IP to a two-letter country code.
 *
 * Default driver is 'ipinfo' — a plain HTTPS call via Laravel's HTTP
 * client, no composer dependency needed. 'maxmind' is also supported
 * for teams who want a local, zero-latency lookup, but that path
 * requires the geoip2/geoip2 package (not installed by this scaffold)
 * plus a GeoLite2 database file; it degrades gracefully — logging a
 * warning and returning null (treated as "unknown", not "blocked") —
 * if that package or database isn't present rather than breaking
 * every request.
 */
class GeoLocationService
{
    public function resolveCountry(string $ip): ?string
    {
        if ($this->isPrivateOrLocal($ip)) {
            return null; // local/dev traffic — nothing to resolve, nothing to block
        }
        return Cache::remember(
            "geo:country:{$ip}",
            now()->addMinutes(config('geolocation.cache_ttl_minutes', 1440)),
            fn () => config('geolocation.driver') === 'maxmind'
                ? $this->resolveViaMaxmind($ip)
                : $this->resolveViaIpinfo($ip)
        );
    }

    protected function resolveViaIpinfo(string $ip): ?string
    {
        $token = config('geolocation.ipinfo.token');

        try {
            $response = Http::timeout(3)->get("https://ipinfo.io/{$ip}/json", array_filter([
                'token' => $token,
            ]));

            return $response->successful() ? $response->json('country') : null;
        } catch (\Throwable $e) {
            Log::warning('IP geolocation lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }

    protected function resolveViaMaxmind(string $ip): ?string
    {
        $dbPath = config('geolocation.maxmind.database_path');

        if (! class_exists(\GeoIp2\Database\Reader::class) || ! is_readable($dbPath)) {
            Log::warning('MaxMind driver selected but geoip2/geoip2 package or database file is missing — falling back to unresolved.');

            return null;
        }

        try {
            $reader = new \GeoIp2\Database\Reader($dbPath);

            return $reader->country($ip)->country->isoCode;
        } catch (\Throwable $e) {
            Log::warning('MaxMind lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }

    protected function isPrivateOrLocal(string $ip): bool
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
