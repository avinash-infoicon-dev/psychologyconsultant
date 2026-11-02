<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ProfessionalDocument;
use App\Services\ProfessionalMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileDocumentController extends Controller
{
    use ResolvesProfessional;

    public function __construct(protected ProfessionalMediaService $media) {}

    public function store(Request $request): RedirectResponse
    {
        $professional = $this->requireProfessional($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['nullable', 'string', 'max:100'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ]);

        $path = $this->media->storePrivateDocument($request->file('document'), $professional->id);

        ProfessionalDocument::query()->create([
            'professional_id' => $professional->id,
            'title' => $data['title'],
            'document_type' => $data['document_type'] ?? null,
            'document' => $path,
            'is_verified' => false,
            'status' => true,
        ]);

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'documents'])
            ->with('success', __('frontend.psych_profile.document_created'));
    }

    public function destroy(Request $request, ProfessionalDocument $document): RedirectResponse
    {
        $this->assertOwns($request, $document->professional_id);
        $this->media->deletePrivatePath($document->document);
        $document->delete();

        return redirect()
            ->route('frontend.psychologist.profile.show', ['tab' => 'documents'])
            ->with('success', __('frontend.psych_profile.document_deleted'));
    }

    public function download(Request $request, ProfessionalDocument $document): StreamedResponse
    {
        $this->assertOwns($request, $document->professional_id);

        abort_unless(
            $document->document && \Illuminate\Support\Facades\Storage::disk('private')->exists($document->document),
            404
        );

        return \Illuminate\Support\Facades\Storage::disk('private')->download(
            $document->document,
            basename($document->document)
        );
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
