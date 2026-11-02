<?php

namespace App\Http\Requests\Message;

use App\Models\Message;

class MarkMessageReadRequest extends MessageApiRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $message = $this->route('message');

        if (! $user || ! $message instanceof Message) {
            return false;
        }

        return $user->can('markRead', $message);
    }

    public function rules(): array
    {
        return [];
    }
}
