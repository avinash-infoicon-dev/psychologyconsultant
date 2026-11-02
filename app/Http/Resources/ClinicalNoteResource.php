<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicalNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'psychologist_id' => $this->psychologist_id,
            'conversation_id' => $this->conversation_id,
            'content' => $this->content,
            'is_signed' => $this->is_signed,
            'signed_at' => $this->signed_at?->toIso8601String(),
            'editable_until' => $this->editable_until?->toIso8601String(),
            'is_editable' => $this->isEditable(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
