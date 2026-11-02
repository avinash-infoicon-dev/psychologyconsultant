<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Specialization;
use App\Services\ProfessionalMediaService;
use App\Services\PsychologistVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ResolvesProfessional;

    public function __construct(
        protected ProfessionalMediaService $media,
        protected PsychologistVerificationService $verification,
    ) {}

    public function show(Request $request): View
    {
        // dd('working');
        $professional = $this->requireProfessional($request);
        $tab = $request->string('tab', 'overview')->toString();

        $professional->load([
            'user',
            'specializations.translations',
            'educations' => fn ($q) => $q->latest('year'),
            'experiences' => fn ($q) => $q->latest('start_date'),
            'socialLinks',
            'galleries' => fn ($q) => $q->orderBy('sort_order'),
            'documents' => fn ($q) => $q->latest(),
            'awards' => fn ($q) => $q->latest('year'),
            'settings',
        ]);

        // Legacy: every active row, including none assigned yet.
        // $specializations = Specialization::query()->with('translations')->where('status', true)->orderBy('name')->get();
        
        // $testSpecialization = new Specialization([
        //     'id' => 4,
        //     'name' => 'Mock Testing Specialization', // Customize this name for testing sorting
        //     'slug' => 'mock-testing-specialization',
        //     'description' => 'This is a mock description for testing purposes.',
        //     'status' => 1,
        //     'archived_at' => null,
        //     'created_at' => '2026-08-19 10:24:40',
        //     'updated_at' => '2026-08-19 10:24:40',
        // ]);
        $specializations = Specialization::query()
            ->with('translations')
            ->selectable()
            ->orderBy('name')
            ->get()
            ->concat($professional->specializations)
            //->concat([$testSpecialization])
            ->unique('id')
            ->sortBy('name')
            ->values();
        // dd($specializations);

        return view('frontend.psychologist.profile.show', [
            'professional' => $professional,
            'tab' => $tab,
            'specializations' => $specializations,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', 'max:10'],
            'title' => ['nullable', 'string', 'max:50'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'organization' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'languages' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'online_consultation' => ['nullable', 'boolean'],
            'offline_consultation' => ['nullable', 'boolean'],
            'is_accepting_patients' => ['nullable', 'boolean'],
            'profile_image' => ['nullable', 'image', 'max:4096'],
            //'cover_image' => ['nullable', 'image', 'max:6144'],
            'cover_image' => ['nullable', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml', 'max:6144',],

            'specialization_ids' => ['nullable', 'array'],
            'specialization_ids.*' => ['integer', 'exists:specializations,id'],
        ]);

        DB::transaction(function () use ($request, $professional, $data) {
            $professional->user?->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'locale' => $data['locale'] ?? $professional->user->locale,
            ]);

            $languages = collect(explode(',', (string) ($data['languages'] ?? '')))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();

            $updates = [
                'title' => $data['title'] ?? null,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'organization' => $data['organization'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'consultation_fee' => $data['consultation_fee'] ?? null,
                'bio' => $data['bio'] ?? null,
                'languages' => $languages,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'online_consultation' => $request->boolean('online_consultation'),
                'offline_consultation' => $request->boolean('offline_consultation'),
                'is_accepting_patients' => $request->boolean('is_accepting_patients'),
            ];

            if ($request->hasFile('profile_image')) {
                $this->media->deletePublicPath($professional->profile_image);
                $updates['profile_image'] = $this->media->storePublicImage(
                    $request->file('profile_image'),
                    $professional->id,
                    'profile'
                );
            }

            if ($request->hasFile('cover_image')) {
                $this->media->deletePublicPath($professional->cover_image);
                $updates['cover_image'] = $this->media->storePublicImage(
                    $request->file('cover_image'),
                    $professional->id,
                    'cover'
                );
            }

            $professional->update($updates);
            // Legacy: sync posted IDs only (would drop archived history).
            // $professional->specializations()->sync($request->input('specialization_ids', []));
            $submitted = collect($request->input('specialization_ids', []))->map(fn ($id) => (int) $id);
            $keepHistorical = $professional->specializations
                ->reject(fn ($spec) => $spec->isSelectable())
                ->pluck('id');
            $selectableIds = Specialization::query()->selectable()->pluck('id');
            $professional->specializations()->sync(
                $submitted->intersect($selectableIds)->merge($keepHistorical)->unique()->values()->all()
            );
        });

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'overview'])
            ->with('success', __('frontend.psych_profile.updated'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);

        $data = $request->validate([
            'timezone' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rule::in(\DateTimeZone::listIdentifiers()),
            ],
        ]);

        $professional->settings()->updateOrCreate(
            ['professional_id' => $professional->id],
            ['timezone' => $data['timezone'] ?? 'Europe/Zurich']
        );

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'settings'])
            ->with('success', 'Profile settings updated successfully.');
    }

    public function submitVerification(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        $this->verification->submit($professional);

        return redirect()
            ->route('frontend.psychologist.profile.show')
            ->with('success', __('frontend.psych_profile.verification_submitted'));
    }

    protected function requireProfessional(Request $request): Professional
    {
        $professional = $this->professional($request->user());
        abort_unless($professional, 403);

        return $professional;
    }
}
