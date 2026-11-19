<?php

namespace App\Http\Requests\Message;

class UploadMessageDocumentRequest extends MessageConversationRequest
{
    public const MAX_KILOBYTES = 10240;

    public function rules(): array
    {
        return [
            'document' => [
                'required',
                'file',
                'max:'.self::MAX_KILOBYTES,
                'mimes:pdf',
                'mimetypes:application/pdf,application/x-pdf',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => __('frontend.chat.pdf_only'),
            'document.mimes' => __('frontend.chat.pdf_only'),
            'document.mimetypes' => __('frontend.chat.pdf_only'),
            'document.max' => __('frontend.chat.pdf_too_large', ['size' => 10]),
        ];
    }
}
