<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileEducationController extends Controller
{
    use ResolvesProfessional;

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        Education::query()->create([
            ...$this->validated($request),
            'professional_id' => $professional->id,
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'education'])
            ->with('success', __('frontend.psych_profile.education_created'));
    }

    public function update(Request $request, Education $education): RedirectResponse
    {
        $this->assertOwns($request, $education->professional_id);
        $education->update($this->validated($request));

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'education'])
            ->with('success', __('frontend.psych_profile.education_updated'));
    }

    public function destroy(Request $request, Education $education): RedirectResponse
    {
        $this->assertOwns($request, $education->professional_id);
        $education->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'education'])
            ->with('success', __('frontend.psych_profile.education_deleted'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'degree' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    protected function requireProfessional(Request $request): Professional
    {
        $id = $this->professionalId($request->user());
        abort_unless($id, 403);

        return Professional::query()->findOrFail($id);
    }

    protected function assertOwns(Request $request, int $professionalId): void
    {
        abort_unless((int) $this->professionalId($request->user()) === $professionalId, 403);
    }
}
