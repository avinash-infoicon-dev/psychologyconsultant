<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestAcceptedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $psychologistName;

    public function __construct($psychologistName)
    {
        $this->psychologistName = $psychologistName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'request_accepted',
            'message' => __('relationship.request_accepted_notification', ['name' => $this->psychologistName]),
            'url' => route('frontend.patient.psychologists.index'),
        ];
    }

    /**
     * Payload for NotificationService (custom notifications table).
     *
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function payload(string $psychologistName, ?string $url = null): array
    {
        return [
            'type' => 'request_accepted',
            'title' => __('relationship.accepted'),
            'body' => __('relationship.request_accepted_notification', ['name' => $psychologistName]),
            'data' => [
                'url' => $url ?: route('frontend.patient.psychologists.index'),
            ],
        ];
    }
}
