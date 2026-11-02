<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Fallback mailable used by NotificationService for any notification type
 * that doesn't have a dedicated Mailable. For notifications where the
 * email needs bespoke formatting (e.g. invoices with a PDF attachment),
 * build a dedicated Mailable instead and call Mail::send() from that
 * module's service rather than routing through here.
 */
class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $body,
        public array $data = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->title);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.generic-notification',
            with: ['title' => $this->title, 'body' => $this->body, 'data' => $this->data],
        );
    }
}
