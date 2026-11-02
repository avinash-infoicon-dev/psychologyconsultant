<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileExperienceController extends Controller
{
    use ResolvesProfessional;

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        $data = $this->validated($request);
        $data['currently_working'] = $request->boolean('currently_working');
        if ($data['currently_working']) {
            $data['end_date'] = null;
        }

        Experience::query()->create([
            ...$data,
            'professional_id' => $professional->id,
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'experience'])
            ->with('success', __('frontend.psych_profile.experience_created'));
    }

    public function update(Request $request, Experience $experience): RedirectResponse
    {
        $this->assertOwns($request, $experience->professional_id);
        $data = $this->validated($request);
        $data['currently_working'] = $request->boolean('currently_working');
        if ($data['currently_working']) {
            $data['end_date'] = null;
        }
        $experience->update($data);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'experience'])
            ->with('success', __('frontend.psych_profile.experience_updated'));
    }

    public function destroy(Request $request, Experience $experience): RedirectResponse
    {
        $this->assertOwns($request, $experience->professional_id);
        $experience->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'experience'])
            ->with('success', __('frontend.psych_profile.experience_deleted'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'designation' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'currently_working' => ['nullable', 'boolean'],
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
