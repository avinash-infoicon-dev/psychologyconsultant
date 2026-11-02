<?php

namespace App\Http\Requests\Message;

use App\Models\Conversation;

abstract class MessageConversationRequest extends MessageApiRequest
{
    public function authorize(): bool
    {
        return $this->userCanAccessConversation();
    }

    protected function userCanAccessConversation(): bool
    {
        $user = $this->user();
        $conversation = $this->route('conversation');

        if (! $user || ! $conversation instanceof Conversation) {
            return false;
        }

        $user->loadMissing(['patient', 'psychologist', 'professional']);

        if ($user->isPatient()) {
            return $conversation->patient_id === $user->patient?->id;
        }

        if ($user->isPsychologist()) {
            $professionalId = $user->psychologist?->id ?? $user->professional?->id;

            return $conversation->psychologist_id === $professionalId;
        }

        return false;
    }
}
