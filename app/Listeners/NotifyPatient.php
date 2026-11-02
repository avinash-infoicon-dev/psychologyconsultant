<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPatient implements ShouldQueue
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $message->loadMissing([
            'sender',
            'conversation.patient.user',
            'conversation.psychologist.user',
        ]);

        $conversation = $message->conversation;
        $psychologistUserId = $conversation?->psychologist?->user_id;

        // Only notify the patient when the psychologist sent the message.
        if (! $psychologistUserId || (int) $message->sender_id !== (int) $psychologistUserId) {
            return;
        }

        $patientUser = $conversation->patient?->user;

        if (! $patientUser) {
            return;
        }

        $payload = NewMessageNotification::for($message, 'psychologist_response');
        $payload['data']['url'] = route('frontend.patient.conversations.show', $message->conversation->uuid);

        $this->notifications->send(
            $patientUser,
            $payload['type'],
            $payload['title'],
            $payload['body'],
            $payload['data'],
            'msg:'.$message->id.':'.$patientUser->id,
        );
    }
}
