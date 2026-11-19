<?php

namespace App\Services;

use App\Models\Absence;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Patient;
use App\Models\PatientPsychologistRelationship;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(protected NotificationService $notifications) {}

    /**
     * @param  array{appointment_date: string, appointment_time: string, duration_minutes: int, consultation_type: string, notes?: ?string}  $data
     */
    public function book(
        User $actor,
        Patient $patient,
        Professional $professional,
        PatientPsychologistRelationship $relationship,
        array $data,
        bool $confirmImmediately = false,
    ): Appointment {
        $start = Carbon::parse($data['appointment_date'].' '.$data['appointment_time']);
        $duration = (int) $data['duration_minutes'];
        $this->assertBookable($professional, $patient, $start, $duration);

        $patient->loadMissing('user');
        $appointment = Appointment::query()->create([
            'professional_id' => $professional->id,
            'patient_id' => $patient->id,
            'relationship_id' => $relationship->id,
            'client_name' => $patient->user?->name ?: 'Patient',
            'client_email' => $patient->user?->email,
            'appointment_date' => $start->toDateString(),
            'appointment_time' => $start->format('H:i:s'),
            'duration_minutes' => $duration,
            'requested_by' => $actor->id,
            'consultation_type' => $data['consultation_type'],
            'status' => $confirmImmediately ? Appointment::STATUS_CONFIRMED : Appointment::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->notifyCounterpart($appointment, $actor);

        return $appointment;
    }

    public function confirm(Appointment $appointment, User $psychologist): Appointment
    {
        $this->assertPsychologist($appointment, $psychologist);
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'appointment' => __('frontend.appointments.not_pending'),
            ]);
        }

        $appointment->update(['status' => Appointment::STATUS_CONFIRMED]);
        $this->notifyPatient($appointment, 'confirmed');

        return $appointment;
    }

    public function decline(Appointment $appointment, User $psychologist, ?string $reason = null): Appointment
    {
        $this->assertPsychologist($appointment, $psychologist);

        return $this->close($appointment, $psychologist, Appointment::STATUS_CANCELLED, $reason, 'declined');
    }

    public function cancel(Appointment $appointment, User $actor, ?string $reason = null): Appointment
    {
        $this->assertParticipant($appointment, $actor);
        if (! $appointment->isOpen() || $appointment->startsAt()->isPast()) {
            throw ValidationException::withMessages([
                'appointment' => __('frontend.appointments.cannot_cancel'),
            ]);
        }

        return $this->close($appointment, $actor, Appointment::STATUS_CANCELLED, $reason, 'cancelled');
    }

    public function complete(Appointment $appointment, User $psychologist): Appointment
    {
        $this->assertPsychologist($appointment, $psychologist);
        if ($appointment->status !== Appointment::STATUS_CONFIRMED || $appointment->startsAt()->isFuture()) {
            throw ValidationException::withMessages([
                'appointment' => __('frontend.appointments.cannot_complete'),
            ]);
        }

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);
        $this->notifyPatient($appointment, 'completed');

        return $appointment;
    }

    public function currentRelationship(Patient $patient): ?PatientPsychologistRelationship
    {
        return app(PatientPsychologistRelationshipService::class)
            ->getCurrentRelationshipForPatient($patient);
    }

    /**
     * @return Collection<int, PatientPsychologistRelationship>
     */
    public function activePatients(int $professionalId): Collection
    {
        return PatientPsychologistRelationship::query()
            ->with('patient.user')
            ->where('psychologist_id', $professionalId)
            ->whereIn('status', [
                PatientPsychologistRelationship::STATUS_ACTIVE,
                PatientPsychologistRelationship::STATUS_ACCEPTED,
            ])
            ->orderByDesc('id')
            ->get();
    }

    protected function assertBookable(Professional $professional, Patient $patient, Carbon $start, int $duration): void
    {
        if ($start->lte(now())) {
            throw ValidationException::withMessages([
                'appointment_time' => __('frontend.appointments.must_be_future'),
            ]);
        }

        if ($start->gt(now()->addDays(90))) {
            throw ValidationException::withMessages([
                'appointment_date' => __('frontend.appointments.too_far'),
            ]);
        }

        $end = $start->copy()->addMinutes($duration);
        $day = $start->copy()->locale('en')->dayName;
        $startMinutes = ($start->hour * 60) + $start->minute;
        $endMinutes = $startMinutes + $duration;

        $slots = Availability::query()
            ->where('professional_id', $professional->id)
            ->where('day', $day)
            ->where('is_available', true)
            ->get();

        $fits = $slots->contains(function (Availability $slot) use ($startMinutes, $endMinutes) {
            $slotStart = $this->minutes(substr((string) $slot->start_time, 0, 8));
            $slotEnd = $this->minutes(substr((string) $slot->end_time, 0, 8));

            return $startMinutes >= $slotStart && $endMinutes <= $slotEnd;
        });

        if (! $fits) {
            throw ValidationException::withMessages([
                'appointment_time' => __('frontend.appointments.outside_hours'),
            ]);
        }

        $away = Absence::query()
            ->where('professional_id', $professional->id)
            ->where('is_active', true)
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->exists();

        if ($away) {
            throw ValidationException::withMessages([
                'appointment_date' => __('frontend.appointments.psychologist_away'),
            ]);
        }

        $busy = Appointment::query()
            ->where('professional_id', $professional->id)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->whereDate('appointment_date', $start->toDateString())
            ->get()
            ->contains(fn (Appointment $appointment) => $start->lt($appointment->endsAt()) && $end->gt($appointment->startsAt()));

        if ($busy) {
            throw ValidationException::withMessages([
                'appointment_time' => __('frontend.appointments.slot_taken'),
            ]);
        }

        $patientBusy = Appointment::query()
            ->where('patient_id', $patient->id)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->whereDate('appointment_date', $start->toDateString())
            ->get()
            ->contains(fn (Appointment $appointment) => $start->lt($appointment->endsAt()) && $end->gt($appointment->startsAt()));

        if ($patientBusy) {
            throw ValidationException::withMessages([
                'appointment_time' => __('frontend.appointments.patient_busy'),
            ]);
        }
    }

    protected function close(Appointment $appointment, User $actor, string $status, ?string $reason, string $event): Appointment
    {
        if ($appointment->status !== Appointment::STATUS_PENDING && $event === 'declined') {
            throw ValidationException::withMessages([
                'appointment' => __('frontend.appointments.not_pending'),
            ]);
        }

        $appointment->update([
            'status' => $status,
            'cancelled_by' => $actor->id,
            'cancel_reason' => $reason,
        ]);

        $appointment->loadMissing(['patient.user', 'professional.user']);
        $recipient = (int) $actor->id === (int) $appointment->professional?->user_id
            ? $appointment->patient?->user
            : $appointment->professional?->user;

        if ($recipient) {
            $this->notifications->send(
                $recipient,
                'appointment',
                __('frontend.appointments.notify.'.$event.'_title'),
                __('frontend.appointments.notify.'.$event.'_body', [
                    'name' => $actor->name,
                    'when' => $appointment->startsAt()->format('d M Y H:i'),
                ]),
                ['appointment_id' => $appointment->id],
                'appointment-'.$appointment->id.'-'.$event,
            );
        }

        return $appointment;
    }

    protected function notifyCounterpart(Appointment $appointment, User $actor): void
    {
        $appointment->loadMissing(['patient.user', 'professional.user']);
        $recipient = $actor->isPatient()
            ? $appointment->professional?->user
            : $appointment->patient?->user;

        if (! $recipient) {
            return;
        }

        $event = $appointment->status === Appointment::STATUS_CONFIRMED ? 'scheduled' : 'requested';
        $this->notifications->send(
            $recipient,
            'appointment',
            __('frontend.appointments.notify.'.$event.'_title'),
            __('frontend.appointments.notify.'.$event.'_body', [
                'name' => $actor->name,
                'when' => $appointment->startsAt()->format('d M Y H:i'),
            ]),
            ['appointment_id' => $appointment->id],
            'appointment-'.$appointment->id.'-'.$event,
        );
    }

    protected function notifyPatient(Appointment $appointment, string $event): void
    {
        $appointment->loadMissing(['patient.user', 'professional.user']);
        $patientUser = $appointment->patient?->user;
        if (! $patientUser) {
            return;
        }

        $this->notifications->send(
            $patientUser,
            'appointment',
            __('frontend.appointments.notify.'.$event.'_title'),
            __('frontend.appointments.notify.'.$event.'_body', [
                'name' => $appointment->professional?->user?->name ?: __('frontend.chat.unknown'),
                'when' => $appointment->startsAt()->format('d M Y H:i'),
            ]),
            ['appointment_id' => $appointment->id],
            'appointment-'.$appointment->id.'-'.$event,
        );
    }

    protected function assertPsychologist(Appointment $appointment, User $user): void
    {
        $user->loadMissing(['psychologist', 'professional']);
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;
        if (! $user->isPsychologist() || (int) $appointment->professional_id !== (int) $professionalId) {
            abort(403);
        }
    }

    protected function assertParticipant(Appointment $appointment, User $user): void
    {
        $user->loadMissing(['patient', 'psychologist', 'professional']);
        $ownsPatient = $user->isPatient() && (int) $appointment->patient_id === (int) $user->patient?->id;
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;
        $ownsProfessional = $user->isPsychologist() && (int) $appointment->professional_id === (int) $professionalId;

        if (! $ownsPatient && ! $ownsProfessional) {
            abort(403);
        }
    }

    protected function minutes(string $time): int
    {
        [$hour, $minute] = array_pad(explode(':', $time), 2, 0);

        return ((int) $hour * 60) + (int) $minute;
    }
}
