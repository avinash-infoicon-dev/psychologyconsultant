<?php

namespace App\Http\Requests\Conversation;

use App\Models\Conversation;

class ShowConversationRequest extends ConversationApiRequest
{
    public function authorize(): bool
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

    public function rules(): array
    {
        return [];
    }
}
