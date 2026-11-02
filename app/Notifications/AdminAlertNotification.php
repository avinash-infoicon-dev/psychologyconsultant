<?php

namespace App\Notifications;

class AdminAlertNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function profileReview(string $psychologistName, ?string $url = null): array
    {
        return [
            'type' => 'psychologist_profile_review',
            'title' => __('frontend.notifications.admin_profile_review_title'),
            'body' => __('frontend.notifications.admin_profile_review_body', ['name' => $psychologistName]),
            'data' => [
                'url' => $url ?: route('admin.psychologists.index'),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function systemAlert(string $title, string $body, ?string $url = null, array $data = []): array
    {
        return [
            'type' => 'system_alert',
            'title' => $title,
            'body' => $body,
            'data' => array_merge($data, [
                'url' => $url ?: route('admin.dashboard'),
            ]),
        ];
    }
}
