<?php

namespace App\Http\Requests\Message;

use App\Support\MessagingLimits;

class SendMessageRequest extends MessageConversationRequest
{
    public function rules(): array
    {
        // Old limit (kept for reference):
        // 'body' => ['required', 'string', 'max:10000'],
        // Patients only:
        // if ($this->user()?->isPatient()) {
        //     $rules[] = 'max:'.MessagingLimits::maxTextChars();
        // }

        // §5.5 — combined plan + global caps (null / 0 = unlimited).
        $rules = ['required', 'string'];
        $maxChars = MessagingLimits::maxTextChars($this->user());

        if ($maxChars !== null) {
            $rules[] = 'max:'.$maxChars;
        }

        return [
            'body' => $rules,
        ];
    }
}
