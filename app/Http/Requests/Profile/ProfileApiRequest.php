<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

abstract class ProfileApiRequest extends FormRequest
{
    public function attributes(): array
    {
        return __('profile_api.validation.attributes');
    }

    public function messages(): array
    {
        return __('profile_api.validation.messages');
    }
}
