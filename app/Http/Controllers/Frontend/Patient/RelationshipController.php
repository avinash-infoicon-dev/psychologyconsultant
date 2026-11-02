<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\PatientPsychologistRelationship;
use App\Services\PatientPsychologistRelationshipService;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function __construct(protected PatientPsychologistRelationshipService $relationshipService) {}

    public function store(Request $request)
    {
        $request->validate([
            'psychologist_id' => 'required|exists:professionals,id'
        ]);

        $psychologist = Professional::findOrFail($request->psychologist_id);
        $patient = $request->user()->patient;

        if (!$patient) {
            return back()->with('error', 'Patient profile not found.');
        }

        if (! \App\Support\PsychologistCareStatus::allowsMessaging($psychologist)) {
            return back()->with('error', __('frontend.chat.psychologist_unavailable'));
        }

        $this->relationshipService->requestConsultation($patient, $psychologist);

        return back()->with('success', __('relationship.request_sent'));
    }

    public function close(Request $request, PatientPsychologistRelationship $relationship)
    {
        if ($relationship->patient_id !== $request->user()->patient->id) {
            abort(403);
        }

        $this->relationshipService->closeRelationship($relationship, $request->user());

        return back()->with('success', __('relationship.closed'));
    }

    public function activate(Request $request, PatientPsychologistRelationship $relationship)
    {
        if ($relationship->patient_id !== $request->user()->patient->id) {
            abort(403);
        }

        // // Optional: Check for subscription/payment here.
        // $patient = $request->user()->patient;
        // if (!$patient->activeSubscription() && $patient->creditBalance() <= 0) {
        //     return redirect()->route('frontend.patient.subscription.index')
        //         ->with('error', 'Please subscribe or add credits to start chatting.');
        // }

        $this->relationshipService->markAsActive($relationship);

        return back()->with('success', __('relationship.status_active'));
    }
}
