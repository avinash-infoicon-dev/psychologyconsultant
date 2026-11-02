<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;

class ResetPasswordRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $password = config('security.password');

        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => array_filter([
                'required',
                'confirmed',
                'string',
                'min:'.($password['min_length'] ?? 10),
                $password['require_uppercase'] ? 'regex:/[A-Z]/' : null,
                $password['require_number'] ? 'regex:/[0-9]/' : null,
                $password['require_symbol'] ? 'regex:/[^A-Za-z0-9]/' : null,
            ]),
        ];
    }
}
