<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::with('translations')->orderBy('sort_order')->get();

        return response()->json(['data' => PlanResource::collection($plans)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->syncActiveFlag($this->validated($request));
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $plan = Plan::create($data);

        return response()->json(['data' => new PlanResource($plan)], 201);
    }

    public function show(Plan $plan): JsonResponse
    {
        return response()->json(['data' => new PlanResource($plan)]);
    }

    public function update(Request $request, Plan $plan): JsonResponse
    {
        $plan->update($this->syncActiveFlag($this->validated($request, $plan)));

        return response()->json(['data' => new PlanResource($plan->fresh())]);
    }

    /** Plans are never hard-deleted — archive so subscription history is kept. */
    public function destroy(Plan $plan): JsonResponse
    {
        // abort_if($plan->subscriptions()->exists(), 422, 'Cannot delete a plan with existing subscriptions — deactivate it instead.');
        //
        // $plan->delete();
        //
        // return response()->json(['message' => 'Plan deleted.']);

        $plan->archive();

        return response()->json(['message' => 'Plan archived.']);
    }

    protected function validated(Request $request, ?Plan $plan = null): array
    {
        return $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', Rule::unique('plans', 'slug')->ignore($plan?->id)],
            'plan_type' => ['sometimes', Rule::in([Plan::TYPE_SUBSCRIPTION, Plan::TYPE_ON_DEMAND, Plan::TYPE_ADDON])],
            'description' => ['sometimes', 'nullable', 'string'],
            'motto' => ['sometimes', 'nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            // Old intervals only:
            // 'billing_interval' => ['sometimes', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'billing_interval' => ['sometimes', Rule::in(['monthly', 'quarterly', 'yearly', 'per_exchange'])],
            'exchange_quota' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_text_chars' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_audio_seconds' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'patient_messages_per_exchange' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'psychologist_messages_per_exchange' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'response_time_hours' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:720'],
            'response_time_label' => ['sometimes', 'nullable', 'string', 'max:255'],
            'channels' => ['sometimes', 'nullable', 'array'],
            'channels.*' => ['string', Rule::in([Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION, Plan::CHANNEL_AUDIO])],
            'features' => ['sometimes', 'array'],
            'stripe_price_id' => ['sometimes', 'nullable', 'string'],
            // 'is_active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in([Plan::STATUS_DRAFT, Plan::STATUS_ACTIVE, Plan::STATUS_ARCHIVED])],
            'is_active' => ['sometimes', 'boolean'],
            'is_recommended' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);
    }

    /** Keep is_active aligned with draft / active / archived. */
    protected function syncActiveFlag(array $data): array
    {
        if (isset($data['status'])) {
            $data['is_active'] = $data['status'] === Plan::STATUS_ACTIVE;
        } elseif (array_key_exists('is_active', $data)) {
            $data['status'] = $data['is_active'] ? Plan::STATUS_ACTIVE : Plan::STATUS_DRAFT;
        }

        return $data;
    }
}
