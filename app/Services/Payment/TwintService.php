<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Twint doesn't publish a PHP SDK, so this talks to their REST API
 * directly. The exact endpoint shapes below follow Twint's merchant
 * API pattern (pairing token -> monitor status -> capture); confirm
 * field names against your acquiring bank's Twint integration docs
 * before going live, since some details vary by acquirer.
 */
class TwintService
{
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->authToken(),
            'Content-Type' => 'application/json',
        ])
            ->timeout(config('twint.timeout', 15))
            ->baseUrl(config('twint.base_url'));
    }

    protected function authToken(): string
    {
        // Twint merchant APIs commonly use a signed API-key/secret pair
        // rather than OAuth; adjust this to match your acquirer's actual
        // auth scheme (some use HMAC request signing instead of a bearer
        // token — this is the simplest common case).
        return config('twint.api_key');
    }

    /** Initiate a payment — returns a pairing/QR token the client displays to the payer. */
    public function initiatePayment(int $amountRappen, string $currency, string $reference, array $metadata = []): array
    {
        $response = $this->client()->post('/v3/payments', [
            'merchantId' => config('twint.merchant_id'),
            'amount' => $amountRappen,
            'currency' => strtoupper($currency),
            'reference' => $reference,
            'callbackUrl' => config('twint.callback_url'),
            'metadata' => $metadata,
        ]);

        return $this->handle($response);
    }

    public function getPaymentStatus(string $twintTransactionId): array
    {
        $response = $this->client()->get("/v3/payments/{$twintTransactionId}");

        return $this->handle($response);
    }

    public function refund(string $twintTransactionId, ?int $amountRappen = null): array
    {
        $response = $this->client()->post("/v3/payments/{$twintTransactionId}/refund", array_filter([
            'amount' => $amountRappen,
        ]));

        return $this->handle($response);
    }

    /**
     * Twint webhook signatures are typically HMAC-SHA256 over the raw
     * body using the merchant API secret. Adjust the header name/scheme
     * to match your acquirer's actual documentation before production use.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        $secret = config('twint.api_secret');

        if (! $secret) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signatureHeader);
    }

    protected function handle($response): array
    {
        if (! $response->successful()) {
            throw new RuntimeException(
                'Twint API error: '.($response->json('message') ?? $response->status())
            );
        }

        return $response->json();
    }
}
