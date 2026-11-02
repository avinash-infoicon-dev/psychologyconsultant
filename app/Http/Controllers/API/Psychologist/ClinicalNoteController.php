<?php

namespace App\Http\Controllers\API\Psychologist;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClinicalNoteRequest;
use App\Http\Resources\ClinicalNoteResource;
use App\Models\ClinicalNote;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClinicalNoteController extends Controller
{
    public function index(Request $request, Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        $notes = $patient->clinicalNotes()->latest()->paginate(20);

        return response()->json([
            'data' => ClinicalNoteResource::collection($notes),
            'meta' => [
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'total' => $notes->total(),
            ],
        ]);
    }

    public function store(ClinicalNoteRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('create', [ClinicalNote::class, $patient]);

        $user = $request->user();
        $user->loadMissing(['psychologist', 'professional']);
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;
        abort_unless($professionalId, 403);

        $note = $patient->clinicalNotes()->create([
            //  'psychologist_id' => $request->user()->psychologist->id,
            'psychologist_id' => $professionalId,
            'conversation_id' => $request->input('conversation_id'),
            'content' => $request->string('content'),
            'editable_until' => now()->addHours(config('healthcare.clinical_notes.editable_within_hours', 48)),
        ]);

        return response()->json(['data' => new ClinicalNoteResource($note)], 201);
    }

    public function show(Request $request, ClinicalNote $clinicalNote): JsonResponse
    {
        $this->authorize('view', $clinicalNote);

        return response()->json(['data' => new ClinicalNoteResource($clinicalNote)]);
    }

    public function update(ClinicalNoteRequest $request, ClinicalNote $clinicalNote): JsonResponse
    {
        $this->authorize('update', $clinicalNote);

        $clinicalNote->update(['content' => $request->string('content')]);

        return response()->json(['data' => new ClinicalNoteResource($clinicalNote->fresh())]);
    }
}
