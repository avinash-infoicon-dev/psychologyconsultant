<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Professional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileAwardController extends Controller
{
    use ResolvesProfessional;

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        $data = $this->validated($request);

        Award::query()->create([
            ...$data,
            'professional_id' => $professional->id,
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'awards'])
            ->with('success', __('frontend.psych_profile.award_created'));
    }

    public function update(Request $request, Award $award): RedirectResponse
    {
        $this->assertOwns($request, $award->professional_id);
        $award->update($this->validated($request));

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'awards'])
            ->with('success', __('frontend.psych_profile.award_updated'));
    }

    public function destroy(Request $request, Award $award): RedirectResponse
    {
        $this->assertOwns($request, $award->professional_id);
        $award->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'awards'])
            ->with('success', __('frontend.psych_profile.award_deleted'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
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
