<?php

namespace App\Http\Requests;

use App\Http\Requests\Profile\ProfileApiRequest;
use App\Support\Locales;

class UpdateProfileRequest extends ProfileApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $common = [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'locale' => ['sometimes', 'string', Locales::rule()],
        ];

        if ($user->isPatient()) {
            return array_merge($common, [
                'date_of_birth' => ['sometimes', 'date', 'before:-18 years'],
                'gender' => ['sometimes', 'nullable', 'string', 'max:50'],
                'address' => ['sometimes', 'nullable', 'string', 'max:255'],
                'city' => ['sometimes', 'nullable', 'string', 'max:255'],
                'country' => ['sometimes', 'string', 'size:2'],
                'emergency_contact_name' => ['sometimes', 'nullable', 'string', 'max:255'],
                'emergency_contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            ]);
        }

        if ($user->isPsychologist()) {
            return array_merge($common, [
                'specialization' => ['sometimes', 'nullable', 'string', 'max:255'],
                'bio' => ['sometimes', 'nullable', 'string', 'max:5000'],
                'years_experience' => ['sometimes', 'integer', 'min:0', 'max:70'],
                'is_accepting_patients' => ['sometimes', 'boolean'],
                'languages' => ['sometimes', 'array'],
                'languages.*' => ['string', Locales::rule()],
            ]);
        }

        return $common;
    }
}
