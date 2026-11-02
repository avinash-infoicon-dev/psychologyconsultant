<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public-facing directory listing — deliberately excludes email, license
 * number, and anything else a browsing patient doesn't need to see.
 */
class PsychologistDirectoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'title' => $this->title,
            'specialization' => $this->specialization,
            'specializations' => $this->whenLoaded(
                'specializations',
                fn () => $this->specializations->pluck('name')->values()
            ),
            'bio' => $this->bio,
            // Legacy API key retained for existing clients; data now comes from professionals.experience_years.
            'years_experience' => $this->experience_years,
            'languages' => $this->languages,
            'city' => $this->city,
            'country' => $this->country,
            'consultation_fee' => $this->consultation_fee,
            'rating' => $this->rating,
            'total_reviews' => $this->total_reviews,
            'profile_image' => $this->profile_image_url,
            'cover_image' => $this->cover_image_url,
            'online_consultation' => $this->online_consultation,
            'offline_consultation' => $this->offline_consultation,
            'is_verified' => $this->is_verified,
            'is_accepting_patients' => $this->is_accepting_patients,
            'is_featured' => $this->is_featured,
            'current_absence' => $this->when(
                $this->relationLoaded('currentAbsences') && $this->currentAbsences->isNotEmpty(),
                function () {
                    $absence = $this->currentAbsences->first();

                    return [
                        'starts_at' => $absence->starts_at?->toIso8601String(),
                        'ends_at' => $absence->ends_at?->toIso8601String(),
                        'reason' => $absence->reason,
                    ];
                }
            ),
        ];
    }
}
