<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $from = $request->filled('from') ? \Carbon\Carbon::parse($request->string('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? \Carbon\Carbon::parse($request->string('to')) : now();

        $payments = Payment::where('status', 'succeeded')->whereBetween('paid_at', [$from, $to]);

        return response()->json([
            'data' => [
                'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
                'revenue' => (int) (clone $payments)->sum('amount'),
                'refunded' => (int) (clone $payments)->sum('refunded_amount'),
                'payment_count' => (clone $payments)->count(),
                'new_subscriptions' => Subscription::whereBetween('created_at', [$from, $to])->count(),
                'cancelled_subscriptions' => Subscription::where('status', 'cancelled')
                    ->whereBetween('cancelled_at', [$from, $to])->count(),
                'by_gateway' => (clone $payments)->selectRaw('gateway, count(*) as count, sum(amount) as total')
                    ->groupBy('gateway')->get(),
            ],
        ]);
    }

    /** Streamed CSV so large exports don't blow up memory. */
    public function export(Request $request): StreamedResponse
    {
        $from = $request->filled('from') ? \Carbon\Carbon::parse($request->string('from')) : now()->startOfMonth();
        $to = $request->filled('to') ? \Carbon\Carbon::parse($request->string('to')) : now();

        return response()->streamDownload(function () use ($from, $to) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Amount', 'Currency', 'Gateway', 'Status', 'Paid At']);

            Payment::with('user')
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at')
                ->chunk(500, function ($payments) use ($handle) {
                    foreach ($payments as $payment) {
                        fputcsv($handle, [
                            $payment->id,
                            $payment->user->email,
                            $payment->amount,
                            $payment->currency,
                            $payment->gateway,
                            $payment->status,
                            $payment->paid_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, 'payments_'.$from->toDateString().'_to_'.$to->toDateString().'.csv');
    }
}
