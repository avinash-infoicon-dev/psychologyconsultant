<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Messaging\ExchangeQuotaService;
use App\Services\Payment\PlanPurchaseService;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions,
        protected PlanPurchaseService $purchases,
        protected ExchangeQuotaService $quota,
        protected \App\Services\Payment\StripeService $stripe,
    ) {}

    /** Current status + catalogue (subscription tiers + on-demand / addon packs). */
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('patient');
        $patient = $user->patient;

        if ($request->has('session_id')) {
            try {
                $session = $this->stripe->retrieveCheckoutSession($request->string('session_id')->toString());
                if (($session['payment_status'] ?? null) === 'paid') {
                    $planId = $session['metadata']['plan_id'] ?? null;
                    $stripeSubscriptionId = is_string($session['subscription'] ?? null)
                        ? $session['subscription']
                        : ($session['subscription']['id'] ?? null);

                    if ($planId) {
                        $planModel = Plan::find($planId);
                        if ($planModel) {
                            $intent = $session['metadata']['intent'] ?? 'subscribe';
                            if ($intent === 'change') {
                                $subscription = $patient?->lifecycleSubscription();
                                if ($subscription) {
                                    $this->subscriptions->changePlan($subscription, $planModel);
                                    if ($stripeSubscriptionId && ! $subscription->stripe_subscription_id) {
                                        $subscription->update(['stripe_subscription_id' => $stripeSubscriptionId]);
                                    }
                                }
                            } else {
                                $already = $stripeSubscriptionId
                                    ? \App\Models\Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->exists()
                                    : false;
                                if (! $already) {
                                    $this->purchases->purchase($user, $planModel, 'stripe', [
                                        'stripe_subscription_id' => $stripeSubscriptionId,
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore session check errors gracefully
            }
        }
        
        // Legacy: only status=active (hides past due from lifecycle actions).
        // $active = $patient?->activeSubscription();
        $active = $patient?->lifecycleSubscription();
        $subscription = $active ?? $patient?->latestSubscription();
        $subscription?->loadMissing(['plan.translations', 'pendingPlan.translations']);

        // Legacy: always show the full catalogue.
        // $plans = Plan::query()
        //     ->where('is_active', true)
        //     ->orderBy('sort_order')
        //     ->orderBy('name')
        //     ->get();

        // Active subscribers already have a monthly tier — only offer
        // on-demand / additional-exchange packs. Non-subscribers see all plans.
        // $plansQuery = Plan::query()
        //     ->with('translations')
        //     ->where('is_active', true)
        //     ->orderBy('sort_order')
        //     ->orderBy('name');
        // if ($subscription) {
        //     $plansQuery->whereIn('plan_type', [Plan::TYPE_ON_DEMAND, Plan::TYPE_ADDON]);
        // }
        // $plans = $plansQuery->get();

        $plans = Plan::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $addonPlans = $plans->filter(fn (Plan $plan) => $plan->isPerExchange())->values();
        $changePlans = $plans
            ->filter(fn (Plan $plan) => $plan->isSubscription() && ! $plan->isPerExchange())
            ->when($active, fn ($collection) => $collection->reject(
                fn (Plan $plan) => (int) $plan->id === (int) $active->plan_id
            ))
            ->values();

        $subscriptionsHistory = $patient?->subscriptions()
            ->with(['plan.translations'])
            ->orderBy('id', 'desc')
            ->get() ?? collect();

        $exchangesHistory = $patient?->exchanges()
            ->with(['psychologist.user', 'subscription'])
            ->orderBy('id', 'desc')
            ->get() ?? collect();
        
        $catalogue = $active ? $addonPlans : $plans;

        return view('frontend.patient.subscription.index', [
            'user' => $user,
            'patient' => $patient,
            'subscription' => $subscription,
            'activeSubscription' => $active,
            'creditBalance' => (int) ($patient?->creditBalance() ?? 0),
            'exchangeBalance' => $this->quota->balanceForPatient($patient),
            'plans' => $catalogue,
            'changePlans' => $changePlans,
            'addonPlans' => $addonPlans,
            'quotaExceeded' => $request->boolean('quota'),
            'subscriptionsHistory' => $subscriptionsHistory,
            'exchangesHistory' => $exchangesHistory,
        ]);
    }

    /** Current Plan details page. */
    public function current(Request $request): View
    {
        $user = $request->user()->loadMissing('patient');
        $patient = $user->patient;
        $active = $patient?->lifecycleSubscription();
        $subscription = $active ?? $patient?->latestSubscription();
        $subscription?->loadMissing(['plan.translations', 'pendingPlan.translations']);

        $plans = Plan::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $changePlans = $plans
            ->filter(fn (Plan $plan) => $plan->isSubscription() && ! $plan->isPerExchange())
            ->when($active, fn ($collection) => $collection->reject(
                fn (Plan $plan) => (int) $plan->id === (int) $active->plan_id
            ))
            ->values();

        return view('frontend.patient.subscription.current', [
            'user' => $user,
            'patient' => $patient,
            'subscription' => $subscription,
            'activeSubscription' => $active,
            'creditBalance' => (int) ($patient?->creditBalance() ?? 0),
            'exchangeBalance' => $this->quota->balanceForPatient($patient),
            'changePlans' => $changePlans,
        ]);
    }

    /** All Plans / Catalogue page. */
    public function plans(Request $request): View
    {
        $user = $request->user()->loadMissing('patient');
        $patient = $user->patient;
        $active = $patient?->lifecycleSubscription();
        $subscription = $active ?? $patient?->latestSubscription();
        $subscription?->loadMissing(['plan.translations', 'pendingPlan.translations']);

        $plans = Plan::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $addonPlans = $plans->filter(fn (Plan $plan) => $plan->isPerExchange())->values();
        $changePlans = $plans
            ->filter(fn (Plan $plan) => $plan->isSubscription() && ! $plan->isPerExchange())
            ->when($active, fn ($collection) => $collection->reject(
                fn (Plan $plan) => (int) $plan->id === (int) $active->plan_id
            ))
            ->values();

        $catalogue = $active ? $addonPlans : $plans;

        return view('frontend.patient.subscription.plans', [
            'user' => $user,
            'patient' => $patient,
            'subscription' => $subscription,
            'activeSubscription' => $active,
            'plans' => $catalogue,
            'changePlans' => $changePlans,
            'addonPlans' => $addonPlans,
            'quotaExceeded' => $request->boolean('quota'),
        ]);
    }

    /** Subscription History page (with pagination). */
    public function history(Request $request): View
    {
        $user = $request->user()->loadMissing('patient');
        $patient = $user->patient;

        $subscriptionsHistory = $patient?->subscriptions()
            ->with(['plan.translations'])
            ->orderBy('id', 'desc')
            ->paginate(10) ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        return view('frontend.patient.subscription.history', [
            'user' => $user,
            'patient' => $patient,
            'subscriptionsHistory' => $subscriptionsHistory,
        ]);
    }

    /** Exchange History page (with pagination). */
    public function exchanges(Request $request): View
    {
        dd('Not available');
        $user = $request->user()->loadMissing('patient');
        $patient = $user->patient;

        $exchangesHistory = $patient?->exchanges()
            ->with(['psychologist.user', 'subscription'])
            ->orderBy('id', 'desc')
            ->paginate(10) ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        return view('frontend.patient.subscription.exchanges', [
            'user' => $user,
            'patient' => $patient,
            'exchangesHistory' => $exchangesHistory,
        ]);
    }

    /** Dummy payment page (no real gateway). */
    public function checkout(Request $request, Plan $plan): View|RedirectResponse
    {
        if (! $plan->is_active) {
            return redirect()
                ->route('frontend.patient.subscription.index')
                ->withErrors(['plan' => __('frontend.subscription.plan_unavailable')]);
        }

        $plan->loadMissing('translations');

        return view('frontend.patient.subscription.checkout', [
            'plan' => $plan,
            'returnTo' => $request->string('return')->toString() ?: null,
            'reason' => $request->string('reason')->toString() ?: null,
            'intent' => $request->string('intent')->toString() ?: 'subscribe',
        ]);
    }

    /** Dummy pay always succeeds → activate subscription or award credits. */
    public function pay(Request $request, Plan $plan): RedirectResponse
    {
        if (! $plan->is_active) {
            return redirect()
                ->route('frontend.patient.subscription.index')
                ->withErrors(['plan' => __('frontend.subscription.plan_unavailable')]);
        }

        try {
            $intent = $request->string('intent')->toString() ?: 'subscribe';
            $paymentMethod = $request->string('payment_method')->toString() ?: 'stripe';

            if (in_array($paymentMethod, ['stripe', 'twint'], true)) {
                $isSubscriptionMode = ($plan->plan_type === Plan::TYPE_SUBSCRIPTION && $plan->billing_interval !== 'per_exchange');
                $params = [
                    'mode' => $isSubscriptionMode ? 'subscription' : 'payment',
                    'price_id' => $plan->stripe_price_id,
                    // 'price_id' => "price_1UJ7a32O7LLrVwB3D91oAv1b",
                    // Price is stored in smallest currency unit (rappen).
                    // 'amount' => (int) round($plan->price * 100),
                    'amount' => (int) $plan->price,
                    'currency' => $plan->currency ?? 'chf',
                    'product_name' => $plan->name,
                    'customer_email' => $request->user()->email,
                    'success_url' => route('frontend.patient.subscription.index') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('frontend.patient.subscription.checkout', $plan->id),
                    'metadata' => [
                        'user_id' => (string) $request->user()->id,
                        'plan_id' => (string) $plan->id,
                        'intent' => $intent,
                        'payment_method' => $paymentMethod,
                    ],
                ];

                if ($paymentMethod === 'twint') {
                    $params['payment_method_types'] = ['twint'];
                }

                if ($isSubscriptionMode) {
                    $params['interval'] = $plan->billing_interval === 'yearly' ? 'year' : 'month';
                }

                $session = $this->stripe->createCheckoutSession($params);

                if (! empty($session['url'])) {
                    return redirect()->away($session['url']);
                }

                throw new RuntimeException('Unable to start Stripe checkout.');
            }

            // Dummy payment (kept, not deleted):
            // if ($intent === 'change') {
            //     $subscription = $request->user()->patient?->lifecycleSubscription();
            //     ...
            //     $this->subscriptions->changePlan($subscription, $plan);
            // } else {
            //     $this->purchases->purchase($request->user(), $plan, 'dummy');
            // }

            if ($intent === 'change') {
                $subscription = $request->user()->patient?->lifecycleSubscription();
                if (! $subscription) {
                    return redirect()
                        ->route('frontend.patient.subscription.index')
                        ->withErrors(['subscription' => __('frontend.subscription.none_active')]);
                }

                $this->subscriptions->changePlan($subscription, $plan);
            } else {
                // /$this->purchases->purchase($request->user(), $plan, $dummy);
                $this->purchases->purchase($request->user(), $plan, $paymentMethod);
            }
        } catch (RuntimeException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        $returnTo = $request->string('return')->toString();
        $success = $intent === 'change'
            ? __('frontend.subscription.changed')
            : __('frontend.subscription.payment_success');

        if ($returnTo && str_starts_with($returnTo, url('/'))) {
            return redirect()
                ->to($returnTo)
                ->with('success', $success);
        }

        return redirect()
            ->route('frontend.patient.subscription.index')
            ->with('success', $success);
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $request->user()->patient?->lifecycleSubscription();

        if (! $subscription) {
            return redirect()
                ->route('frontend.patient.subscription.index')
                ->withErrors(['subscription' => __('frontend.subscription.none_active')]);
        }

        try {
            $this->subscriptions->cancel($subscription);
        } catch (RuntimeException $e) {
            return back()->withErrors(['subscription' => $e->getMessage()]);
        }

        return redirect()
            ->route('frontend.patient.subscription.index')
            ->with('success', __('frontend.subscription.cancelled'));
    }

    public function autoRenew(Request $request): RedirectResponse
    {
        $subscription = $request->user()->patient?->lifecycleSubscription();

        if (! $subscription) {
            return redirect()
                ->route('frontend.patient.subscription.index')
                ->withErrors(['subscription' => __('frontend.subscription.none_active')]);
        }

        $enabled = $request->boolean('auto_renew');

        try {
            $this->subscriptions->setAutoRenew($subscription, $enabled);
        } catch (RuntimeException $e) {
            return back()->withErrors(['subscription' => $e->getMessage()]);
        }

        return redirect()
            ->route('frontend.patient.subscription.index')
            ->with('success', $enabled
                ? __('frontend.subscription.auto_renew_on')
                : __('frontend.subscription.auto_renew_off'));
    }

    /** Dummy recovery after a failed renewal charge. No real gateway in this step. */
    public function retryPayment(Request $request): RedirectResponse
    {
        $subscription = $request->user()->patient?->lifecycleSubscription();

        if (! $subscription) {
            return redirect()
                ->route('frontend.patient.subscription.index')
                ->withErrors(['subscription' => __('frontend.subscription.none_active')]);
        }

        $this->subscriptions->retryPayment($subscription);

        return redirect()
            ->route('frontend.patient.subscription.index')
            ->with('success', __('frontend.subscription.payment_retried'));
    }
}