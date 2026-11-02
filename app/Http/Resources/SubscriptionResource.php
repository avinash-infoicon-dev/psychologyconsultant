<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'current_period_ends_at' => $this->current_period_ends_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'exchanges_used' => $this->exchanges_used,
            'exchanges_remaining' => $this->remaining_exchanges,
            'included_exchanges' => $this->included_exchanges,
            'consumed_exchanges' => $this->consumed_exchanges,
            'purchased_exchanges' => $this->purchased_exchanges,
            'auto_renew' => (bool) $this->auto_renew,
            'cancel_at_period_end' => (bool) $this->cancel_at_period_end,
            'pending_plan_id' => $this->pending_plan_id,
            'last_payment_failed_at' => $this->last_payment_failed_at?->toIso8601String(),
            'last_payment_error' => $this->last_payment_error,
        ];
    }
}
