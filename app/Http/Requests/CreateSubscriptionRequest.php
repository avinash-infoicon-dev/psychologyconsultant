<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', Rule::exists('plans', 'id')->where('is_active', true)],
            'gateway' => ['required', Rule::in(['stripe', 'twint'])],
            'payment_method_id' => ['required_if:gateway,stripe', 'nullable', 'string'],
        ];
    }
}
