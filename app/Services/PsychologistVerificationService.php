<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Professional;
use App\Models\Psychologist;
use App\Notifications\AccountStatusNotification;
use App\Notifications\AdminAlertNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PsychologistVerificationService
{
    public function __construct(protected NotificationService $notifications) {}

    public function professionalFrom(Psychologist|Professional $profile): Professional
    {
        if ($profile instanceof Professional) {
            return $profile;
        }

        return Professional::query()->whereKey($profile->getKey())->firstOrFail();
    }

    public function submit(Psychologist|Professional $profile): Professional
    {
        $professional = $this->professionalFrom($profile);

        if (! $professional->canSubmitForVerification()) {
            throw ValidationException::withMessages([
                'verification_status' => __('frontend.psych_profile.verification_cannot_submit'),
            ]);
        }

        $this->assertReadyToSubmit($professional);

        return DB::transaction(function () use ($professional) {
            $this->applyStatus($professional, Professional::VERIFICATION_SUBMITTED);
            ActivityLog::record('psychologist.submitted', $professional);
            $this->notifyAdminsOfSubmission($professional);

            return $professional->fresh();
        });
    }

    public function startReview(Psychologist|Professional $profile): Professional
    {
        $professional = $this->professionalFrom($profile);
        $this->assertStatus($professional, Professional::VERIFICATION_SUBMITTED);

        return DB::transaction(function () use ($professional) {
            $this->applyStatus($professional, Professional::VERIFICATION_UNDER_REVIEW);
            ActivityLog::record('admin.psychologist.review_started', $professional);
            $this->notifyPsychologist($professional, AccountStatusNotification::underReview());

            return $professional->fresh();
        });
    }

    public function verify(Psychologist|Professional $profile): Professional
    {
        $professional = $this->professionalFrom($profile);
        $this->assertStatus($professional, Professional::VERIFICATION_UNDER_REVIEW);

        return DB::transaction(function () use ($professional) {
            $this->applyStatus($professional, Professional::VERIFICATION_VERIFIED);
            ActivityLog::record('admin.psychologist.verified', $professional);
            $this->notifyPsychologist($professional, AccountStatusNotification::profileApproved());

            return $professional->fresh();
        });
    }

    public function reject(Psychologist|Professional $profile, string $reason): Professional
    {
        $professional = $this->professionalFrom($profile);
        $this->assertStatus($professional, Professional::VERIFICATION_UNDER_REVIEW);
        $reason = $this->requireReason($reason);

        return DB::transaction(function () use ($professional, $reason) {
            $this->applyStatus($professional, Professional::VERIFICATION_REJECTED, $reason);
            ActivityLog::record('admin.psychologist.rejected', $professional, ['reason' => $reason]);
            $this->notifyPsychologist($professional, AccountStatusNotification::profileRejected($reason));

            return $professional->fresh();
        });
    }

    public function suspend(Psychologist|Professional $profile, string $reason): Professional
    {
        $professional = $this->professionalFrom($profile);
        $this->assertStatus($professional, Professional::VERIFICATION_VERIFIED);
        $reason = $this->requireReason($reason);

        return DB::transaction(function () use ($professional, $reason) {
            $this->applyStatus($professional, Professional::VERIFICATION_SUSPENDED, $reason);
            ActivityLog::record('admin.psychologist.suspended', $professional, ['reason' => $reason]);
            // Do not notify the psychologist of suspend/restore or active/inactive.
            // $this->notifyPsychologist($professional, AccountStatusNotification::suspended($reason));

            return $professional->fresh();
        });
    }

    public function restore(Psychologist|Professional $profile): Professional
    {
        $professional = $this->professionalFrom($profile);
        $this->assertStatus($professional, Professional::VERIFICATION_SUSPENDED);

        return DB::transaction(function () use ($professional) {
            $this->applyStatus($professional, Professional::VERIFICATION_VERIFIED);
            ActivityLog::record('admin.psychologist.restored', $professional);
            // Do not notify the psychologist of suspend/restore or active/inactive.
            // $this->notifyPsychologist($professional, AccountStatusNotification::restored());

            return $professional->fresh();
        });
    }

    public function verificationHistory(Professional $professional, int $limit = 30)
    {
        return ActivityLog::query()
            ->with('user')
            ->where('subject_id', $professional->id)
            ->whereIn('subject_type', [Professional::class, Psychologist::class])
            ->where(function ($query) {
                $query->where('action', 'like', 'admin.psychologist.%')
                    ->orWhere('action', 'psychologist.submitted');
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function paginatedVerificationHistory(Professional $professional, int $perPage = 15)
    {
        return ActivityLog::query()
            ->with('user')
            ->where('subject_id', $professional->id)
            ->whereIn('subject_type', [Professional::class, Psychologist::class])
            ->where(function ($query) {
                $query->where('action', 'like', 'admin.psychologist.%')
                    ->orWhere('action', 'psychologist.submitted');
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function assertReadyToSubmit(Professional $professional): void
    {
        $professional->loadMissing(['user', 'specializations', 'educations']);

        $identity = trim(($professional->first_name ?? '').' '.($professional->last_name ?? ''));
        if ($identity === '') {
            $identity = trim((string) $professional->user?->name);
        }

        $hasSpecializations = $professional->specializations->isNotEmpty()
            || filled($professional->specialization);

        if ($identity === '' || ! $hasSpecializations || $professional->educations->isEmpty()) {
            throw ValidationException::withMessages([
                'profile' => __('frontend.psych_profile.verification_incomplete'),
            ]);
        }
    }

    protected function applyStatus(Professional $professional, string $status, ?string $reason = null): void
    {
        $listed = $status === Professional::VERIFICATION_VERIFIED;

        $professional->update([
            'verification_status' => $status,
            'is_verified' => $listed,
            'status' => $listed,
            'is_accepting_patients' => $listed,
            'verification_reason' => in_array($status, [
                Professional::VERIFICATION_REJECTED,
                Professional::VERIFICATION_SUSPENDED,
            ], true) ? $reason : null,
        ]);
    }

    protected function assertStatus(Professional $professional, string $expected): void
    {
        if ($professional->verification_status !== $expected) {
            throw ValidationException::withMessages([
                'verification_status' => __('backend.psychologists.invalid_transition'),
            ]);
        }
    }

    protected function requireReason(string $reason): string
    {
        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => __('backend.psychologists.reason_required'),
            ]);
        }

        return $reason;
    }

    protected function notifyAdminsOfSubmission(Professional $professional): void
    {
        $professional->loadMissing('user');
        $name = $professional->user?->name ?? $professional->name;
        $url = $professional->uuid
            ? route('admin.psychologists.show', $professional->uuid)
            : route('admin.psychologists.index');

        $this->notifications->notifyAdmins(
            AdminAlertNotification::profileReview($name, $url),
            'psych-review-submit:'.$professional->user_id.':'.now()->timestamp
        );
    }

    /**
     * @param  array{type: string, title: string, body: string, data: array<string, mixed>}  $payload
     */
    protected function notifyPsychologist(Professional $professional, array $payload): void
    {
        $professional->loadMissing('user');

        if ($professional->user) {
            $this->notifications->sendPayload($professional->user, $payload);
        }
    }
}
