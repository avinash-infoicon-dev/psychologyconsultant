<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

abstract class AuthApiRequest extends FormRequest
{
    public function attributes(): array
    {
        return __('auth_api.validation.attributes');
    }

    public function messages(): array
    {
        return __('auth_api.validation.messages');
    }
}
