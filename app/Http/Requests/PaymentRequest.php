<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:100'], // smallest currency unit, min CHF 1.00
            'currency' => ['nullable', 'string', 'size:3'],
            'gateway' => ['required', Rule::in(['stripe', 'twint'])],
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id')],
        ];
    }
}
