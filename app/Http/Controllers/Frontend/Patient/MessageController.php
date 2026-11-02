<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Exceptions\ChannelNotAllowedException;
use App\Exceptions\ExchangeMessageLimitException;
use App\Exceptions\PsychologistUnavailableException;
use App\Exceptions\QuotaExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\ListMessagesRequest;
use App\Http\Requests\Message\MarkConversationReadRequest;
use App\Http\Requests\Message\MarkMessageReadRequest;
use App\Http\Requests\Message\SendMessageRequest;
use App\Http\Requests\Message\StreamMessageAudioRequest;
use App\Http\Requests\Message\UploadMessageAudioRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Messaging\AudioService;
use App\Services\Messaging\ExchangeQuotaService;
use App\Services\Messaging\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messages,
        protected AudioService $audioService,
        protected ExchangeQuotaService $quota,
    ) {}

    public function index(ListMessagesRequest $request, Conversation $conversation): JsonResponse
    {
        $afterId = $request->integer('after_id');

        if ($afterId > 0) {
            $newer = $this->messages->listAfterId($conversation, $afterId);

            return response()->json([
                'data' => $newer->map(fn (Message $message) => $this->messagePayload($message))->values(),
            ]);
        }

        $perPage = $request->integer('per_page', 30);
        $messages = $this->messages->listNewestForConversation($conversation, $perPage);

        return response()->json([
            'data' => $messages->getCollection()->map(fn (Message $message) => $this->messagePayload($message))->values(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'has_more' => $messages->hasMorePages(),
                'next_page' => $messages->hasMorePages() ? $messages->currentPage() + 1 : null,
            ],
        ]);
    }

    public function store(SendMessageRequest $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        try {
            $message = $this->messages->send($request->user(), $conversation, $request->string('body'));
        } catch (QuotaExceededException) {
            return $this->quotaExceededResponse($request, $conversation);
        } catch (ExchangeMessageLimitException $e) {
            return $this->exchangeMessageLimitResponse($request, $e, $conversation);
        } catch (PsychologistUnavailableException $e) {
            return $this->psychologistUnavailableResponse($request, $e);
        }

        if ($request->expectsJson() || $request->ajax()) {
            // Old: return response()->json(['data' => $this->messagePayload(...)], 201);
            return response()->json([
                'data' => $this->messagePayload($message->loadMissing('sender')),
                'exchange_message_usage' => $this->exchangeMessageUsagePayload($conversation, $request->user()),
            ], 201);
        }

        return redirect()
            ->route($this->conversationsShowRoute($request->user()), $conversation)
            ->with('success', __('message_api.sent'));
    }

    public function storeAudio(UploadMessageAudioRequest $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        try {
            $message = $this->messages->sendAudio(
                $request->user(),
                $conversation,
                $request->file('audio'),
                $request->integer('duration_seconds') ?: null,
                $request->audioMimeType(),
            );
        } catch (QuotaExceededException) {
            return $this->quotaExceededResponse($request, $conversation);
        } catch (ExchangeMessageLimitException $e) {
            return $this->exchangeMessageLimitResponse($request, $e, $conversation);
        } catch (PsychologistUnavailableException $e) {
            return $this->psychologistUnavailableResponse($request, $e);
        } catch (ChannelNotAllowedException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => 'channel_not_allowed',
                ], 422);
            }

            return back()->withErrors(['audio' => $e->getMessage()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            // Old: return response()->json(['data' => $this->messagePayload(...)], 201);
            return response()->json([
                'data' => $this->messagePayload($message->loadMissing(['sender', 'audio'])),
                'exchange_message_usage' => $this->exchangeMessageUsagePayload($conversation, $request->user()),
            ], 201);
        }

        return redirect()
            ->route($this->conversationsShowRoute($request->user()), $conversation)
            ->with('success', __('message_api.audio_sent'));
    }

    public function streamAudio(StreamMessageAudioRequest $request, Message $message): Response|StreamedResponse
    {
        $message->loadMissing('audio');

        if (! $message->isAudio() || ! $message->audio) {
            abort(404);
        }

        $bytes = $this->audioService->retrieveBytes($message->audio);
        $mime = $this->normalizedAudioMime($message->audio->mime_type);

        return response($bytes, 200, [
            'Content-Type' => $mime,
            'Content-Length' => (string) strlen($bytes),
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function markRead(MarkMessageReadRequest $request, Message $message): JsonResponse|RedirectResponse
    {
        $message->loadMissing('conversation');
        $this->messages->markIncomingAsRead($message->conversation, $request->user());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'data' => ['id' => $message->id, 'is_read' => true, 'status' => 'read'],
            ]);
        }

        // Legacy redirect after marking a single message:
        // return redirect()
        //     ->route($this->conversationsShowRoute($request->user()), $message->conversation)
        //     ->with('success', __('message_api.marked_read'));

        return redirect()
            ->route($this->conversationsShowRoute($request->user()), $message->conversation)
            ->with('success', __('message_api.marked_read'));
    }

    public function markConversationRead(MarkConversationReadRequest $request, Conversation $conversation): JsonResponse
    {
        $ids = $this->messages->markIncomingAsRead($conversation, $request->user());

        return response()->json([
            'data' => [
                'message_ids' => $ids,
            ],
        ]);
    }

    protected function conversationsShowRoute(User $user): string
    {
        return $user->isPsychologist()
            ? 'frontend.psychologist.conversations.show'
            : 'frontend.patient.conversations.show';
    }

    protected function quotaExceededResponse(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $paymentUrl = route('frontend.patient.subscription.index', [
            'quota' => 1,
            'return' => route($this->conversationsShowRoute($request->user()), $conversation),
        ]);

        // Patients only — psychologists never consume patient quotas.
        if (! $request->user()?->isPatient()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => __('message_api.quota_exceeded')], 422);
            }

            return back()->withErrors(['body' => __('message_api.quota_exceeded')]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => __('message_api.quota_exceeded'),
                'code' => 'quota_exceeded',
                'redirect' => $paymentUrl,
            ], 402);
        }

        return redirect()
            ->to($paymentUrl)
            ->with('error', __('message_api.quota_exceeded'));
    }

    protected function exchangeMessageLimitResponse(
        Request $request,
        ExchangeMessageLimitException $e,
        Conversation $conversation,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'exchange_message_limit',
                'exchange_message_usage' => $this->exchangeMessageUsagePayload($conversation, $request->user()),
            ], 422);
        }

        return back()->withErrors(['body' => $e->getMessage()]);
    }

    protected function psychologistUnavailableResponse(
        Request $request,
        PsychologistUnavailableException $e,
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'psychologist_unavailable',
            ], 422);
        }

        return back()->withErrors(['body' => $e->getMessage()]);
    }

    /**
     * @return array{used: int, limit: int, remaining: int, at_cap: bool}|null
     */
    protected function exchangeMessageUsagePayload(Conversation $conversation, ?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return $this->quota->currentExchangeMessageUsage($conversation, $user);
    }

    protected function messagePayload(Message $message): array
    {
        $message->loadMissing(['sender', 'audio']);

        $payload = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->name,
            'type' => $message->type,
            'body' => $message->type === 'text' ? $message->body : null,
            'is_read' => $message->is_read,
            'status' => $message->is_read ? 'read' : 'sent',
            'read_at' => $message->read_at?->toIso8601String(),
            'created_at' => $message->created_at?->toIso8601String(),
        ];

        if ($message->type === 'audio' && $message->audio) {
            $payload['audio'] = [
                'url' => route($this->messagesAudioRoute(), $message),
                'duration_seconds' => $message->audio->duration_seconds,
                'transcript' => $message->audio->transcript,
                'transcription_status' => $message->audio->transcription_status,
            ];
        }

        return $payload;
    }

    protected function messagesAudioRoute(): string
    {
        $user = request()->user();

        return ($user?->isPsychologist() ? 'frontend.psychologist' : 'frontend.patient').'.messages.audio';
    }

    protected function normalizedAudioMime(?string $mime): string
    {
        return match ($mime) {
            'video/webm' => 'audio/webm',
            'video/ogg' => 'audio/ogg',
            null, '' => 'audio/webm',
            default => $mime,
        };
    }
}
