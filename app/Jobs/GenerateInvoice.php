<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\Payment\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched for each Twint subscriber whose billing period has ended
 * (Twint has no gateway-side recurring billing — see SubscriptionService).
 * Stripe subscribers are invoiced via Stripe's own billing + webhook.
 */
class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Subscription $subscription) {}

    public function handle(InvoiceService $invoices): void
    {
        if ($this->subscription->status !== 'active') {
            return;
        }

        $invoices->generateForSubscription($this->subscription);
    }
}
