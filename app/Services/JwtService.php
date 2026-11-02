<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class JwtService
{
    public function issueToken(User $user): string
    {
        $now = time();
        $ttlMinutes = $this->ttlMinutes();
        $jti = (string) Str::uuid();

        $payload = [
            'iss' => config('app.url'),
            'iat' => $now,
            'exp' => $now + ($ttlMinutes * 60),
            'sub' => (string) $user->id,
            'jti' => $jti,
        ];

        return $this->encode($payload);
    }

    public function ttlSeconds(): int
    {
        return $this->ttlMinutes() * 60;
    }

    public function userFromToken(string $token): ?User
    {
        $payload = $this->decode($token);

        if (! $payload || ! isset($payload['sub'], $payload['jti'], $payload['iat'])) {
            return null;
        }

        if ($this->isBlacklisted($payload['jti'])) {
            return null;
        }

        if ($this->isRevokedForUser((int) $payload['sub'], (int) $payload['iat'])) {
            return null;
        }

        return User::query()->find($payload['sub']);
    }

    public function invalidate(string $token): void
    {
        $payload = $this->decode($token, checkExpiry: false);

        if (! $payload || ! isset($payload['jti'])) {
            return;
        }

        $ttl = max(1, ($payload['exp'] ?? time()) - time());
        Cache::put($this->blacklistKey($payload['jti']), true, now()->addSeconds($ttl));
    }

    public function revokeAllForUser(int $userId): void
    {
        Cache::put("jwt:revoked_after:{$userId}", time(), now()->addDays(30));
    }

    protected function isRevokedForUser(int $userId, int $issuedAt): bool
    {
        $revokedAfter = Cache::get("jwt:revoked_after:{$userId}");

        return $revokedAfter && $issuedAt < $revokedAfter;
    }

    protected function isBlacklisted(string $jti): bool
    {
        return (bool) Cache::get($this->blacklistKey($jti), false);
    }

    protected function blacklistKey(string $jti): string
    {
        return "jwt:blacklist:{$jti}";
    }

    protected function ttlMinutes(): int
    {
        return (int) config('jwt.ttl', 1440);
    }

    protected function encode(array $payload): string
    {
        $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => config('jwt.algo', 'HS256')]));
        $body = $this->base64UrlEncode(json_encode($payload));
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "{$header}.{$body}", $this->secret(), true)
        );

        return "{$header}.{$body}.{$signature}";
    }

    protected function decode(string $token, bool $checkExpiry = true): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $signature] = $parts;

        $expected = $this->base64UrlEncode(
            hash_hmac('sha256', "{$header}.{$body}", $this->secret(), true)
        );

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($body), true);

        if (! is_array($payload)) {
            return null;
        }

        if ($checkExpiry && isset($payload['exp']) && time() >= $payload['exp']) {
            return null;
        }

        return $payload;
    }

    protected function secret(): string
    {
        $secret = config('jwt.secret');

        if (! $secret) {
            throw new RuntimeException('JWT secret is not configured.');
        }

        if (str_starts_with($secret, 'base64:')) {
            return base64_decode(substr($secret, 7)) ?: $secret;
        }

        return $secret;
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}
