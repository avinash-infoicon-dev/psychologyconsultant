<?php

namespace App\Http\Controllers\API\Messaging;

use App\Exceptions\ChannelNotAllowedException;
use App\Exceptions\ExchangeMessageLimitException;
use App\Exceptions\PsychologistUnavailableException;
use App\Exceptions\QuotaExceededException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\ListMessagesRequest;
use App\Http\Requests\Message\MarkMessageReadRequest;
use App\Http\Requests\Message\SendMessageRequest;
use App\Http\Requests\Message\UploadMessageAudioRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Messaging\MessageService;
use App\Services\Messaging\ExchangeQuotaService;
use Illuminate\Http\JsonResponse;

// use App\Http\Requests\SendMessageRequest;
// use App\Http\Requests\UploadAudioRequest;
// use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected MessageService $messages,
        protected ExchangeQuotaService $quota,
    ) {}

    public function index(ListMessagesRequest $request, Conversation $conversation): JsonResponse
    {
        // $this->authorize('view', $conversation);

        $perPage = $request->integer('per_page', 50);
        $messages = $this->messages->listForConversation($conversation, $perPage);

        return response()->json($this->messages->paginatedPayload($messages));

        // $messages = $conversation->messages()
        //     ->with(['audio', 'conversation'])
        //     ->orderBy('created_at')
        //     ->paginate(50);
        //
        // return response()->json([
        //     'data' => MessageResource::collection($messages),
        //     'meta' => [
        //         'current_page' => $messages->currentPage(),
        //         'last_page' => $messages->lastPage(),
        //         'total' => $messages->total(),
        //     ],
        // ]);
    }

    public function store(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        // $this->authorize('view', $conversation);

        try {
            $message = $this->messages->send($request->user(), $conversation, $request->string('body'));
        } catch (QuotaExceededException $e) {
            return response()->json(['message' => __('message_api.quota_exceeded')], 402);
        } catch (ExchangeMessageLimitException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'exchange_message_limit',
                'exchange_message_usage' => $this->quota->currentExchangeMessageUsage($conversation, $request->user()),
            ], 422);
        } catch (PsychologistUnavailableException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'psychologist_unavailable',
            ], 422);
        }

        $message->setRelation('conversation', $conversation);

        // Old: return response()->json(['data' => new MessageResource($message)], 201);
        return response()->json([
            'data' => new MessageResource($message),
            'exchange_message_usage' => $this->quota->currentExchangeMessageUsage($conversation, $request->user()),
        ], 201);
    }

    public function storeAudio(UploadMessageAudioRequest $request, Conversation $conversation): JsonResponse
    {
        // $this->authorize('view', $conversation);

        try {
            $message = $this->messages->sendAudio(
                $request->user(),
                $conversation,
                $request->file('audio'),
                $request->integer('duration_seconds') ?: null,
                $request->audioMimeType(),
            );
        } catch (QuotaExceededException $e) {
            return response()->json(['message' => __('message_api.quota_exceeded')], 402);
        } catch (ExchangeMessageLimitException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'exchange_message_limit',
                'exchange_message_usage' => $this->quota->currentExchangeMessageUsage($conversation, $request->user()),
            ], 422);
        } catch (PsychologistUnavailableException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'psychologist_unavailable',
            ], 422);
        } catch (ChannelNotAllowedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'channel_not_allowed',
            ], 422);
        }

        $message->setRelation('conversation', $conversation);

        // Old: return response()->json(['data' => new MessageResource($message)], 201);
        return response()->json([
            'data' => new MessageResource($message),
            'exchange_message_usage' => $this->quota->currentExchangeMessageUsage($conversation, $request->user()),
        ], 201);
    }

    public function markRead(MarkMessageReadRequest $request, Message $message): JsonResponse
    {
        // $this->authorize('markRead', $message);

        $this->messages->markRead($message);

        return response()->json([
            'data' => new MessageResource($message->fresh(['conversation'])),
        ]);
    }
}
