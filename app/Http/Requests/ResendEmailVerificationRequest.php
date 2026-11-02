<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;

class ResendEmailVerificationRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }
}
