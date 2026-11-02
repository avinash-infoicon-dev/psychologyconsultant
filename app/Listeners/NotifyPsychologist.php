<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPsychologist implements ShouldQueue
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
        $patientUserId = $conversation?->patient?->user_id;

        // Only notify the psychologist when the patient sent the message.
        if (! $patientUserId || (int) $message->sender_id !== (int) $patientUserId) {
            return;
        }

        $psychologistUser = $conversation->psychologist?->user;

        if (! $psychologistUser) {
            return;
        }

        $payload = NewMessageNotification::for($message);
        $payload['data']['url'] = route('frontend.psychologist.conversations.show', $message->conversation->uuid);

        $this->notifications->send(
            $psychologistUser,
            $payload['type'],
            $payload['title'],
            $payload['body'],
            $payload['data'],
            'msg:'.$message->id.':'.$psychologistUser->id,
        );
    }
}
