<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $this->isConversationParticipant($user, $message);
    }

    public function create(User $user, Message $message): bool
    {
        return $this->isConversationParticipant($user, $message);
    }

    /** Only the sender may edit their own message, and only text messages. */
    public function update(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id && $message->type === 'text';
    }

    /** "Delete" is soft — only for the sender's own view; the other party still sees it. */
    public function delete(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    /** Only the recipient (not the sender) can mark a message as read. */
    public function markRead(User $user, Message $message): bool
    {
        return $message->sender_id !== $user->id && $this->isConversationParticipant($user, $message);
    }

    protected function isConversationParticipant(User $user, Message $message): bool
    {
        $conversation = $message->conversation;

        if ($user->isPatient()) {
            return $conversation->patient?->user_id === $user->id;
        }

        if ($user->isPsychologist()) {
            return $conversation->psychologist?->user_id === $user->id;
        }

        return false;
    }
}
