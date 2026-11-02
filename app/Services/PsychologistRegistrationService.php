<?php

namespace App\Services;

use App\Models\Award;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Professional;
use App\Models\ProfessionalDocument;
use App\Models\ProfessionalGallery;
use App\Models\ProfessionalSocialLink;
use App\Models\Specialization;
use Illuminate\Http\UploadedFile;

class PsychologistRegistrationService
{
    public function __construct(protected ProfessionalMediaService $media) {}

    public function attachRelated(Professional $professional, array $data): void
    {
        $this->storeSpecializations($professional, $data['specialization_ids'] ?? []);
        $this->storeEducations($professional, $data['education'] ?? []);
        $this->storeExperiences($professional, $data['experience'] ?? []);
        $this->storeSocial($professional, $data['social'] ?? []);
        $this->storeGallery($professional, $data['gallery'] ?? []);
        $this->storeDocuments($professional, $data['documents'] ?? []);
        $this->storeAwards($professional, $data['awards'] ?? []);
        $this->storeImages($professional, $data);
    }

    public function overviewAttributes(array $data, \App\Models\User $user): array
    {
        $languages = $data['languages'] ?? null;
        if (is_string($languages)) {
            $languages = array_values(array_filter(array_map('trim', explode(',', $languages))));
        }

        $credentials = null;
        if (! empty($data['specializations_text'])) {
            $credentials = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $data['specializations_text']))));
        }

        return [
            'title' => $data['title'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'gender' => $this->normalizeGender($data['gender'] ?? null),
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'email' => $user->email,
            'phone' => $user->phone,
            'website' => $data['website'] ?? null,
            'organization' => $data['organization'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'specialization' => null,
            'experience_years' => (int) ($data['years_experience'] ?? 0),
            'consultation_fee' => $data['consultation_fee'] ?? null,
            'bio' => $data['bio'] ?? null,
            'languages' => $languages ?: null,
            'credentials' => $credentials,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ];
    }

    protected function storeSpecializations(Professional $professional, array $ids): void
    {
        $selected = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $selectableIds = Specialization::query()
            ->selectable()
            ->whereIn('id', $selected)
            ->pluck('id');

        $professional->specializations()->sync($selectableIds->all());

        $names = Specialization::query()
            ->with('translations')
            ->whereIn('id', $selectableIds)
            ->get()
            ->pluck('name')
            ->filter()
            ->implode(', ');

        $professional->update([
            'specialization' => $names !== '' ? $names : null,
        ]);
    }

    protected function storeImages(Professional $professional, array $data): void
    {
        $updates = [];
        if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
            $updates['profile_image'] = $this->media->storePublicImage($data['profile_photo'], $professional->id, 'profile');
        }
        if (($data['cover_image'] ?? null) instanceof UploadedFile) {
            $updates['cover_image'] = $this->media->storePublicImage($data['cover_image'], $professional->id, 'cover');
        }
        if ($updates !== []) {
            $professional->update($updates);
        }
    }

    protected function storeEducations(Professional $professional, array $rows): void
    {
        foreach ($rows as $row) {
            if (! is_array($row) || ! filled($row['degree'] ?? null) || ! filled($row['institution'] ?? null)) {
                continue;
            }
            Education::query()->create([
                'professional_id' => $professional->id,
                'degree' => $row['degree'] ?? null,
                'institution' => $row['institution'] ?? null,
                'field_of_study' => $row['field_of_study'] ?? ($row['field'] ?? null),
                'year' => $row['year'] ?: null,
                'description' => $row['description'] ?? null,
            ]);
        }
    }

    protected function storeExperiences(Professional $professional, array $rows): void
    {
        foreach ($rows as $row) {
            if (
                ! is_array($row)
                || ! filled($row['organization'] ?? null)
                || ! filled($row['role'] ?? ($row['designation'] ?? null))
            ) {
                continue;
            }
            $current = ! empty($row['current']) || ! empty($row['currently_working']);
            Experience::query()->create([
                'professional_id' => $professional->id,
                'designation' => $row['role'] ?? ($row['designation'] ?? null),
                'organization' => $row['organization'] ?? null,
                'start_date' => $row['start_date'] ?: null,
                'end_date' => $current ? null : ($row['end_date'] ?: null),
                'currently_working' => $current,
                'description' => $row['description'] ?? null,
            ]);
        }
    }

    protected function storeSocial(Professional $professional, array $rows): void
    {
        foreach ($rows as $row) {
            if (! is_array($row) || ! filled($row['platform'] ?? null) || ! filled($row['url'] ?? null)) {
                continue;
            }
            ProfessionalSocialLink::query()->create([
                'professional_id' => $professional->id,
                'platform' => $row['platform'] ?? null,
                'url' => $row['url'] ?? null,
                'status' => true,
            ]);
        }
    }

    protected function storeGallery(Professional $professional, array $rows): void
    {
        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $image = $row['image'] ?? null;
            if (! $image instanceof UploadedFile && ! $this->filledAny($row, ['title', 'description'])) {
                continue;
            }
            if (! $image instanceof UploadedFile) {
                continue;
            }
            ProfessionalGallery::query()->create([
                'professional_id' => $professional->id,
                'title' => $row['title'] ?? null,
                'description' => $row['description'] ?? null,
                'sort_order' => (int) ($row['sort_order'] ?? $index + 1),
                'status' => true,
                'image' => $this->media->storePublicImage($image, $professional->id, 'gallery'),
            ]);
        }
    }

    protected function storeDocuments(Professional $professional, array $rows): void
    {
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $file = $row['file'] ?? ($row['document'] ?? null);
            if (! $file instanceof UploadedFile) {
                continue;
            }
            ProfessionalDocument::query()->create([
                'professional_id' => $professional->id,
                'title' => $row['title'] ?: $file->getClientOriginalName(),
                'document_type' => $row['type'] ?? ($row['document_type'] ?? null),
                'document' => $this->media->storePrivateDocument($file, $professional->id),
                'is_verified' => false,
                'status' => true,
            ]);
        }
    }

    protected function storeAwards(Professional $professional, array $rows): void
    {
        foreach ($rows as $row) {
            if (! is_array($row) || ! filled($row['title'] ?? null)) {
                continue;
            }
            Award::query()->create([
                'professional_id' => $professional->id,
                'title' => $row['title'] ?? null,
                'organization' => $row['organization'] ?? null,
                'year' => $row['year'] ?: null,
                'description' => $row['description'] ?? null,
            ]);
        }
    }

    protected function normalizeGender(?string $gender): ?string
    {
        if ($gender === null || trim($gender) === '') {
            return null;
        }

        return match (strtolower(trim($gender))) {
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
            default => null,
        };
    }

    protected function filledAny(array $row, array $keys): bool
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && is_string($row[$key]) && trim($row[$key]) !== '') {
                return true;
            }
            if (isset($row[$key]) && is_numeric($row[$key])) {
                return true;
            }
        }

        return false;
    }
}
