<?php

namespace App\Http\Requests;

/**
 * @deprecated Use App\Http\Requests\Message\SendMessageRequest instead.
 */
class SendMessageRequest extends Message\SendMessageRequest
{
    // public function authorize(): bool
    // {
    //     return true; // authorization handled by ConversationPolicy in the controller
    // }
    //
    // public function rules(): array
    // {
    //     return [
    //         'body' => ['required', 'string', 'max:10000'],
    //     ];
    // }
}
