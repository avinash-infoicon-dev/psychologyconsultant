<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $phone = $this->input('phone') ?: $this->input('subject');

        $this->merge([
            'phone' => is_string($phone) ? trim($phone) : $phone,
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'email' => is_string($this->input('email')) ? trim($this->input('email')) : $this->input('email'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'consultation_type' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:50'],
            'page_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('frontend.contact_inquiry.name_required'),
            'email.required' => __('frontend.contact_inquiry.email_required'),
            'email.email' => __('frontend.contact_inquiry.email_invalid'),
            'message.required' => __('frontend.contact_inquiry.message_required'),
        ];
    }
}
