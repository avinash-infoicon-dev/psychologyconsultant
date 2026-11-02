<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PsychologistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'license_number' => $this->license_number,
            'specialization' => $this->specialization,
            'bio' => $this->bio,
            'years_experience' => $this->years_experience,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'verification_reason' => $this->when(
                in_array($this->verification_status, ['rejected', 'suspended'], true),
                $this->verification_reason
            ),
            'is_accepting_patients' => $this->is_accepting_patients,
            'languages' => $this->languages,
            'active_patient_count' => $this->when(
                $request->user()?->isAdmin() || $request->user()?->psychologist?->id === $this->id,
                fn () => $this->activePatientCount()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
