<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsenceController extends Controller
{
    use ResolvesProfessional;

    public function index(Request $request): View
    {
        $professionalId = $this->professionalId($request->user()) ?: 0;

        $absences = Absence::query()
            ->where('professional_id', $professionalId)
            ->orderByDesc('starts_at')
            ->paginate(20);

        $currentAbsence = Absence::query()
            ->where('professional_id', $professionalId)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        return view('frontend.psychologist.absences.index', compact('absences', 'currentAbsence'));
    }

    public function create(): View
    {
        return view('frontend.psychologist.absences.form', ['absence' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $professionalId = $this->professionalId($request->user());
        abort_unless($professionalId, 403);

        Absence::query()->create([
            ...$data,
            'professional_id' => $professionalId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('frontend.psychologist.absences.index')
            ->with('success', __('frontend.absences.created'));
    }

    public function edit(Request $request, Absence $absence): View
    {
        $this->assertOwns($request, $absence);

        return view('frontend.psychologist.absences.form', compact('absence'));
    }

    public function update(Request $request, Absence $absence): RedirectResponse
    {
        $this->assertOwns($request, $absence);
        $data = $this->validated($request);

        $absence->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('frontend.psychologist.absences.index')
            ->with('success', __('frontend.absences.updated'));
    }

    public function destroy(Request $request, Absence $absence): RedirectResponse
    {
        $this->assertOwns($request, $absence);
        $absence->delete();

        return redirect()
            ->route('frontend.psychologist.absences.index')
            ->with('success', __('frontend.absences.deleted'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    protected function assertOwns(Request $request, Absence $absence): void
    {
        abort_unless(
            (int) $absence->professional_id === (int) $this->professionalId($request->user()),
            403
        );
    }
}
