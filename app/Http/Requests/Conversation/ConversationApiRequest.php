<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;

abstract class ConversationApiRequest extends FormRequest
{
    public function expectsJson(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return __('conversation_api.validation.attributes');
    }

    public function messages(): array
    {
        return __('conversation_api.validation.messages');
    }
}
