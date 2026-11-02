<?php

namespace App\Services\Payment;

use App\Models\Payment;
use RuntimeException;

class RefundService
{
    public function __construct(
        protected StripeService $stripe,
        protected TwintService $twint,
    ) {}

    public function refund(Payment $payment, ?int $amount = null): Payment
    {
        if (! $payment->isRefundable()) {
            throw new RuntimeException('This payment has already been fully refunded or was never successful.');
        }

        $amount ??= $payment->amount - $payment->refunded_amount;

        match ($payment->gateway) {
            'stripe' => $this->stripe->refund($payment->gateway_transaction_id, $amount),
            'twint' => $this->twint->refund($payment->gateway_transaction_id, $amount),
            default => throw new RuntimeException("Unknown gateway: {$payment->gateway}"),
        };

        $newRefundedAmount = $payment->refunded_amount + $amount;

        $payment->update([
            'refunded_amount' => $newRefundedAmount,
            'status' => $newRefundedAmount >= $payment->amount ? 'refunded' : 'partially_refunded',
        ]);

        if ($payment->invoice) {
            $payment->invoice->update(['status' => 'void']);
        }

        return $payment->fresh();
    }
}
