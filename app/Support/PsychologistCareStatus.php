<?php

namespace App\Support;

use App\Models\Conversation;
use App\Models\Professional;
use App\Models\Psychologist;

/**
 * Patient-facing psychologist availability.
 *
 * Active: profile, history, send.
 * Inactive: profile (limited CTAs), history, no send.
 * Suspended: name / relationship only, history, no send.
 * Removed: no public profile, history, no send.
 */
class PsychologistCareStatus
{
    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    public const SUSPENDED = 'suspended';

    public const REMOVED = 'removed';

    public static function for(Professional|Psychologist|null $professional): string
    {
        if (! $professional) {
            return self::REMOVED;
        }

        if (method_exists($professional, 'trashed') && $professional->trashed()) {
            return self::REMOVED;
        }

        $professional->loadMissing('user');
        $user = $professional->user;

        if (! $user || (method_exists($user, 'trashed') && $user->trashed())) {
            return self::REMOVED;
        }

        if ($professional->isVerificationSuspended()) {
            return self::SUSPENDED;
        }

        if (! $user->is_active || $professional->status === false) {
            return self::INACTIVE;
        }

        return self::ACTIVE;
    }

    public static function forConversation(?Conversation $conversation): string
    {
        if (! $conversation) {
            return self::REMOVED;
        }

        $conversation->loadMissing('psychologist.user');

        return self::for($conversation->psychologist);
    }

    public static function allowsMessaging(Professional|Psychologist|null $professional): bool
    {
        return self::for($professional) === self::ACTIVE;
    }

    public static function allowsPublicProfile(Professional|Psychologist|null $professional): bool
    {
        $status = self::for($professional);

        return $status === self::ACTIVE || $status === self::INACTIVE;
    }

    public static function allowsLimitedProfile(Professional|Psychologist|null $professional): bool
    {
        $status = self::for($professional);

        return in_array($status, [self::ACTIVE, self::INACTIVE, self::SUSPENDED], true);
    }

    public static function messagingPaused(Professional|Psychologist|null $professional): bool
    {
        return ! self::allowsMessaging($professional);
    }
}
