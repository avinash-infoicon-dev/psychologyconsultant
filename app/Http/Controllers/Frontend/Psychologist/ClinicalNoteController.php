<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClinicalNoteRequest;
use App\Models\ClinicalNote;
use App\Models\Conversation;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalNoteController extends Controller
{
    use ResolvesProfessional;

    public function index(Request $request, Patient $patient): View
    {
        $this->authorize('view', $patient);

        $notes = $patient->clinicalNotes()->latest()->paginate(20);
        $patient->load('user');

        return view('frontend.psychologist.clinical-notes.index', [
            'patient' => $patient,
            'notes' => $notes,
            'conversation' => null,
        ]);
    }

    public function create(Request $request, Patient $patient): View
    {
        $this->authorize('create', [ClinicalNote::class, $patient]);

        $patient->load('user');
        $conversationId = $request->integer('conversation_id') ?: null;

        return view('frontend.psychologist.clinical-notes.form', [
            'patient' => $patient,
            'note' => null,
            'conversationId' => $conversationId,
        ]);
    }

    public function store(ClinicalNoteRequest $request, Patient $patient): RedirectResponse
    {
        $this->authorize('create', [ClinicalNote::class, $patient]);

        $professionalId = $this->professionalId($request->user());
        abort_unless($professionalId, 403);

        // Mirror API ClinicalNoteController@store (kept for web session auth).
        $note = $patient->clinicalNotes()->create([
            'psychologist_id' => $professionalId,
            'conversation_id' => $request->input('conversation_id'),
            'content' => $request->string('content'),
            'editable_until' => now()->addHours(config('healthcare.clinical_notes.editable_within_hours', 48)),
        ]);

        return redirect()
            ->route('frontend.psychologist.patients.clinical-notes.index', $patient)
            ->with('success', __('frontend.clinical_notes.created'));
    }

    public function edit(Request $request, ClinicalNote $clinicalNote): View|RedirectResponse
    {
        $this->authorize('update', $clinicalNote);

        $clinicalNote->load('patient.user');

        return view('frontend.psychologist.clinical-notes.form', [
            'patient' => $clinicalNote->patient,
            'note' => $clinicalNote,
            'conversationId' => $clinicalNote->conversation_id,
        ]);
    }

    public function update(ClinicalNoteRequest $request, ClinicalNote $clinicalNote): RedirectResponse
    {
        $this->authorize('update', $clinicalNote);

        $clinicalNote->update(['content' => $request->string('content')]);

        return redirect()
            ->route('frontend.psychologist.patients.clinical-notes.index', $clinicalNote->patient)
            ->with('success', __('frontend.clinical_notes.updated'));
    }

    /** Notes scoped to the open conversation (from chat header). */
    public function forConversation(Request $request, Conversation $conversation): View
    {
        $conversation->load(['patient.user']);
        $this->authorize('view', $conversation->patient);

        $notes = ClinicalNote::query()
            ->where('patient_id', $conversation->patient_id)
            ->where(function ($q) use ($conversation) {
                $q->where('conversation_id', $conversation->id)
                    ->orWhereNull('conversation_id');
            })
            ->latest()
            ->paginate(20);

        return view('frontend.psychologist.clinical-notes.index', [
            'patient' => $conversation->patient,
            'notes' => $notes,
            'conversation' => $conversation,
        ]);
    }
}
