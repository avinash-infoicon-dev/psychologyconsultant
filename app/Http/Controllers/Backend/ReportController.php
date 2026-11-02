<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportComment;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports) {}

    public function index(Request $request): View
    {
        dd('working');
        $filters = $request->only(['status', 'type', 'q']);

        return view('backend.reports.index', [
            'reports' => $this->reports->listForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Report $report): View
    {
        dd('working');
        $report->load([
            'reporter',
            'reportedUser.professional',
            'reportedUser.psychologist',
            'assignee',
            'conversation.patient.user',
            'conversation.psychologist.user',
            'message',
            'comments.user',
        ]);

        $reportedPsychologist = null;
        if ($report->reportedUser?->isPsychologist()) {
            $reportedPsychologist = $report->reportedUser->professional
                ?? $report->reportedUser->psychologist;
        }

        return view('backend.reports.show', [
            'report' => $report,
            'admins' => $this->reports->assignableAdmins(),
            'reportedPsychologist' => $reportedPsychologist,
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        dd('working');
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', Report::STATUSES)],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $this->reports->changeStatus(
            $report,
            $validated['status'],
            array_key_exists('assigned_to', $validated) ? (int) ($validated['assigned_to'] ?? 0) : null
        );

        return back()->with('success', __('backend.reports.updated'));
    }

    public function comment(Request $request, Report $report): RedirectResponse
    {
        dd('working');
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:5000'],
            'audience' => ['required', 'in:'.implode(',', ReportComment::AUDIENCES)],
            // Old: 'is_internal' => ['sometimes', 'boolean'],
        ]);

        $this->reports->addComment(
            $request->user(),
            $report,
            $validated['body'],
            $validated['audience']
        );

        return back()->with('success', __('backend.reports.comment_added'));
    }
}
