<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $expiryMinutes,
        public string $userName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('auth_api.email_verify_mail_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.email-verification-code',
            with: [
                'code' => $this->code,
                'expiryMinutes' => $this->expiryMinutes,
                'userName' => $this->userName,
            ],
        );
    }
}
