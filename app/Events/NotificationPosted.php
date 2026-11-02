<?php

namespace App\Events;

use App\Models\Notification as NotificationRecord;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast immediately so header/sidebar unread badges do not wait on queue:work.
 */
class NotificationPosted implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public NotificationRecord $notification,
        public int $unreadCount,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $data = is_array($this->notification->data) ? $this->notification->data : [];

        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'data' => $data,
            'url' => $data['url'] ?? null,
            'conversation_id' => $data['conversation_id'] ?? null,
            'created_at' => $this->notification->created_at?->toIso8601String(),
            'unread_count' => $this->unreadCount,
        ];
    }
}
