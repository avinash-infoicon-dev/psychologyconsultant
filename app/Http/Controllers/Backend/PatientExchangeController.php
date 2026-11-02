<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\Messaging\ExchangeLedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PatientExchangeController extends Controller
{
    public function __construct(protected ExchangeLedgerService $ledger) {}

    public function show(Patient $patient): View
    {
        $patient->load(['user', 'credits' => fn ($q) => $q->latest()->limit(30)]);

        $subscription = $patient->lifecycleSubscription() ?? $patient->latestSubscription();
        $subscription?->loadMissing('plan');

        $credits = max(0, (int) $patient->creditBalance());
        $balance = $subscription?->balancePayload() ?? [
            'included' => 0,
            'consumed' => 0,
            'purchased' => $credits,
            'remaining' => $credits,
            'total' => $credits,
            'renewal_date' => null,
            'renewal_label' => null,
            'has_subscription' => false,
        ];

        $transactions = $patient->exchangeTransactions()
            ->with('admin')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $exchanges = $patient->exchanges()
            ->with(['psychologist.user', 'conversation'])
            ->latest('id')
            ->limit(50)
            ->get();

        return view('backend.patients.exchanges', [
            'patient' => $patient,
            'subscription' => $subscription,
            'balance' => $balance,
            'transactions' => $transactions,
            'exchanges' => $exchanges,
        ]);
    }

    public function add(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validateWithBag('add', [
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->ledger->addExchanges(
                $patient,
                (int) $validated['quantity'],
                $validated['reason'],
                $request->user(),
            );
        } catch (ValidationException $e) {
            throw $e->errorBag('add');
        }

        return back()->with('success', __('backend.patients.exchanges_added'));
    }

    public function adjust(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validateWithBag('adjust', [
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->ledger->adjustBalance(
                $patient,
                (int) $validated['quantity'],
                $validated['reason'],
                $request->user(),
            );
        } catch (ValidationException $e) {
            throw $e->errorBag('adjust');
        }

        return back()->with('success', __('backend.patients.exchanges_adjusted'));
    }
}
