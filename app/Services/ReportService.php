<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function __construct(protected NotificationService $notifications) {}

    /**
     * @return Collection<int, Conversation>
     */
    public function reportableConversations(User $user): Collection
    {
        $query = Conversation::query()
            ->with(['patient.user', 'psychologist.user'])
            ->latest('last_message_at');

        if ($user->isPatient()) {
            return $query->where('patient_id', $user->patient?->id)->get();
        }

        if ($user->isPsychologist()) {
            $professionalId = $user->psychologist?->id ?? $user->professional?->id;

            return $query->where('psychologist_id', $professionalId)->get();
        }

        return collect();
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function reportableUsers(User $user): Collection
    {
        return $this->reportableConversations($user)
            ->map(function (Conversation $conversation) use ($user) {
                $peerId = $this->peerUserId($conversation, $user);
                $peer = $conversation->patient?->user_id === $peerId
                    ? $conversation->patient?->user
                    : $conversation->psychologist?->user;

                if (! $peerId || ! $peer) {
                    return null;
                }

                return [
                    'id' => $peer->id,
                    'name' => $peer->name,
                ];
            })
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * @param  array{
     *     type: string,
     *     reason: string,
     *     description: string,
     *     conversation_id?: int|null,
     *     reported_user_id?: int|null,
     *     message_id?: int|null
     * }  $input
     */
    public function create(User $reporter, array $input): Report
    {
        $type = $input['type'];
        $conversation = null;

        if (! empty($input['conversation_id'])) {
            $conversation = Conversation::query()
                ->with(['patient.user', 'psychologist.user'])
                ->find($input['conversation_id']);

            if (! $conversation || ! $this->isParticipant($reporter, $conversation)) {
                throw ValidationException::withMessages([
                    'conversation_id' => __('frontend.reports.invalid_conversation'),
                ]);
            }
        }

        $reportedUserId = $input['reported_user_id'] ?? null;

        if ($type === Report::TYPE_CONVERSATION) {
            if (! $conversation) {
                throw ValidationException::withMessages([
                    'conversation_id' => __('frontend.reports.conversation_required'),
                ]);
            }

            $reportedUserId = $this->peerUserId($conversation, $reporter);
        }

        if ($type === Report::TYPE_USER) {
            if (! $reportedUserId) {
                throw ValidationException::withMessages([
                    'reported_user_id' => __('frontend.reports.user_required'),
                ]);
            }

            $allowedIds = $this->reportableUsers($reporter)->pluck('id')->all();
            if (! in_array((int) $reportedUserId, $allowedIds, true)) {
                throw ValidationException::withMessages([
                    'reported_user_id' => __('frontend.reports.invalid_user'),
                ]);
            }

            if ($conversation && $this->peerUserId($conversation, $reporter) !== (int) $reportedUserId) {
                throw ValidationException::withMessages([
                    'conversation_id' => __('frontend.reports.conversation_mismatch'),
                ]);
            }
        }

        if ((int) $reportedUserId === $reporter->id) {
            throw ValidationException::withMessages([
                'reported_user_id' => __('frontend.reports.cannot_report_self'),
            ]);
        }

        $report = Report::query()->create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reportedUserId,
            'conversation_id' => $conversation?->id,
            'message_id' => $input['message_id'] ?? null,
            'type' => $type,
            'reason' => $input['reason'],
            'description' => $input['description'],
            'status' => Report::STATUS_OPEN,
        ]);

        $this->notifications->notifyAdmins([
            'type' => 'system_alert',
            'title' => __('frontend.notifications.admin_report_title'),
            'body' => __('frontend.notifications.admin_report_body', ['name' => $reporter->name]),
            'data' => [
                'url' => route('admin.reports.show', $report),
                'report_id' => $report->id,
            ],
        ]);

        return $report;
    }

    public function listForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        // Old: only reports the user submitted.
        // return Report::query()
        //     ->with(['reportedUser', 'conversation'])
        //     ->where('reporter_id', $user->id)
        //     ->latest()
        //     ->paginate($perPage);

        return Report::query()
            ->with(['reportedUser', 'conversation', 'reporter'])
            ->where(function (Builder $query) use ($user) {
                $query->where('reporter_id', $user->id)
                    ->orWhere(function (Builder $inner) use ($user) {
                        $inner->where('reported_user_id', $user->id)
                            ->whereHas('comments', function (Builder $comments) {
                                $comments->where('audience', ReportComment::AUDIENCE_REPORTED);
                            });
                    });
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array{status?: string, type?: string, q?: string}  $filters
     */
    public function listForAdmin(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Report::query()
            ->with(['reporter', 'reportedUser', 'assignee', 'conversation'])
            ->when(! empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(! empty($filters['type']), fn (Builder $query) => $query->where('type', $filters['type']))
            ->when(! empty($filters['q']), function (Builder $query) use ($filters) {
                $q = '%'.$filters['q'].'%';
                $query->where(function (Builder $inner) use ($q) {
                    $inner->where('reason', 'like', $q)
                        ->orWhere('description', 'like', $q)
                        ->orWhereHas('reporter', fn (Builder $userQuery) => $userQuery->where('name', 'like', $q)->orWhere('email', 'like', $q))
                        ->orWhereHas('reportedUser', fn (Builder $userQuery) => $userQuery->where('name', 'like', $q)->orWhere('email', 'like', $q));
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function changeStatus(Report $report, string $status, ?int $assignedTo = null): Report
    {
        $updates = ['status' => $status];

        if ($assignedTo !== null) {
            $updates['assigned_to'] = $assignedTo ?: null;
        }

        if ($status === Report::STATUS_RESOLVED && ! $report->resolved_at) {
            $updates['resolved_at'] = now();
        }

        if ($status === Report::STATUS_CLOSED) {
            $updates['closed_at'] = $report->closed_at ?? now();
            if (! $report->resolved_at && empty($updates['resolved_at'])) {
                $updates['resolved_at'] = now();
            }
        }

        $report->fill($updates);
        $report->save();

        return $report->fresh(['reporter', 'reportedUser', 'assignee', 'conversation.patient.user', 'conversation.psychologist.user', 'comments.user']);
    }

    public function assign(Report $report, ?int $assignedTo): Report
    {
        $report->assigned_to = $assignedTo ?: null;
        $report->save();

        return $report->fresh(['assignee']);
    }

    /**
     * @param  string  $audience  reporter|reported|internal
     */
    public function addComment(User $user, Report $report, string $body, string $audience = ReportComment::AUDIENCE_REPORTER): ReportComment
    {
        // Old: addComment(..., bool $internal = false) and a single reporter thread.
        // if (! $user->hasPermissionTo('reports.manage')) {
        //     $internal = false;
        // }

        if (! $user->hasPermissionTo('reports.manage')) {
            $audience = $report->isReportedUser($user)
                ? ReportComment::AUDIENCE_REPORTED
                : ReportComment::AUDIENCE_REPORTER;
        }

        if (! in_array($audience, ReportComment::AUDIENCES, true)) {
            $audience = ReportComment::AUDIENCE_REPORTER;
        }

        $comment = $report->comments()->create([
            'user_id' => $user->id,
            'body' => $body,
            'audience' => $audience,
            'is_internal' => $audience === ReportComment::AUDIENCE_INTERNAL,
        ]);

        $report->loadMissing(['reporter', 'reportedUser']);

        if ($user->hasPermissionTo('reports.manage')) {
            if ($audience === ReportComment::AUDIENCE_REPORTER && $report->reporter) {
                $this->notifyReportParty($report->reporter, $report);
            }

            if ($audience === ReportComment::AUDIENCE_REPORTED && $report->reportedUser) {
                $this->notifyReportParty($report->reportedUser, $report);
            }
        } else {
            $this->notifications->notifyAdmins([
                'type' => 'system_alert',
                'title' => __('frontend.notifications.admin_report_reply_title'),
                'body' => __('frontend.notifications.admin_report_reply_body', [
                    'name' => $user->name,
                    'id' => $report->id,
                ]),
                'data' => [
                    'url' => route('admin.reports.show', $report),
                    'report_id' => $report->id,
                ],
            ]);
        }

        return $comment;
    }

    public function roleOnReport(User $user, Report $report): string
    {
        if ($report->isReportedUser($user) && ! $report->isReporter($user)) {
            return 'reported';
        }

        return 'reporter';
    }

    protected function notifyReportParty(User $party, Report $report): void
    {
        $prefix = $party->isPsychologist()
            ? 'frontend.psychologist'
            : 'frontend.patient';

        $this->notifications->sendPayload($party, [
            'type' => 'report_update',
            'title' => __('frontend.notifications.report_update_title'),
            'body' => __('frontend.notifications.report_update_body', ['id' => $report->id]),
            'data' => [
                'url' => route($prefix.'.reports.show', $report),
                'report_id' => $report->id,
            ],
        ]);
    }

    /**
     * @return Collection<int, User>
     */
    public function assignableAdmins(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('slug', ['admin', 'superadmin']))
            ->orderBy('name')
            ->get();
    }

    public function isParticipant(User $user, Conversation $conversation): bool
    {
        $user->loadMissing(['patient', 'psychologist', 'professional']);

        if ($user->isPatient()) {
            return $conversation->patient_id === $user->patient?->id;
        }

        if ($user->isPsychologist()) {
            $professionalId = $user->psychologist?->id ?? $user->professional?->id;

            return $conversation->psychologist_id === $professionalId;
        }

        return false;
    }

    public function peerUserId(Conversation $conversation, User $reporter): ?int
    {
        $conversation->loadMissing(['patient', 'psychologist']);

        if ($reporter->isPatient() && $conversation->patient_id === $reporter->patient?->id) {
            return $conversation->psychologist?->user_id;
        }

        if ($reporter->isPsychologist()) {
            $professionalId = $reporter->psychologist?->id ?? $reporter->professional?->id;
            if ($conversation->psychologist_id === $professionalId) {
                return $conversation->patient?->user_id;
            }
        }

        return null;
    }

    public function conversationLabel(Conversation $conversation, User $viewer): string
    {
        $peerId = $this->peerUserId($conversation, $viewer);
        $peer = $conversation->patient?->user_id === $peerId
            ? $conversation->patient?->user
            : $conversation->psychologist?->user;
        $name = $peer?->name ?: __('frontend.chat.unknown');

        return '#'.$conversation->id.' — '.$name;
    }
}
