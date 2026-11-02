<?php

namespace App\Http\Controllers\API\Messaging;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conversation\ListConversationsRequest;
use App\Http\Requests\Conversation\ShowConversationRequest;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Services\Messaging\ConversationService;
use Illuminate\Http\JsonResponse;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversations) {}

    public function index(ListConversationsRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $conversations = $this->conversations->listForUser($request->user(), $perPage);

        return response()->json($this->conversations->paginatedPayload($conversations));

        // $conversations = $this->conversations->listForUser($request->user());
        //
        // return response()->json([
        //     'data' => ConversationResource::collection($conversations),
        //     'meta' => [
        //         'current_page' => $conversations->currentPage(),
        //         'last_page' => $conversations->lastPage(),
        //         'total' => $conversations->total(),
        //     ],
        // ]);
    }

    /** Patients only — starts (or resumes) a conversation with a chosen / assigned psychologist. */
    public function store(StoreConversationRequest $request): JsonResponse
    {
        // $this->authorize('create', Conversation::class);

        // Legacy assigned-only:
        // $conversation = $this->conversations->findOrCreateForPatient($request->user());
        $conversation = $this->conversations->findOrCreateForPatient(
            $request->user(),
            $request->validated('professional_uuid'),
        );

        return response()->json([
            'data' => new ConversationResource($conversation),
            'message' => __('conversation_api.created'),
        ], 201);
    }

    public function show(ShowConversationRequest $request, Conversation $conversation): JsonResponse
    {
        // $this->authorize('view', $conversation);

        return response()->json([
            'data' => new ConversationResource(
                $this->conversations->loadForResource($conversation),
            ),
        ]);

        // $conversation->load(['patient.user', 'psychologist.user']);
        //
        // return response()->json(['data' => new ConversationResource($conversation)]);
    }
}
