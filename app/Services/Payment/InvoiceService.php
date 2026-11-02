<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Subscription;

class InvoiceService
{
    public function generateForSubscription(Subscription $subscription): Invoice
    {
        return Invoice::create([
            'user_id' => $subscription->patient->user_id,
            'subscription_id' => $subscription->id,
            'invoice_number' => $this->nextInvoiceNumber(),
            'amount' => $subscription->plan->price,
            'currency' => $subscription->plan->currency,
            'status' => 'open',
            //'due_date' => now()->addDays(config('payment.invoice.due_days', 14)),
            'due_date' => now()->addDays((int) config('payment.invoice.due_days', 14)),
            'issued_at' => now(),
        ]);
    }

    public function markPaid(Invoice $invoice): void
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
    }

    public function markVoid(Invoice $invoice): void
    {
        $invoice->update(['status' => 'void']);
    }

    /**
     * Sequential, gap-free-per-prefix invoice numbers, e.g. INV-1000,
     * INV-1001. Stored as a running counter in `settings` rather than
     * derived from `count(*)` so numbers stay stable even if invoices
     * are later voided/deleted.
     */
    protected function nextInvoiceNumber(): string
    {
        $prefix = config('payment.invoice.prefix', 'INV');
        $key = "invoice_counter:{$prefix}";

        $next = (int) Setting::get($key, config('payment.invoice.starting_number', 1000));

        Setting::set($key, $next + 1, 'integer', 'payment');

        return "{$prefix}-{$next}";
    }
}
