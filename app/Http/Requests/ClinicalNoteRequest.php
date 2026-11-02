<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClinicalNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // authorization handled by ClinicalNotePolicy in the controller
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:20000'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
        ];
    }
}
