<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Validation\Rule;

class StoreConversationRequest extends ConversationApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() ?? false;
    }

    public function rules(): array
    {
        //return [];
        return [
            // Optional when the patient already has an assigned psychologist.
            'professional_uuid' => [
                'nullable',
                'uuid',
                Rule::exists('professionals', 'uuid'),
            ],
        ];
    }

    /**
     * Web forms should redirect with flash errors; API clients get JSON.
     */
    public function expectsJson(): bool
    {
        return $this->ajax() || $this->wantsJson();
    }
}
