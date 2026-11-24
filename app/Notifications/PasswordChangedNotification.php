<?php

namespace App\Notifications;

class PasswordChangedNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function payload(): array
    {
        return [
            'type' => 'security_alert',
            'title' => __('frontend.notifications.password_changed_title'),
            'body' => __('frontend.notifications.password_changed_body'),
            'data' => ['event' => 'password_changed'],
        ];
    }
}
