<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        return view('frontend.reports.index', [
            'reports' => $this->reports->listForUser($request->user()),
            'routePrefix' => $this->routePrefix($request->user()),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Report::class);

        $user = $request->user();
        $conversations = $this->reports->reportableConversations($user);
        $peers = $this->reports->reportableUsers($user);

        $selectedConversationId = null;
        if ($request->filled('conversation')) {
            $match = $conversations->first(
                fn (Conversation $item) => $item->uuid === $request->string('conversation')->toString()
                    || (string) $item->id === $request->string('conversation')->toString()
            );
            $selectedConversationId = $match?->id;
        }

        return view('frontend.reports.create', [
            'conversations' => $conversations,
            'peers' => $peers,
            'reportsService' => $this->reports,
            'selectedType' => $request->string('type')->toString() ?: Report::TYPE_CONVERSATION,
            'selectedConversationId' => $selectedConversationId,
            'selectedUserId' => $request->integer('user') ?: null,
            'routePrefix' => $this->routePrefix($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', Report::TYPES)],
            'reason' => ['required', 'in:'.implode(',', Report::REASONS)],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'conversation_id' => ['nullable', 'integer', 'exists:conversations,id'],
            'reported_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $report = $this->reports->create($request->user(), $validated);

        return redirect()
            ->route($this->routePrefix($request->user()).'.reports.show', $report)
            ->with('success', __('frontend.reports.created'));
    }

    public function show(Request $request, Report $report): View
    {
        $this->authorize('view', $report);

        // Old: loaded every non-internal comment and always showed case details.
        // $report->load([
        //     'reportedUser',
        //     'conversation.patient.user',
        //     'conversation.psychologist.user',
        //     'comments' => fn ($query) => $query->where('is_internal', false)->with('user')->oldest(),
        // ]);

        $roleOnReport = $this->reports->roleOnReport($request->user(), $report);
        $audience = $roleOnReport === 'reported'
            ? ReportComment::AUDIENCE_REPORTED
            : ReportComment::AUDIENCE_REPORTER;

        $comments = fn ($query) => $query->where('audience', $audience)->with('user')->oldest();

        if ($roleOnReport === 'reported') {
            $report->load(['comments' => $comments]);
        } else {
            $report->load([
                'reportedUser',
                'conversation.patient.user',
                'conversation.psychologist.user',
                'comments' => $comments,
            ]);
        }

        return view('frontend.reports.show', [
            'report' => $report,
            'roleOnReport' => $roleOnReport,
            'audience' => $audience,
            'routePrefix' => $this->routePrefix($request->user()),
        ]);
    }

    public function comment(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('comment', $report);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $roleOnReport = $this->reports->roleOnReport($request->user(), $report);
        $audience = $roleOnReport === 'reported'
            ? ReportComment::AUDIENCE_REPORTED
            : ReportComment::AUDIENCE_REPORTER;

        $this->reports->addComment($request->user(), $report, $validated['body'], $audience);

        return back()->with('success', __('frontend.reports.comment_added'));
    }

    protected function routePrefix(User $user): string
    {
        return $user->isPsychologist() ? 'frontend.psychologist' : 'frontend.patient';
    }
}
