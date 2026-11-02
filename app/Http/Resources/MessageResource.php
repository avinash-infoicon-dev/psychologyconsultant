<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'conversation_id' => $this->whenLoaded(
                'conversation',
                fn () => $this->conversation->uuid,
            ),
            'sender_id' => $this->sender_id,
            'type' => $this->type,
            'body' => $this->when($this->type === 'text', $this->body),
            'audio' => $this->when($this->type === 'audio', function () {
                return $this->audio ? [
                    'duration_seconds' => $this->audio->duration_seconds,
                    'transcript' => $this->audio->transcript,
                    'transcription_status' => $this->audio->transcription_status,
                    'language' => $this->audio->language,
                ] : null;
            }),
            'is_read' => $this->is_read,
            'status' => $this->is_read ? 'read' : 'sent',
            'read_at' => $this->read_at?->toIso8601String(),
            'edited_at' => $this->edited_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
