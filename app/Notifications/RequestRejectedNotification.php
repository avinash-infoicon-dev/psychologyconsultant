<?php

namespace App\Notifications;

class RequestRejectedNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function payload(string $psychologistName, ?string $reason = null, ?string $url = null): array
    {
        $body = __('frontend.notifications.request_rejected_body', ['name' => $psychologistName]);
        if ($reason) {
            $body .= ' '.__('frontend.notifications.request_rejected_reason', ['reason' => $reason]);
        }

        return [
            'type' => 'request_rejected',
            'title' => __('frontend.notifications.request_rejected_title'),
            'body' => $body,
            'data' => [
                'url' => $url ?: route('frontend.patient.psychologists.index'),
                'reason' => $reason,
            ],
        ];
    }
}
