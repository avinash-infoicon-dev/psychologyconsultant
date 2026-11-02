<?php

namespace App\Http\Requests\PsychologistDirectory;

use Illuminate\Foundation\Http\FormRequest;

abstract class PsychologistDirectoryApiRequest extends FormRequest
{
    public function expectsJson(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return __('psychologist_directory_api.validation.attributes');
    }

    public function messages(): array
    {
        return __('psychologist_directory_api.validation.messages');
    }
}
