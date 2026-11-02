<?php

namespace App\Notifications;

class PatientRequestNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function payload(string $patientName, ?string $url = null): array
    {
        return [
            'type' => 'patient_request',
            'title' => __('frontend.notifications.patient_request_title'),
            'body' => __('frontend.notifications.patient_request_body', ['name' => $patientName]),
            'data' => [
                'url' => $url ?: route('frontend.psychologist.dashboard'),
            ],
        ];
    }
}
