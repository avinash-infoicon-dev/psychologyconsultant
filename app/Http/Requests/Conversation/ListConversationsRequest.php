<?php

namespace App\Http\Requests\Conversation;

class ListConversationsRequest extends ConversationApiRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user?->isPatient() || $user?->isPsychologist());
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
