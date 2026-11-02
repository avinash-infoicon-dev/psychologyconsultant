<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentController extends Controller
{
    public function __construct(protected RefundService $refunds) {}

    public function index(Request $request): JsonResponse
    {
        $query = Payment::query()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->string('gateway'));
        }

        $payments = $query->latest()->paginate(30);

        return response()->json([
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    public function show(Payment $payment): JsonResponse
    {
        return response()->json(['data' => $payment->load('user', 'invoice', 'subscription')]);
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $payment = $this->refunds->refund($payment, $data['amount'] ?? null);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['data' => $payment]);
    }
}
