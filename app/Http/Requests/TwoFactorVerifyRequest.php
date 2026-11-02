<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;

class TwoFactorVerifyRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'digits:'.config('security.two_factor.code_length', 6)],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
