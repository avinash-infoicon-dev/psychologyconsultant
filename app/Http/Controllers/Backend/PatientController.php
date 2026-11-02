<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        // /$patients = Patient::query()
        return view('backend.patients.index', [
            'patients' => $this->filteredPatients($request),
        ]);
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'user.roles',
            'psychologist.user',
            // Moved to dedicated pages:
            // 'subscriptions' => fn ($q) => $q->with('plan')->latest()->limit(20),
            // 'conversations' => fn ($q) => $q->with('psychologist.user')->latest('last_message_at')->limit(20),
            // 'clinicalNotes' => fn ($q) => $q->latest()->limit(20),
            // 'credits' => fn ($q) => $q->latest()->limit(30),
        ]);

        return view('backend.patients.show', [
            'patient' => $patient,
            'creditBalance' => $patient->creditBalance(),
            'activeSubscription' => $patient->activeSubscription(),
        ]);
    }

    public function subscriptionsIndex(Request $request): View
    {
        return $this->sectionIndex($request, 'subscriptions', 'admin.patients.subscriptions');
    }

    public function conversationsIndex(Request $request): View
    {
        return $this->sectionIndex($request, 'conversations', 'admin.patients.conversations');
    }

    public function exchangesIndex(Request $request): View
    {
        return $this->sectionIndex($request, 'credits', 'admin.patients.exchanges');
    }

    public function notesIndex(Request $request): View
    {
        return $this->sectionIndex($request, 'notes', 'admin.patients.notes');
    }

    public function subscriptions(Patient $patient): View
    {
        $patient->load(['user', 'subscriptions' => fn ($q) => $q->with('plan')->latest()]);

        return view('backend.patients.subscriptions', [
            'patient' => $patient,
        ]);
    }

    public function conversations(Patient $patient): View
    {
        $patient->load(['user']);
        $conversations = $patient->conversations()
            ->with('psychologist.user')
            ->latest('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view('backend.patients.conversations', compact('patient', 'conversations'));
    }

    public function notes(Patient $patient): View
    {
        $patient->load(['user']);

        // Old:
        // $clinicalNotes = $patient->clinicalNotes()->latest()->paginate(20)->withQueryString();

        $clinicalNotes = \App\Models\ClinicalNote::query()
            ->with(['psychologist.user'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('backend.patients.notes', compact('patient', 'clinicalNotes'));
    }

    protected function sectionIndex(Request $request, string $section, string $actionRoute): View
    {
        return view('backend.patients.section-index', [
            'patients' => $this->filteredPatients($request),
            'pageTitle' => __('backend.patients.section_'.$section),
            'actionRoute' => $actionRoute,
            'actionLabel' => __('backend.patients.section_'.$section),
        ]);
    }

    protected function filteredPatients(Request $request)
    {
        return Patient::query()
            ->with(['user', 'psychologist.user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->whereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', $q)
                            ->orWhere('email', 'like', $q)
                            ->orWhere('phone', 'like', $q);
                    })
                        ->orWhere('city', 'like', $q)
                        ->orWhere('country', 'like', $q);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    // return view('backend.patients.index', compact('patients'));
    // }

    // public function show(Patient $patient): View
    // {
    //     $patient->load([
    //         'user.roles',
    //         'psychologist.user',
    //         'subscriptions' => fn ($q) => $q->with('plan')->latest()->limit(20),
    //         'conversations' => fn ($q) => $q->with('psychologist.user')->latest('last_message_at')->limit(20),
    //         'clinicalNotes' => fn ($q) => $q->latest()->limit(20),
    //         'credits' => fn ($q) => $q->latest()->limit(30),
    //     ]);

    //     return view('backend.patients.show', [
    //         'patient' => $patient,
    //         'creditBalance' => $patient->creditBalance(),
    //         'activeSubscription' => $patient->activeSubscription(),
    //     ]);
}
