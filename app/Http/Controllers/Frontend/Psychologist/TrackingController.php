<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Messaging\ExchangeQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TrackingController extends Controller
{
    use ResolvesProfessional;

    public function __construct(protected ExchangeQueueService $queue) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $professionalId = $this->professionalId($user) ?: 0;

        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $messagesSent = Message::query()
            ->where('sender_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $conversationsTouched = Message::query()
            ->where('sender_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->distinct('conversation_id')
            ->count('conversation_id');

        $activeConversations = Conversation::query()
            ->where('psychologist_id', $professionalId)
            ->whereBetween('last_message_at', [$from, $to])
            ->count();

        $pending = $this->queue->pendingForPsychologist($user);
        $avgResponseHours = $this->averageResponseHours($user->id, $professionalId, $from, $to);

        return view('frontend.psychologist.tracking.index', [
            'from' => $from,
            'to' => $to,
            'messagesSent' => $messagesSent,
            'conversationsTouched' => $conversationsTouched,
            'activeConversations' => $activeConversations,
            'pendingCount' => $pending->count(),
            'overdueCount' => $pending->where('is_overdue', true)->count(),
            'avgResponseHours' => $avgResponseHours,
        ]);
    }

    /** Mean hours between a patient message and the psychologist's next reply. */
    protected function averageResponseHours(int $psychUserId, int $professionalId, Carbon $from, Carbon $to): ?float
    {
        $conversationIds = Conversation::query()
            ->where('psychologist_id', $professionalId)
            ->pluck('id');

        if ($conversationIds->isEmpty()) {
            return null;
        }

        $psychMessages = Message::query()
            ->whereIn('conversation_id', $conversationIds)
            ->where('sender_id', $psychUserId)
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get(['id', 'conversation_id', 'created_at']);

        if ($psychMessages->isEmpty()) {
            return null;
        }

        $hours = [];

        foreach ($psychMessages as $reply) {
            $priorPatientMsg = Message::query()
                ->where('conversation_id', $reply->conversation_id)
                ->where('sender_id', '!=', $psychUserId)
                ->where('created_at', '<', $reply->created_at)
                ->orderByDesc('created_at')
                ->first();

            if ($priorPatientMsg) {
                $hours[] = $priorPatientMsg->created_at->diffInMinutes($reply->created_at) / 60;
            }
        }

        if ($hours === []) {
            return null;
        }

        return round(array_sum($hours) / count($hours), 1);
    }
}
