<?php

namespace App\Http\Requests\PsychologistDirectory;

use App\Models\Availability;
use App\Support\Locales;
use Illuminate\Validation\Rule;

class ListPsychologistsRequest extends PsychologistDirectoryApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() ?? false;
    }

    public function expectsJson(): bool
    {
        // Web directory reuses this request; JSON errors stay on the API only.
        // Old: always true via PsychologistDirectoryApiRequest.
        return str_starts_with($this->path(), 'api/');
    }

    public function rules(): array
    {
        return [
            'specialization' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', Rule::in(Locales::codes())],
            'availability' => ['nullable', 'string', Rule::in(['today', ...Availability::DAYS])],
            'accepting_only' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('accepting_only')) {
            $this->merge([
                'accepting_only' => filter_var($this->input('accepting_only'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        foreach (['specialization', 'language', 'availability'] as $key) {
            if ($this->exists($key) && $this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }
    }
}
