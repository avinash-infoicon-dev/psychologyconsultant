<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to FCM's HTTP v1 API directly. This avoids pulling in the
 * kreait/firebase-php SDK as a hard dependency just for push — the
 * service-account OAuth2 flow is only ~30 lines using PHP's built-in
 * openssl functions plus Laravel's Http client.
 *
 * If your project already uses the Firebase SDK elsewhere, swap this
 * implementation out; the public sendToDevice()/sendToTokens() surface
 * is what NotificationService and the FcmChannel depend on.
 */
class FcmService
{
    protected ?array $credentials = null;

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_filter($tokens);

        if (empty($tokens)) {
            return [];
        }

        $accessToken = $this->getAccessToken();
        $projectId = config('notification.push.fcm.project_id');

        if (! $accessToken || ! $projectId) {
            Log::warning('FCM not configured — skipping push notification.', ['title' => $title]);

            return [];
        }

        $results = [];

        foreach ($tokens as $token) {
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => ['title' => $title, 'body' => $body],
                        'data' => array_map('strval', $data),
                    ],
                ]);

            $results[$token] = $response->successful();

            if (! $response->successful()) {
                Log::warning('FCM push failed', ['token' => $token, 'response' => $response->json()]);
            }
        }

        return $results;
    }

    protected function getAccessToken(): ?string
    {
        return Cache::remember('fcm:access_token', now()->addMinutes(50), function () {
            $credentials = $this->loadCredentials();

            if (! $credentials) {
                return null;
            }

            $jwt = $this->buildSignedJwt($credentials);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            return $response->successful() ? $response->json('access_token') : null;
        });
    }

    protected function loadCredentials(): ?array
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $path = config('notification.push.fcm.credentials_path');

        if (! $path || ! is_readable($path)) {
            return null;
        }

        return $this->credentials = json_decode(file_get_contents($path), true);
    }

    protected function buildSignedJwt(array $credentials): string
    {
        $now = time();

        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));

        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signingInput = "{$header}.{$claims}";

        openssl_sign($signingInput, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');

        return $signingInput.'.'.$this->base64UrlEncode($signature);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
