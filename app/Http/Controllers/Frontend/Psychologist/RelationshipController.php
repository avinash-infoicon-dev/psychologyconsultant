<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\PatientPsychologistRelationship;
use App\Services\PatientPsychologistRelationshipService;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function __construct(protected PatientPsychologistRelationshipService $relationshipService) {}

    public function index(Request $request)
    {
        $psychologist = $request->user()->professional ?? $request->user()->psychologist;

        if (!$psychologist) {
            abort(403);
        }

        $pendingRequests = $this->relationshipService->getPendingRequestsForPsychologist($psychologist);

        return view('frontend.psychologist.relationships.index', compact('pendingRequests'));
    }

    public function accept(Request $request, PatientPsychologistRelationship $relationship)
    {
        $psychologist = $request->user()->professional ?? $request->user()->psychologist;

        if ($relationship->psychologist_id !== $psychologist->id) {
            abort(403);
        }

        $this->relationshipService->acceptRequest($relationship);

        return back()->with('success', __('relationship.accepted'));
    }

    public function reject(Request $request, PatientPsychologistRelationship $relationship)
    {
        $psychologist = $request->user()->professional ?? $request->user()->psychologist;

        if ($relationship->psychologist_id !== $psychologist->id) {
            abort(403);
        }

        $this->relationshipService->rejectRequest(
            $relationship,
            $request->filled('reason') ? (string) $request->input('reason') : null,
        );

        return back()->with('success', __('relationship.rejected'));
    }

    public function close(Request $request, PatientPsychologistRelationship $relationship)
    {
        $psychologist = $request->user()->professional ?? $request->user()->psychologist;

        if ($relationship->psychologist_id !== $psychologist->id) {
            abort(403);
        }

        $this->relationshipService->closeRelationship($relationship, $request->user());

        return back()->with('success', __('relationship.closed'));
    }
}
