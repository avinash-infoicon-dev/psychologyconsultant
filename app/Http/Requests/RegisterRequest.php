<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthApiRequest;
use App\Models\CountryRestriction;
use App\Support\Locales;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegisterRequest extends AuthApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $role = $this->input('role', 'patient');
        $intent = $this->input('intent', $role === 'psychologist' ? 'draft' : 'submit');

        $name = trim((string) $this->input('name'));
        if ($role === 'psychologist' && $name === '') {
            $name = trim($this->input('first_name').' '.$this->input('last_name'));
        }

        $gender = $this->input('gender');
        $genderMap = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
        if (is_string($gender) && isset($genderMap[strtolower($gender)])) {
            $gender = $genderMap[strtolower($gender)];
        } elseif (! is_string($gender) || trim($gender) === '') {
            $gender = null;
        }

        $this->merge([
            'role' => $role,
            'intent' => $role === 'psychologist' ? $intent : 'submit',
            'name' => $name !== '' ? $name : $this->input('name'),
            'locale' => $this->input('locale') ?: app()->getLocale(),
            'gender' => $gender,
        ]);
    }

    public function rules(): array
    {
        $password = config('security.password');
        $allowedCountryCodes = CountryRestriction::allowedOptions()->keys()->all();
        $isPatient = $this->input('role') === 'patient';
        $isPsychologist = $this->input('role') === 'psychologist';
        $isSubmit = $isPsychologist && $this->input('intent') === 'submit';

        $countryRules = ['nullable', 'string'];
        if ($isPatient) {
            $countryRules = ['required', 'string', 'size:2', Rule::in($allowedCountryCodes)];
        } elseif ($isPsychologist) {
            $countryRules = ['nullable', 'string', 'max:100'];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => array_filter([
                'required',
                'confirmed',
                'string',
                'min:'.($password['min_length'] ?? 10),
                $password['require_uppercase'] ? 'regex:/[A-Z]/' : null,
                $password['require_number'] ? 'regex:/[0-9]/' : null,
                $password['require_symbol'] ? 'regex:/[^A-Za-z0-9]/' : null,
            ]),
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', Locales::rule()],
            'role' => ['required', Rule::in(['patient', 'psychologist'])],
            'intent' => [Rule::in(['draft', 'submit'])],

            'date_of_birth' => [$isPatient ? 'required' : 'nullable', 'date', 'before:-18 years'],
            'gender' => ['nullable', 'string', 'max:50'],
            'country' => $countryRules,

            // Legacy psychologist-only fields on the single-page form:
            // 'license_number' => ['required_if:role,psychologist', 'nullable', 'string', 'max:100'],
            // 'specialization' => [$isSubmit ? 'required' : 'nullable', 'string', 'max:255'],
            'license_number' => [$isSubmit ? 'required' : 'nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'specialization_ids' => [$isSubmit ? 'required' : 'nullable', 'array', $isSubmit ? 'min:1' : 'nullable'],
            'specialization_ids.*' => ['integer', 'exists:specializations,id'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:70'],

            'first_name' => [$isPsychologist ? 'required' : 'nullable', 'string', 'max:255'],
            'last_name' => [$isPsychologist ? 'required' : 'nullable', 'string', 'max:255'],
            'title' => [$isPsychologist ? 'required' : 'nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'languages' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'specializations_text' => ['nullable', 'string', 'max:5000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            'education' => [$isSubmit ? 'required' : 'nullable', 'array'],
            'education.*.degree' => ['nullable', 'string', 'max:255'],
            'education.*.institution' => ['nullable', 'string', 'max:255'],
            'education.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'education.*.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'education.*.description' => ['nullable', 'string', 'max:2000'],

            'experience' => ['nullable', 'array'],
            'experience.*.role' => ['nullable', 'string', 'max:255'],
            'experience.*.organization' => ['nullable', 'string', 'max:255'],
            'experience.*.start_date' => ['nullable', 'date'],
            'experience.*.end_date' => ['nullable', 'date'],
            'experience.*.current' => ['nullable', 'boolean'],
            'experience.*.description' => ['nullable', 'string', 'max:2000'],

            'social' => ['nullable', 'array'],
            'social.*.platform' => ['nullable', 'string', 'max:100'],
            'social.*.url' => ['nullable', 'string', 'max:255'],

            'gallery' => ['nullable', 'array'],
            'gallery.*.title' => ['nullable', 'string', 'max:255'],
            'gallery.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'gallery.*.description' => ['nullable', 'string', 'max:1000'],
            'gallery.*.image' => ['nullable', 'image', 'max:6144'],

            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['nullable', 'string', 'max:255'],
            'documents.*.type' => ['nullable', 'string', 'max:100'],
            'documents.*.file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],

            'awards' => ['nullable', 'array'],
            'awards.*.title' => ['nullable', 'string', 'max:255'],
            'awards.*.organization' => ['nullable', 'string', 'max:255'],
            'awards.*.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'awards.*.description' => ['nullable', 'string', 'max:2000'],

            'accept_terms' => [($isPatient || $isSubmit) ? 'accepted' : 'sometimes'],
            'accept_privacy_policy' => [($isPatient || $isSubmit) ? 'accepted' : 'sometimes'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('email')) {
                foreach (array_keys($validator->errors()->messages()) as $key) {
                    if ($key !== 'email') {
                        $validator->errors()->forget($key);
                    }
                }

                return;
            }

            if ($this->input('role') !== 'psychologist' || $this->input('intent') !== 'submit') {
                return;
            }

            $hasEducation = collect($this->input('education', []))->contains(function ($row) {
                return is_array($row)
                    && filled($row['degree'] ?? null)
                    && filled($row['institution'] ?? null);
            });

            $hasSpecializations = collect($this->input('specialization_ids', []))->filter()->isNotEmpty();

            if (! $hasEducation || ! $hasSpecializations) {
                $validator->errors()->add('education', __('frontend.psych_profile.verification_incomplete'));
            }
        });
    }
}
