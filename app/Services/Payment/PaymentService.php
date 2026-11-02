<?php

namespace App\Services\Payment;

use App\Events\PaymentCompleted;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected StripeService $stripe,
        protected TwintService $twint,
    ) {}

    /** Start a payment — e.g. paying an open invoice, or a Twint QR-code purchase. */
    public function createIntent(User $user, int $amount, string $currency, string $gateway, ?Invoice $invoice = null): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice?->id,
            'amount' => $amount,
            'currency' => $currency,
            'gateway' => $gateway,
            'status' => 'pending',
        ]);

        $gatewayResponse = match ($gateway) {
            'stripe' => $this->stripe->createPaymentIntent($amount, $currency, metadata: ['payment_id' => $payment->id]),
            'twint' => $this->twint->initiatePayment($amount, $currency, (string) $payment->id),
            default => throw new RuntimeException("Unknown gateway: {$gateway}"),
        };

        $payment->update([
            'gateway_transaction_id' => $gatewayResponse['id'] ?? $gatewayResponse['transactionId'] ?? null,
            'gateway_response' => $gatewayResponse,
        ]);

        return [
            'payment' => $payment,
            'client_secret' => $gatewayResponse['client_secret'] ?? null, // Stripe
            'pairing_token' => $gatewayResponse['token'] ?? null,        // Twint QR/pairing
        ];
    }

    public function confirm(Payment $payment, ?string $paymentMethodId = null): Payment
    {
        $result = match ($payment->gateway) {
            'stripe' => $this->stripe->confirmPaymentIntent($payment->gateway_transaction_id, $paymentMethodId),
            'twint' => $this->twint->getPaymentStatus($payment->gateway_transaction_id),
            default => throw new RuntimeException("Unknown gateway: {$payment->gateway}"),
        };

        $status = $result['status'] ?? null;
        $succeeded = in_array($status, ['succeeded', 'SUCCESS', 'settled'], true);

        return DB::transaction(function () use ($payment, $result, $succeeded) {
            $payment->update([
                'status' => $succeeded ? 'succeeded' : 'failed',
                'gateway_response' => $result,
                'paid_at' => $succeeded ? now() : null,
                'failure_reason' => $succeeded ? null : ($result['failure_message'] ?? 'Payment not confirmed'),
            ]);

            if ($succeeded) {
                $payment->invoice?->update(['status' => 'paid', 'paid_at' => now()]);
                event(new PaymentCompleted($payment));
            }

            return $payment->fresh();
        });
    }
}
