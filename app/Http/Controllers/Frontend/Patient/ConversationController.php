<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conversation\ListConversationsRequest;
use App\Http\Requests\Conversation\ShowConversationRequest;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Models\Conversation;
use App\Models\ResponseTemplate;
use App\Models\User;
use App\Services\Messaging\ConversationService;
use App\Services\Messaging\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationService $conversations,
        protected MessageService $messages,
    ) {}

    public function index(ListConversationsRequest $request): View|JsonResponse
    {
        $perPage = $request->integer('per_page', 20);
        $conversations = $this->conversations->listForUser($request->user(), $perPage);

        // Only the chat live-poll uses ?live=1 — normal sidebar/page visits must get HTML.
        if ($request->boolean('live')) {
            return response()->json([
                'data' => $conversations->getCollection()->map(function (Conversation $item) use ($request) {
                    $isPsychologist = $request->user()->isPsychologist();
                    $peer = $isPsychologist ? $item->patient : $item->psychologist;
                    $name = $peer?->user?->name
                        ?? trim(($peer?->first_name ?? '').' '.($peer?->last_name ?? ''))
                        ?: __('frontend.chat.unknown');
                    $latest = $item->messages->first();

                    return [
                        'uuid' => $item->uuid,
                        'status' => $item->status,
                        'name' => $name,
                        'preview' => $latest
                            ? ($latest->type === 'audio'
                                ? __('frontend.chat.audio_message')
                                : \Illuminate\Support\Str::limit($latest->body ?? '—', 48))
                            : __('frontend.chat.no_messages_yet'),
                        'time' => optional($item->last_message_at ?? $item->updated_at)->format('H:i'),
                        'unread' => (int) ($item->unread_count ?? 0),
                        'last_message_id' => $latest?->id,
                    ];
                })->values(),
            ]);
        }

        // Legacy list-only view:
        // return view('frontend.patient.conversations.index', compact('conversations'));

        return view('frontend.patient.conversations.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
            'chatRoutePrefix' => $this->chatRoutePrefix($request->user()),
            'responseTemplates' => $this->responseTemplatesFor($request->user()),
        ]);
    }

    public function store(StoreConversationRequest $request): RedirectResponse
    {
        try {
            $conversation = $this->conversations->findOrCreateForPatient(
                $request->user(),
                $request->validated('professional_uuid'),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // No psychologist chosen/assigned → send patient to the directory.
            if ($e->validator->errors()->has('professional_uuid')) {
                return redirect()
                    ->route('frontend.patient.psychologists.index')
                    ->with('error', $e->validator->errors()->first('professional_uuid'));
            }

            throw $e;
        }

        // dd(compact('conversation'));

        return redirect()
            ->route('frontend.patient.conversations.show', $conversation)
            ->with('success', __('conversation_api.created'));
    }

    /** Header bell: unread totals + recent unread threads (JSON). */
    public function unreadSummary(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->conversations->unreadSummaryFor($request->user()),
        ]);
    }

    public function show(ShowConversationRequest $request, Conversation $conversation): View
    {
        $conversation->load(['patient.user', 'psychologist.user']);
        $messagesPage = $this->messages->listNewestForConversation($conversation, 30);
        $messages = $messagesPage->getCollection()->reverse()->values();

        // Legacy per-row mark:
        // $messages
        //     ->where('sender_id', '!=', $request->user()->id)
        //     ->where('is_read', false)
        //     ->each(fn ($message) => $message->markAsRead());
        $readerId = (int) $request->user()->id;
        $this->messages->markIncomingAsRead($conversation, $request->user());
        $messages->each(function ($message) use ($readerId) {
            if ((int) $message->sender_id !== $readerId) {
                $message->is_read = true;
                $message->read_at = $message->read_at ?? now();
            }
        });

        $conversations = $this->conversations->listForUser($request->user(), 100);

        // Legacy detail view:
        // return view('frontend.patient.conversations.show', compact('conversation'));

        return view('frontend.patient.conversations.show', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
            'hasOlderMessages' => $messagesPage->hasMorePages(),
            'nextMessagesPage' => $messagesPage->hasMorePages() ? 2 : null,
            'conversation' => $conversation,
            'chatRoutePrefix' => $this->chatRoutePrefix($request->user()),
            'responseTemplates' => $this->responseTemplatesFor($request->user()),
        ]);
    }

    protected function chatRoutePrefix(User $user): string
    {
        return $user->isPsychologist()
            ? 'frontend.psychologist'
            : 'frontend.patient';
    }

    /** Active reply snippets for psychologist chat composer. */
    protected function responseTemplatesFor(User $user): Collection
    {
        if (! $user->isPsychologist()) {
            return collect();
        }

        $user->loadMissing(['psychologist', 'professional']);
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        if (! $professionalId) {
            return collect();
        }

        return ResponseTemplate::query()
            ->where('professional_id', $professionalId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'title', 'body']);
    }
    public function close(Request $request, Conversation $conversation): RedirectResponse
    {
        // Verify authorization: User must be a participant
        $userId = $request->user()->id;
        $isParticipant = ($conversation->patient->user_id === $userId) 
            || ($conversation->psychologist->user_id === $userId)
            || ($conversation->patient->user->id === $userId) // handle different relation definitions
            || (optional($conversation->psychologist->user)->id === $userId);

        if (!$isParticipant) {
            abort(403, 'Unauthorized to close this conversation.');
        }

        $conversation->update([
            'status' => 'closed',
        ]);

        return redirect()->back()->with('success', __('relationship.conversation_closed'));
    }

    //  public function register_test(Request $request){
    //     dd($request->all());
    // }

}
