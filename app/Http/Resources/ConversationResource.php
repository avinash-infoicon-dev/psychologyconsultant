<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'status' => $this->status,
            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name,
            ],
            'psychologist' => [
                'id' => $this->psychologist->id,
                'name' => $this->psychologist->user->name,
                'specialization' => $this->psychologist->specialization,
            ],
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'latest_message' => new MessageResource($this->whenLoaded('messages', fn () => $this->messages->last())),
            'unread_count' => $this->when(
                isset($this->unread_count),
                fn () => $this->unread_count
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
