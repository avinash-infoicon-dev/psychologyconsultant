<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\PsychologistDirectory\ListPsychologistsRequest;
use App\Models\Professional;
use App\Services\PsychologistDirectoryService;
use Illuminate\View\View;
use App\Models\PatientPsychologistRelationship;

class PsychologistDirectoryController extends Controller
{
    public function __construct(protected PsychologistDirectoryService $directory) {}

    public function index(ListPsychologistsRequest $request): View
    {
        // Legacy variable:
        // $psychologists = $this->directory->paginate($request->validated());
        $professionals = $this->directory->paginate($request->validated());

        // dd(compact('professionals'));

        return view('frontend.patient.psychologists.index', compact('professionals'));
    }

    public function show(Professional $professional): View
    {
        // Legacy: $professional = $this->directory->findForDirectory($professional);
        $patient = request()->user()?->patient;
        $view = $this->directory->viewForPatient($professional, $patient);
        $professional = $view['professional'];
        $careStatus = $view['care_status'];
        $profileMode = $view['profile_mode'];

        $relationship = null;
        if ($patient) {
            $relationship = PatientPsychologistRelationship::query()
                ->where('patient_id', $patient->id)
                ->where('psychologist_id', $professional->id)
                ->latest('id')
                ->first();
        }

        return view('frontend.patient.psychologists.show', compact(
            'professional',
            'relationship',
            'careStatus',
            'profileMode',
        ));
    }
}
