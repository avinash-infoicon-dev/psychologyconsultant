<?php

namespace App\Services;

use App\Http\Resources\PsychologistDirectoryResource;
use App\Models\Patient;
use App\Models\PatientPsychologistRelationship;
use App\Models\Professional;
use App\Support\PsychologistCareStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PsychologistDirectoryService
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        // Legacy source:
        // $query = Psychologist::query()
        $query = Professional::query()
            ->psychologists()
            // Old: ->where('is_verified', true)->where('status', true)
            ->listedForPatients()
            ->with([
                'user',
                'professionalType',
                'specializations' => fn ($query) => $query->with('translations'),
                // Legacy: hide inactive specializations on directory cards.
                // 'specializations' => fn ($query) => $query->where('status', true)->with('translations'),
                'currentAbsences',
                'availabilities',
                'settings',
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderBy('first_name');

        if (! empty($filters['specialization'])) {
            $term = '%'.$filters['specialization'].'%';

            $query->where(function ($inner) use ($term) {
                $inner->where('specialization', 'like', $term)
                    ->orWhereHas(
                        'specializations',
                        fn ($specializationQuery) => $specializationQuery
                            ->where(function ($nameQuery) use ($term) {
                                $nameQuery->where('name', 'like', $term)
                                    ->orWhereHas('translations', fn ($t) => $t->where('name', 'like', $term));
                            })
                    );
            });
        }

        if (! empty($filters['language'])) {
            $query->whereJsonContains('languages', $filters['language']);
        }

        if (! empty($filters['availability'])) {
            $day = $filters['availability'] === 'today'
                ? now()->format('l')
                : $filters['availability'];

            $query->whereHas(
                'availabilities',
                fn ($availabilityQuery) => $availabilityQuery
                    ->where('day', $day)
                    ->where('is_available', true)
            );

            if ($filters['availability'] === 'today') {
                $query->whereDoesntHave('currentAbsences');
            }
        }

        if (! empty($filters['accepting_only'])) {
            $query->where('is_accepting_patients', true);
        }

        // Old: return $query->paginate($filters['per_page'] ?? 20);
        return $query->paginate($filters['per_page'] ?? 20)->withQueryString();
    }

    public function findForDirectory(Professional $professional): Professional
    {
        $professional->loadMissing(['professionalType', 'user']);

        abort_unless(
            $professional->isListedForPatients()
                && $professional->professionalType?->slug === 'psychologist',
            404,
            __('psychologist_directory_api.not_found')
        );

        return $this->loadDirectoryRelations($professional);
    }

    /**
     * @return array{professional: Professional, care_status: string, profile_mode: string}
     */
    public function viewForPatient(Professional $professional, ?Patient $patient): array
    {
        $professional->loadMissing(['professionalType', 'user']);

        abort_unless(
            $professional->professionalType?->slug === 'psychologist',
            404,
            __('psychologist_directory_api.not_found')
        );

        $status = PsychologistCareStatus::for($professional);
        $hasRelationship = $this->patientHasRelationship($patient, $professional);

        if ($status === PsychologistCareStatus::REMOVED) {
            abort(404, __('psychologist_directory_api.not_found'));
        }

        if ($status === PsychologistCareStatus::SUSPENDED && ! $hasRelationship) {
            abort(404, __('psychologist_directory_api.not_found'));
        }

        if ($status === PsychologistCareStatus::SUSPENDED) {
            $professional->load(['user', 'professionalType']);

            return [
                'professional' => $professional,
                'care_status' => $status,
                'profile_mode' => 'limited',
            ];
        }

        $this->loadDirectoryRelations($professional);

        return [
            'professional' => $professional,
            'care_status' => $status,
            'profile_mode' => 'full',
        ];
    }

    protected function patientHasRelationship(?Patient $patient, Professional $professional): bool
    {
        if (! $patient) {
            return false;
        }

        return PatientPsychologistRelationship::query()
            ->where('patient_id', $patient->id)
            ->where('psychologist_id', $professional->id)
            ->exists();
    }

    protected function loadDirectoryRelations(Professional $professional): Professional
    {
        return $professional->load([
            'user',
            'professionalType',
            'specializations' => fn ($query) => $query->with('translations'),
            // Legacy: 'specializations' => fn ($query) => $query->where('status', true)->with('translations'),
            'educations' => fn ($query) => $query->latest('year'),
            'experiences' => fn ($query) => $query->latest('start_date'),
            'services' => fn ($query) => $query->where('status', true),
            'availabilities' => fn ($query) => $query->where('is_available', true),
            'currentAbsences',
            'reviews' => fn ($query) => $query
                ->where('is_approved', true)
                ->where('status', true)
                ->latest(),
            'galleries' => fn ($query) => $query
                ->where('status', true)
                ->orderBy('sort_order'),
            'settings',
            'absences',
        ]);
    }

    /** @return array{data: AnonymousResourceCollection, meta: array<string, int>} */
    public function paginatedPayload(LengthAwarePaginator $psychologists): array
    {
        return [
            'data' => PsychologistDirectoryResource::collection($psychologists),
            'meta' => [
                'current_page' => $psychologists->currentPage(),
                'last_page' => $psychologists->lastPage(),
                'total' => $psychologists->total(),
            ],
        ];
    }
}
