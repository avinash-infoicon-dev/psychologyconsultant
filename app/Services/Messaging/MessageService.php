<?php

namespace App\Services\Messaging;

use App\Events\MessageSent;
use App\Events\MessagesRead;
use App\Exceptions\ChannelNotAllowedException;
use App\Exceptions\PsychologistUnavailableException;
use App\Exceptions\QuotaExceededException;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Plan;
use App\Models\User;
use App\Support\PsychologistCareStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

// use App\Services\Messaging\ExchangeQuotaService;
// use App\Services\Messaging\AudioService;

class MessageService
{
    public function __construct(
        protected ExchangeQuotaService $quota,
        protected AudioService $audioService,
    ) {}

    public function listForConversation(Conversation $conversation, int $perPage = 50): LengthAwarePaginator
    {
        return $conversation->messages()
            ->with(['audio', 'conversation'])
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    public function listNewestForConversation(Conversation $conversation, int $perPage = 30): LengthAwarePaginator
    {
        return $conversation->messages()
            ->with(['sender', 'audio'])
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Messages created after a given id — used by the web chat realtime poll fallback.
     *
     * @return \Illuminate\Support\Collection<int, Message>
     */
    public function listAfterId(Conversation $conversation, int $afterId, int $limit = 50)
    {
        return $conversation->messages()
            ->with(['sender', 'audio'])
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    /** @return array{data: AnonymousResourceCollection, meta: array<string, int>} */
    public function paginatedPayload(LengthAwarePaginator $messages): array
    {
        return [
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ];
    }

    /**
     * @throws QuotaExceededException
     * @throws PsychologistUnavailableException
     * @throws \App\Exceptions\ExchangeMessageLimitException
     */
    public function send(User $sender, Conversation $conversation, string $body): Message
    {
        // Combined plan + global caps (null = unlimited).
        $maxChars = \App\Support\MessagingLimits::maxTextChars($sender);
        if ($maxChars !== null && mb_strlen($body) > $maxChars) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'body' => [__('message_api.validation.messages.body.max', ['max' => $maxChars])],
            ]);
        }

        return DB::transaction(function () use ($sender, $conversation, $body) {
            $this->assertPsychologistAvailable($conversation);
            $this->quota->consumeIfStartingExchange($sender, $conversation);
            // Old: create then open/complete only.
            $this->quota->assertExchangeMessageAllowed($sender, $conversation);

            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'type' => 'text',
                'body' => $body,
            ]);

            $conversation->update(['last_message_at' => now()]);

            if ($sender->isPatient()) {
                $this->quota->openOrContinueExchange($sender, $conversation, $message);
            } else {
                $this->quota->completePendingExchange($sender, $conversation, $message);
            }

            $this->quota->recordExchangeMessage($sender, $conversation);

            $message->setRelation('conversation', $conversation);
            $message->loadMissing(['sender', 'conversation.patient.user', 'conversation.psychologist.user']);

            event(new MessageSent($message));

            return $message;
        });
    }

    /**
     * @throws QuotaExceededException
     * @throws ChannelNotAllowedException
     * @throws PsychologistUnavailableException
     * @throws \App\Exceptions\ExchangeMessageLimitException
     */
    public function sendAudio(
        User $sender,
        Conversation $conversation,
        UploadedFile $file,
        ?int $durationSeconds = null,
        ?string $mimeType = null,
    ): Message {
        return DB::transaction(function () use ($sender, $conversation, $file, $durationSeconds, $mimeType) {
            // §6.3: On-demand / Essential = text + dictation only (no voice messages).
            $this->quota->assertChannelAllowed($sender, Plan::CHANNEL_AUDIO);

            // Combined plan + global audio cap (null = unlimited).
            $maxSeconds = \App\Support\MessagingLimits::maxAudioSeconds($sender);
            if ($maxSeconds !== null && ($durationSeconds === null || $durationSeconds > $maxSeconds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'duration_seconds' => [__('message_api.validation.messages.duration_seconds.max', ['max' => $maxSeconds])],
                ]);
            }

            $maxKb = \App\Support\MessagingLimits::maxAudioUploadKb();
            // Legacy: $maxKb = (int) config('speech.max_upload_size_kb', 25600);
            $sizeKb = (int) ceil(($file->getSize() ?: 0) / 1024);
            if ($maxKb > 0 && $sizeKb > $maxKb) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'audio' => [__('message_api.validation.messages.audio.max', ['max' => $maxKb])],
                ]);
            }

            if (! \App\Support\MessagingLimits::audioFileAllowed($file)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'audio' => [__('message_api.validation.messages.audio.mimes', [
                        'values' => implode(', ', \App\Support\MessagingLimits::allowedAudioExtensions()),
                    ])],
                ]);
            }

            $this->assertPsychologistAvailable($conversation);
            $this->quota->consumeIfStartingExchange($sender, $conversation);
            $this->quota->assertExchangeMessageAllowed($sender, $conversation);

            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'type' => 'audio',
            ]);

            // Size was previously checked after create; keep store next.
            $this->audioService->store($message, $file, $durationSeconds, $mimeType);

            $conversation->update(['last_message_at' => now()]);

            if ($sender->isPatient()) {
                $this->quota->openOrContinueExchange($sender, $conversation, $message);
            } else {
                $this->quota->completePendingExchange($sender, $conversation, $message);
            }

            $this->quota->recordExchangeMessage($sender, $conversation);

            $message = $message->fresh(['audio', 'sender']);
            $message->setRelation('conversation', $conversation);
            $message->loadMissing(['conversation.patient.user', 'conversation.psychologist.user']);

            event(new MessageSent($message));

            return $message;
        });
    }

    public function markRead(Message $message): void
    {
        $message->markAsRead();
    }

    /**
     * Mark incoming unread messages as read and notify the sender (ticks).
     *
     * @return list<int>
     */
    public function markIncomingAsRead(Conversation $conversation, User $reader): array
    {
        $messages = $conversation->messages()
            ->where('sender_id', '!=', $reader->id)
            ->where('is_read', false)
            ->get();

        $ids = [];
        foreach ($messages as $message) {
            $message->markAsRead();
            $ids[] = (int) $message->id;
        }

        $conversation->loadMissing(['patient.user', 'psychologist.user']);
        app(\App\Services\NotificationService::class)
            ->markConversationNotificationsRead($reader, (string) $conversation->uuid);

        if ($ids !== []) {
            event(new MessagesRead($conversation, $reader, $ids));
        }

        return $ids;
    }

    /**
     * @throws PsychologistUnavailableException
     */
    protected function assertPsychologistAvailable(Conversation $conversation): void
    {
        $conversation->unsetRelation('psychologist');
        $conversation->load('psychologist.user');

        if (! PsychologistCareStatus::allowsMessaging($conversation->psychologist)) {
            throw new PsychologistUnavailableException();
        }
    }
}
