<?php

namespace App\Http\Requests\Message;

use App\Models\Message;

class StreamMessageAudioRequest extends MessageApiRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $message = $this->route('message');

        if (! $user || ! $message instanceof Message) {
            return false;
        }

        $message->loadMissing(['conversation.patient', 'conversation.psychologist']);
        $conversation = $message->conversation;

        if (! $conversation) {
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
