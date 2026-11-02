<?php

namespace App\Models\Concerns;

trait HasVerificationStatus
{
    public const VERIFICATION_DRAFT = 'draft';

    public const VERIFICATION_SUBMITTED = 'submitted';

    public const VERIFICATION_UNDER_REVIEW = 'under_review';

    public const VERIFICATION_VERIFIED = 'verified';

    public const VERIFICATION_REJECTED = 'rejected';

    public const VERIFICATION_SUSPENDED = 'suspended';

    public const VERIFICATION_STATUSES = [
        self::VERIFICATION_DRAFT,
        self::VERIFICATION_SUBMITTED,
        self::VERIFICATION_UNDER_REVIEW,
        self::VERIFICATION_VERIFIED,
        self::VERIFICATION_REJECTED,
        self::VERIFICATION_SUSPENDED,
    ];

    public function isVerificationSuspended(): bool
    {
        return ($this->verification_status ?? '') === self::VERIFICATION_SUSPENDED;
    }

    public function isListedForPatients(): bool
    {
        // Old: verified + professional status only (ignored users.is_active).
        // return ($this->verification_status ?? '') === self::VERIFICATION_VERIFIED
        //     && (bool) $this->is_verified
        //     && (bool) $this->status;

        return ($this->verification_status ?? '') === self::VERIFICATION_VERIFIED
            && (bool) $this->is_verified
            && (bool) $this->status
            && (bool) $this->user?->is_active
            && ! $this->isVerificationSuspended();
    }

    public function canSubmitForVerification(): bool
    {
        return in_array(
            $this->verification_status,
            [self::VERIFICATION_DRAFT, self::VERIFICATION_REJECTED],
            true
        );
    }

    public function scopeListedForPatients($query)
    {
        // Old: ->where('is_verified', true)->where('status', true)->where('verification_status', verified)
        return $query
            ->where('is_verified', true)
            ->where('status', true)
            ->where('verification_status', self::VERIFICATION_VERIFIED)
            ->whereHas('user', fn ($user) => $user->where('is_active', true));
    }
}
