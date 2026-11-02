<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;

class EmailVerificationRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'digits:'.config('security.email_verification.code_length', 6)],
        ];
    }
}
