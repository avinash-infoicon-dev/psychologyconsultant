<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ProfessionalSocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileSocialLinkController extends Controller
{
    use ResolvesProfessional;

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);
        $data = $this->validated($request, $professional->id);

        ProfessionalSocialLink::query()->create([
            ...$data,
            'professional_id' => $professional->id,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'social'])
            ->with('success', __('frontend.psych_profile.social_created'));
    }

    public function update(Request $request, ProfessionalSocialLink $socialLink): RedirectResponse
    {
        $this->assertOwns($request, $socialLink->professional_id);
        $data = $this->validated($request, $socialLink->professional_id, $socialLink->id);
        $socialLink->update([
            ...$data,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'social'])
            ->with('success', __('frontend.psych_profile.social_updated'));
    }

    public function destroy(Request $request, ProfessionalSocialLink $socialLink): RedirectResponse
    {
        $this->assertOwns($request, $socialLink->professional_id);
        $socialLink->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'social'])
            ->with('success', __('frontend.psych_profile.social_deleted'));
    }

    protected function validated(Request $request, int $professionalId, ?int $ignoreId = null): array
    {
        return $request->validate([
            'platform' => [
                'required',
                'string',
                'max:100',
                Rule::unique('professional_social_links', 'platform')
                    ->where(fn ($q) => $q->where('professional_id', $professionalId))
                    ->ignore($ignoreId),
            ],
            'url' => ['required', 'url', 'max:255'],
            'status' => ['nullable', 'boolean'],
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
