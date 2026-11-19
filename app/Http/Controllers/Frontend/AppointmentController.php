<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Psychologist\ResolvesProfessional;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\PatientPsychologistRelationship;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    use ResolvesProfessional;

    public function __construct(protected AppointmentService $appointments) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $isPsychologist = $user->isPsychologist();
        $relationship = null;
        $patients = collect();
        $slots = collect();
        $types = [Appointment::TYPE_ONLINE];

        if ($isPsychologist) {
            $professionalId = $this->professionalId($user) ?: 0;
            $rows = Appointment::query()
                ->with(['patient.user', 'professional.user'])
                ->where('professional_id', $professionalId)
                ->whereNotNull('patient_id')
                ->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time')
                ->get();
            $patients = $this->appointments->activePatients($professionalId);
            $professional = $this->professional($user);
        } else {
            $patient = $user->patient;
            abort_unless($patient, 403);
            $rows = Appointment::query()
                ->with(['patient.user', 'professional.user'])
                ->where('patient_id', $patient->id)
                ->orderByDesc('appointment_date')
                ->orderByDesc('appointment_time')
                ->get();
            $relationship = $this->appointments->currentRelationship($patient);
            $professional = $relationship?->psychologist;
        }

        if ($professional) {
            $slots = Availability::query()
                ->where('professional_id', $professional->id)
                ->where('is_available', true)
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->orderBy('start_time')
                ->get();
            $types = array_values(array_filter([
                $professional->online_consultation ? Appointment::TYPE_ONLINE : null,
                $professional->offline_consultation ? Appointment::TYPE_OFFLINE : null,
            ])) ?: [Appointment::TYPE_ONLINE];
        }

        return view('frontend.appointments.index', [
            'isPsychologist' => $isPsychologist,
            'upcoming' => $rows->filter->isUpcoming()->sortBy(fn (Appointment $appointment) => $appointment->startsAt()->timestamp)->values(),
            'history' => $rows->reject->isUpcoming()->values(),
            'relationship' => $relationship,
            'patients' => $patients,
            'slots' => $slots,
            'types' => $types,
            'routePrefix' => $isPsychologist ? 'frontend.psychologist' : 'frontend.patient',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'patient_id' => [$user->isPsychologist() ? 'required' : 'nullable', 'integer'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', Rule::in(Appointment::DURATIONS)],
            'consultation_type' => ['required', Rule::in([Appointment::TYPE_ONLINE, Appointment::TYPE_OFFLINE])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($user->isPsychologist()) {
            $professional = $this->professional($user);
            abort_unless($professional, 403);
            $relationship = PatientPsychologistRelationship::query()
                ->where('psychologist_id', $professional->id)
                ->where('patient_id', $data['patient_id'])
                ->whereIn('status', [
                    PatientPsychologistRelationship::STATUS_ACTIVE,
                    PatientPsychologistRelationship::STATUS_ACCEPTED,
                ])
                ->first();
            abort_unless($relationship?->patient, 403);
            $this->appointments->book($user, $relationship->patient, $professional, $relationship, $data, true);
        } else {
            $patient = $user->patient;
            abort_unless($patient, 403);
            $relationship = $this->appointments->currentRelationship($patient);
            abort_unless($relationship?->psychologist, 403);
            $this->appointments->book($user, $patient, $relationship->psychologist, $relationship, $data, false);
        }

        return redirect()
            ->route(($user->isPsychologist() ? 'frontend.psychologist' : 'frontend.patient').'.appointments.index')
            ->with('success', __('frontend.appointments.saved'));
    }

    public function confirm(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->appointments->confirm($appointment, $request->user());

        return back()->with('success', __('frontend.appointments.confirmed'));
    }

    public function decline(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate(['cancel_reason' => ['nullable', 'string', 'max:255']]);
        $this->appointments->decline($appointment, $request->user(), $data['cancel_reason'] ?? null);

        return back()->with('success', __('frontend.appointments.declined'));
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate(['cancel_reason' => ['nullable', 'string', 'max:255']]);
        $this->appointments->cancel($appointment, $request->user(), $data['cancel_reason'] ?? null);

        return back()->with('success', __('frontend.appointments.cancelled'));
    }

    public function complete(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->appointments->complete($appointment, $request->user());

        return back()->with('success', __('frontend.appointments.completed'));
    }
}
