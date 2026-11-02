<?php

namespace App\Notifications;

use App\Models\Message;

/**
 * Not a Laravel Notification class — a small factory that builds the
 * (title, body, data) payload NotificationService needs for a new
 * message. Keeping channel logic (push/mail/database) centralized in
 * NotificationService avoids duplicating FCM/Mail wiring per notification
 * type; this class only knows how to phrase itself.
 */
class NewMessageNotification
{
    public static function for(Message $message, string $type = 'new_message'): array
    {
        $senderName = $message->sender->name;

        // In-app / push may show a short non-clinical hint; email must never
        // include message body (§5.6 confidentiality).
        // Old body that could leak clinical content:
        // $preview = $message->type === 'audio'
        //     ? 'Sent a voice message'
        //     : \Illuminate\Support\Str::limit($message->body, 80);

        $safeHint = $message->type === 'audio'
            ? __('frontend.notifications.new_message_audio_hint')
            : __('frontend.notifications.new_message_text_hint');

        $message->loadMissing('conversation');

        $isPsychologistResponse = $type === 'psychologist_response';
        $title = $isPsychologistResponse
            ? __('frontend.notifications.psychologist_response_title')
            : __('frontend.notifications.new_message_title', ['name' => $senderName]);
        $body = $isPsychologistResponse
            ? __('frontend.notifications.psychologist_response_body')
            : $safeHint;
        $mailBody = __('frontend.notifications.new_message_mail_body', ['name' => $senderName]);

        return [
            // Old: 'type' => 'new_message',
            'type' => $type,
            'title' => $title,
            // Database / push: still no health content (confidentiality).
            'body' => $body,
            'data' => [
                'conversation_id' => $message->conversation->uuid,
                'message_id' => $message->id,
                // Explicit mail copy — NotificationService prefers these over body.
                'mail_title' => $title,
                'mail_body' => $mailBody,
            ],
        ];
    }
}
