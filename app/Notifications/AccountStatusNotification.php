<?php

namespace App\Notifications;

class AccountStatusNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function changed(string $status, ?string $url = null): array
    {
        return [
            'type' => 'account_status',
            'title' => __('frontend.notifications.account_status_title'),
            'body' => __('frontend.notifications.account_status_body', ['status' => $status]),
            'data' => [
                'status' => $status,
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function profileApproved(?string $url = null): array
    {
        return [
            'type' => 'profile_approved',
            'title' => __('frontend.notifications.profile_approved_title'),
            'body' => __('frontend.notifications.profile_approved_body'),
            'data' => [
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function profileRejected(?string $reason = null, ?string $url = null): array
    {
        $body = __('frontend.notifications.profile_rejected_body');
        if ($reason) {
            $body .= ' '.__('frontend.notifications.profile_rejected_reason', ['reason' => $reason]);
        }

        return [
            'type' => 'profile_rejected',
            'title' => __('frontend.notifications.profile_rejected_title'),
            'body' => $body,
            'data' => [
                'reason' => $reason,
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function underReview(?string $url = null): array
    {
        return [
            'type' => 'account_status',
            'title' => __('frontend.notifications.profile_under_review_title'),
            'body' => __('frontend.notifications.profile_under_review_body'),
            'data' => [
                'status' => 'under_review',
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function suspended(?string $reason = null, ?string $url = null): array
    {
        $body = __('frontend.notifications.account_suspended_body');
        if ($reason) {
            $body .= ' '.__('frontend.notifications.profile_rejected_reason', ['reason' => $reason]);
        }

        return [
            'type' => 'account_status',
            'title' => __('frontend.notifications.account_suspended_title'),
            'body' => $body,
            'data' => [
                'status' => 'suspended',
                'reason' => $reason,
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function restored(?string $url = null): array
    {
        return [
            'type' => 'account_status',
            'title' => __('frontend.notifications.account_restored_title'),
            'body' => __('frontend.notifications.account_restored_body'),
            'data' => [
                'status' => 'restored',
                'url' => $url ?: route('frontend.psychologist.profile.show'),
            ],
        ];
    }
}
