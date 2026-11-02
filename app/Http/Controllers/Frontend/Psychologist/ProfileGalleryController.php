<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ProfessionalGallery;
use App\Services\ProfessionalMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileGalleryController extends Controller
{
    use ResolvesProfessional;

    public function __construct(protected ProfessionalMediaService $media) {}

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['required', 'image', 'max:6144'],
        ]);

        ProfessionalGallery::query()->create([
            'professional_id' => $professional->id,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => true,
            'image' => $this->media->storePublicImage($request->file('image'), $professional->id, 'gallery'),
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'gallery'])
            ->with('success', __('frontend.psych_profile.gallery_created'));
    }

    public function destroy(Request $request, ProfessionalGallery $gallery): RedirectResponse
    {
        $this->assertOwns($request, $gallery->professional_id);
        $this->media->deletePublicPath($gallery->image);
        $gallery->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'gallery'])
            ->with('success', __('frontend.psych_profile.gallery_deleted'));
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
