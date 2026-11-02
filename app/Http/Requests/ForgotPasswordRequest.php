<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;

class ForgotPasswordRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
