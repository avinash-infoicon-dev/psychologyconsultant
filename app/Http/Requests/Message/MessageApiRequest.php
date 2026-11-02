<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

abstract class MessageApiRequest extends FormRequest
{
    public function attributes(): array
    {
        return __('message_api.validation.attributes');
    }

    public function messages(): array
    {
        return __('message_api.validation.messages');
    }
}
