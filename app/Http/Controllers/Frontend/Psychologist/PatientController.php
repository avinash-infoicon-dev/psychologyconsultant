<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    use ResolvesProfessional;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Patient::class);

        $professionalId = $this->professionalId($request->user()) ?: 0;

        $patients = Patient::query()
            ->with('user')
            ->where(function ($query) use ($professionalId) {
                $query->where('psychologist_id', $professionalId)
                    ->orWhereHas('conversations', fn ($q) => $q->where('psychologist_id', $professionalId));
            })
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('frontend.psychologist.patients.index', compact('patients'));
    }

    public function show(Request $request, Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load('user');
        $notes = $patient->clinicalNotes()->latest()->paginate(10);
        $conversations = Conversation::query()
            ->where('patient_id', $patient->id)
            ->where('psychologist_id', $this->professionalId($request->user()) ?: 0)
            ->latest('last_message_at')
            ->limit(10)
            ->get();

        return view('frontend.psychologist.patients.show', compact('patient', 'notes', 'conversations'));
    }
}
