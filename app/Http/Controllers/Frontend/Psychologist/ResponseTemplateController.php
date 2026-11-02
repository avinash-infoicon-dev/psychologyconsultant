<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\ResponseTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResponseTemplateController extends Controller
{
    use ResolvesProfessional;

    public function index(Request $request): View
    {
        $professionalId = $this->professionalId($request->user()) ?: 0;

        $templates = ResponseTemplate::query()
            ->where('professional_id', $professionalId)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(20);

        return view('frontend.psychologist.templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('frontend.psychologist.templates.form', ['template' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $professionalId = $this->professionalId($request->user());
        abort_unless($professionalId, 403);

        ResponseTemplate::query()->create([
            ...$data,
            'professional_id' => $professionalId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('frontend.psychologist.templates.index')
            ->with('success', __('frontend.templates.created'));
    }

    public function edit(Request $request, ResponseTemplate $template): View
    {
        $this->assertOwns($request, $template);

        return view('frontend.psychologist.templates.form', compact('template'));
    }

    public function update(Request $request, ResponseTemplate $template): RedirectResponse
    {
        $this->assertOwns($request, $template);
        $data = $this->validated($request);

        $template->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('frontend.psychologist.templates.index')
            ->with('success', __('frontend.templates.updated'));
    }

    public function destroy(Request $request, ResponseTemplate $template): RedirectResponse
    {
        $this->assertOwns($request, $template);
        $template->delete();

        return redirect()
            ->route('frontend.psychologist.templates.index')
            ->with('success', __('frontend.templates.deleted'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function assertOwns(Request $request, ResponseTemplate $template): void
    {
        abort_unless(
            (int) $template->professional_id === (int) $this->professionalId($request->user()),
            403
        );
    }
}
