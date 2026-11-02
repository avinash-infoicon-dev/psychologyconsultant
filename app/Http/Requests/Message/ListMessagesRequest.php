<?php

namespace App\Http\Requests\Message;

class ListMessagesRequest extends MessageConversationRequest
{
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'after_id' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
