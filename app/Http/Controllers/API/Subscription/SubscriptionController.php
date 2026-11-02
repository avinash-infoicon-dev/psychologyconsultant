<?php

namespace App\Http\Controllers\API\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Plan;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptions) {}

    public function current(Request $request): JsonResponse
    {
        $subscription = $request->user()->patient?->lifecycleSubscription();
        // Legacy: only returned status=active.
        // $subscription = $request->user()->patient?->activeSubscription();

        if (! $subscription) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => new SubscriptionResource($subscription->load('plan'))]);
    }

    public function subscribe(CreateSubscriptionRequest $request): JsonResponse
    {
        $plan = Plan::findOrFail($request->integer('plan_id'));

        try {
            $subscription = $this->subscriptions->subscribe(
                $request->user(),
                $plan,
                $request->string('gateway'),
                $request->input('payment_method_id'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['data' => new SubscriptionResource($subscription->load('plan'))], 201);
    }

    public function cancel(Request $request): JsonResponse
    {
        $subscription = $request->user()->patient?->lifecycleSubscription();
        // Legacy: only cancelled status=active.
        // $subscription = $request->user()->patient?->activeSubscription();

        abort_unless($subscription, 404, 'No active subscription to cancel.');

        $this->subscriptions->cancel($subscription);

        return response()->json(['message' => 'Subscription cancelled.']);
    }
}
