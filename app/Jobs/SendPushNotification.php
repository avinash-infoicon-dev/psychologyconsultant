<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Thin queueable wrapper around FcmService for call sites that want to
 * fire-and-forget a push outside of NotificationService's normal flow
 * (e.g. a broadcast to many users at once, batched separately from the
 * per-user notification record).
 */
class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $title,
        public string $body,
        public array $data = [],
    ) {}

    public function handle(FcmService $fcm): void
    {
        $tokens = $this->user->devices()->whereNotNull('fcm_token')->pluck('fcm_token')->all();

        if (! empty($tokens)) {
            $fcm->sendToTokens($tokens, $this->title, $this->body, $this->data);
        }
    }
}
