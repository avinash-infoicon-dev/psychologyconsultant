<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Invoice;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $payments) {}

    public function createIntent(PaymentRequest $request): JsonResponse
    {
        $invoice = null;

        if ($request->filled('invoice_id')) {
            $invoice = Invoice::findOrFail($request->integer('invoice_id'));
            abort_unless($invoice->user_id === $request->user()->id, 403);
        }

        $result = $this->payments->createIntent(
            $request->user(),
            $request->integer('amount'),
            $request->input('currency', config('payment.currency', 'CHF')),
            $request->string('gateway'),
            $invoice,
        );

        return response()->json([
            'payment_id' => $result['payment']->id,
            'client_secret' => $result['client_secret'],
            'pairing_token' => $result['pairing_token'],
        ], 201);
    }

    public function confirm(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_id' => ['required', 'integer'],
            'payment_method_id' => ['nullable', 'string'],
        ]);

        $payment = $request->user()->payments()->findOrFail($data['payment_id']);

        $payment = $this->payments->confirm($payment, $data['payment_method_id'] ?? null);

        return response()->json([
            'status' => $payment->status,
            'paid_at' => $payment->paid_at?->toIso8601String(),
        ]);
    }
}
