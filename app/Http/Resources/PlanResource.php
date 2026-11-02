<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'plan_type' => $this->plan_type,
            'description' => $this->description,
            'motto' => $this->motto,
            'price' => $this->price,
            'display_price' => $this->displayPrice(),
            'currency' => $this->currency,
            'billing_interval' => $this->billing_interval,
            'exchange_quota' => $this->exchange_quota,
            'max_text_chars' => $this->max_text_chars,
            'max_audio_seconds' => $this->max_audio_seconds,
            'patient_messages_per_exchange' => $this->patient_messages_per_exchange,
            'psychologist_messages_per_exchange' => $this->psychologist_messages_per_exchange,
            'response_time_hours' => $this->response_time_hours,
            'response_time_label' => $this->response_time_label,
            'channels' => $this->channels,
            'features' => $this->features,
            'is_recommended' => (bool) $this->is_recommended,
            'status' => $this->status,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
