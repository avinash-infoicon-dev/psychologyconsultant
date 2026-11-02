<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Queued Reverb broadcast: the HTTP send never waits on the WebSocket server.
 * Frontend uses Laravel Echo; keep `php artisan reverb:start` and
 * `php artisan queue:work` running for live delivery.
 */
class MessageSent implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->loadMissing([
            'sender',
            'conversation.patient.user',
            'conversation.psychologist.user',
            'audio',
        ]);
    }

    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $channels = [
            new PrivateChannel('conversation.'.$conversation->uuid),
        ];

        $recipientId = (int) $this->message->sender_id === (int) $conversation->patient?->user_id
            ? $conversation->psychologist?->user_id
            : $conversation->patient?->user_id;

        if ($recipientId) {
            $channels[] = new PrivateChannel('App.Models.User.'.$recipientId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $payload = [
            'id' => $this->message->id,
            'conversation_uuid' => $this->message->conversation->uuid,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender?->name,
            'type' => $this->message->type,
            'body' => $this->message->type === 'text' ? $this->message->body : null,
            'is_read' => (bool) $this->message->is_read,
            'status' => $this->message->is_read ? 'read' : 'sent',
            'read_at' => $this->message->read_at?->toIso8601String(),
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];

        if ($this->message->type === 'audio') {
            $payload['audio'] = [
                'duration_seconds' => $this->message->audio?->duration_seconds,
                'transcript' => $this->message->audio?->transcript,
                'transcription_status' => $this->message->audio?->transcription_status,
            ];
        }

        return $payload;
    }
}
