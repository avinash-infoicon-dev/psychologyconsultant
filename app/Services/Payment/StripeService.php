<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Calls Stripe's REST API directly rather than requiring the stripe-php
 * SDK. Stripe's API is plain form-encoded REST with predictable
 * resource shapes, so this is a thin, dependency-free wrapper around
 * the handful of endpoints this platform actually needs. If your team
 * prefers the official SDK, swap the internals of these methods —
 * PaymentService/SubscriptionService only depend on this class's
 * public method signatures, not on how it talks to Stripe.
 */
class StripeService
{
    protected string $baseUrl = 'https://api.stripe.com/v1';
     //protected string $baseUrl = 'https://api.stripe.com/v2';

    protected function client()
    {
        // Publishable pk_ keys cannot create products, prices, or subscriptions.
        // return Http::withToken(config('payment.stripe.key'))->asForm()->baseUrl($this->baseUrl);
        // return Http::withToken(config('payment.stripe.secret') ?: config('payment.stripe.key'))
        return Http::withToken($this->secretKey())
            ->asForm()
            ->baseUrl($this->baseUrl);
    }

    /** Secret key is sk_… — accept it from either STRIPE_SECRET or STRIPE_KEY. */
    protected function secretKey(): string
    {
        $candidates = [
            (string) config('payment.stripe.secret'),
            (string) config('payment.stripe.key'),
        ];

        foreach ($candidates as $candidate) {
            $value = trim($candidate, " \t\n\r\0\x0B\"'");
            if (str_starts_with($value, 'sk_')) {
                return $value;
            }
        }

        return trim((string) ($candidates[0] ?: $candidates[1]), " \t\n\r\0\x0B\"'");
    }

    /** Create a PaymentIntent for a one-off or first subscription charge. */
    public function createPaymentIntent(int $amount, string $currency, ?string $customerId = null, array $metadata = []): array
    {
        $payload = [
            'amount' => $amount,
            'currency' => strtolower($currency),
            'customer' => $customerId,
            'automatic_payment_methods[enabled]' => 'true',
        ];

        foreach ($metadata as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $payload["metadata[{$key}]"] = (string) $value;
        }

        $response = $this->client()->post('/payment_intents', array_filter($payload));

        return $this->handle($response);
    }

    /**
     * Hosted Checkout Session (card and/or TWINT). Used by the patient
     * website checkout so we do not collect card details on our pages.
     */
    public function createCheckoutSession(array $params): array
    {
        $payload = [
            'mode' => $params['mode'] ?? 'payment',
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
            'client_reference_id' => $params['client_reference_id'] ?? null,
            'customer_email' => $params['customer_email'] ?? null,
        ];

        foreach ($params['payment_method_types'] ?? [] as $index => $type) {
            $payload["payment_method_types[{$index}]"] = $type;
        }

        foreach ($params['metadata'] ?? [] as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $string = (string) $value;
            $payload["metadata[{$key}]"] = $string;
            if (($params['mode'] ?? 'payment') === 'payment') {
                $payload["payment_intent_data[metadata][{$key}]"] = $string;
            }
        }

        if (! empty($params['price_id'])) {
            $payload['line_items[0][price]'] = $params['price_id'];
            $payload['line_items[0][quantity]'] = 1;
        } else {
            $payload['line_items[0][quantity]'] = 1;
            $payload['line_items[0][price_data][currency]'] = strtolower($params['currency'] ?? 'chf');
            $payload['line_items[0][price_data][unit_amount]'] = (int) $params['amount'];
            $payload['line_items[0][price_data][product_data][name]'] = $params['product_name'] ?? 'Plan';
            if (($params['mode'] ?? 'payment') === 'subscription') {
                $payload['line_items[0][price_data][recurring][interval]'] = $params['interval'] ?? 'month';
            }
        }

        $response = $this->client()->post('/checkout/sessions', array_filter($payload));
        return $this->handle($response);
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->client()->get("/checkout/sessions/{$sessionId}", [
            'expand[0]' => 'payment_intent',
            'expand[1]' => 'subscription',
        ]);

        return $this->handle($response);
    }

    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        $response = $this->client()->get("/payment_intents/{$paymentIntentId}");

        return $this->handle($response);
    }

    public function confirmPaymentIntent(string $paymentIntentId, ?string $paymentMethodId = null): array
    {
        $response = $this->client()->post("/payment_intents/{$paymentIntentId}/confirm", array_filter([
            'payment_method' => $paymentMethodId,
        ]));

        return $this->handle($response);
    }

    /** Stripe Product used as the catalogue “plan”. */
    public function createProduct(string $name, ?string $description = null, array $metadata = []): array
    {
        $payload = ['name' => $name];

        if ($description) {
            $payload['description'] = $description;
        }

        foreach ($metadata as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $payload["metadata[{$key}]"] = (string) $value;
        }

        return $this->handle($this->client()->post('/products', $payload));
    }

    /**
     * Recurring price for subscriptions, or a one-off price for
     * on-demand / additional-exchange packs.
     */
    public function createPrice(string $productId, int $unitAmount, string $currency, ?string $interval = null, int $intervalCount = 1): array
    {
        $payload = [
            'product' => $productId,
            'unit_amount' => $unitAmount,
            'currency' => strtolower($currency),
        ];

        if ($interval) {
            $payload['recurring[interval]'] = $interval;
            if ($intervalCount > 1) {
                $payload['recurring[interval_count]'] = $intervalCount;
            }
        }

        return $this->handle($this->client()->post('/prices', $payload));
    }

    /**
     * @return array{plan_id: string, stripe_price_id: string}
     */
    public function createCatalogPlan(string $name, int $unitAmount, string $currency, string $billingInterval, ?string $description = null, array $metadata = []): array
    {
        $product = $this->createProduct($name, $description, $metadata);

        [$interval, $intervalCount] = match ($billingInterval) {
            'yearly' => ['year', 1],
            'quarterly' => ['month', 3],
            'monthly' => ['month', 1],
            default => [null, 1],
        };

        $price = $this->createPrice(
            $product['id'],
            $unitAmount,
            $currency,
            $interval,
            $intervalCount,
        );

        return [
            'plan_id' => $product['id'],
            'stripe_price_id' => $price['id'],
        ];
    }

    public function createCustomer(string $email, string $name): array
    {
        $response = $this->client()->post('/customers', [
            'email' => $email,
            'name' => $name,
        ]);

        return $this->handle($response);
    }

    public function createSubscription(string $customerId, string $priceId, array $metadata = []): array
    {
        $payload = [
            'customer' => $customerId,
            'items[0][price]' => $priceId,
        ];

        foreach ($metadata as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $payload["metadata[{$key}]"] = (string) $value;
        }

        $response = $this->client()->post('/subscriptions', array_filter($payload));

        return $this->handle($response);
    }

    public function retrieveSubscription(string $subscriptionId): array
    {
        $response = $this->client()->get("/subscriptions/{$subscriptionId}");

        return $this->handle($response);
    }

    /** Update cancel_at_period_end (disable / re-enable automatic renewal). */
    public function updateSubscription(string $subscriptionId, array $params): array
    {
        $payload = [];

        if (array_key_exists('cancel_at_period_end', $params)) {
            $payload['cancel_at_period_end'] = ! empty($params['cancel_at_period_end']) ? 'true' : 'false';
        }

        $response = $this->client()->post("/subscriptions/{$subscriptionId}", $payload);

        return $this->handle($response);
    }

    public function cancelSubscription(string $subscriptionId): array
    {
        $response = $this->client()->delete("/subscriptions/{$subscriptionId}");

        return $this->handle($response);
    }

    public function refund(string $paymentIntentId, ?int $amount = null): array
    {
        $response = $this->client()->post('/refunds', array_filter([
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
        ]));

        return $this->handle($response);
    }

    /**
     * Verifies the `Stripe-Signature` header per Stripe's documented
     * scheme (HMAC-SHA256 over "{timestamp}.{payload}") without needing
     * the SDK's Webhook::constructEvent helper.
     */
    public function verifyWebhookSignature(string $payload, string $signatureHeader): bool
    {
        $secret = trim((string) config('payment.stripe.webhook_secret'), " \t\n\r\0\x0B\"'");

        if (! $secret) {
            return false;
        }

        $parts = collect(explode(',', $signatureHeader))
            ->mapWithKeys(function ($part) {
                [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

                return [$key => $value];
            });

        $timestamp = $parts->get('t');
        $signature = $parts->get('v1');

        if (! $timestamp || ! $signature) {
            return false;
        }

        // Reject replayed webhooks older than the configured tolerance window.
        if (abs(time() - (int) $timestamp) > config('payment.webhook_tolerance', 300)) {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);

        return hash_equals($expected, $signature);
    }

    protected function handle($response): array
    {
        if (! $response->successful()) {
            throw new RuntimeException(
                'Stripe API error: '.($response->json('error.message') ?? $response->status())
            );
        }

        return $response->json();
    }
}