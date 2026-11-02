<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Psychologist;
use App\Services\PsychologistVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PsychologistController extends Controller
{
    public function __construct(protected PsychologistVerificationService $verification) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->input('verification_status');
        // Old filter: $verifiedFilter = $request->input('verified');

        $psychologists = Psychologist::query()
            ->psychologists()
            ->with('user.roles')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where('name', 'like', $q)
                        ->orWhere('email', 'like', $q);
                });
            })
            // Old: ->when(in_array($verifiedFilter, ['0', '1'], true), function ($query) use ($verifiedFilter) {
            //     $query->where('is_verified', $verifiedFilter === '1');
            // })
            ->when(
                in_array($statusFilter, Professional::VERIFICATION_STATUSES, true),
                fn ($query) => $query->where('verification_status', $statusFilter)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('backend.psychologists.index', compact('psychologists'));
    }

    public function show(Psychologist $psychologist): View
    {
        $professional = Professional::query()
            ->with([
                'user.roles',
                'professionalType',
                'specializations.translations',
                'educations' => fn ($q) => $q->latest('year'),
                'experiences' => fn ($q) => $q->latest('start_date'),
                'services',
                'availabilities',
                'currentAbsences',
                'absences' => fn ($q) => $q->orderByDesc('starts_at')->limit(20),
                'reviews' => fn ($q) => $q->latest()->limit(20),
                'galleries' => fn ($q) => $q->orderBy('sort_order'),
                'documents' => fn ($q) => $q->latest(),
                'socialLinks',
                'awards' => fn ($q) => $q->latest('year'),
            ])
            ->whereKey($psychologist->getKey())
            ->firstOrFail();

        return view('backend.psychologists.show', [
            'psychologist' => $psychologist,
            'professional' => $professional,
            // History moved to the verification page:
            // 'verificationLogs' => $this->verification->verificationHistory($professional),
        ]);
    }

    public function verifications(Request $request): View
    {
        $statusFilter = $request->input('verification_status');

        $psychologists = Psychologist::query()
            ->psychologists()
            ->with('user.roles')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where('name', 'like', $q)
                        ->orWhere('email', 'like', $q);
                });
            })
            ->when(
                in_array($statusFilter, Professional::VERIFICATION_STATUSES, true),
                fn ($query) => $query->where('verification_status', $statusFilter),
                fn ($query) => $query->whereIn('verification_status', ['submitted', 'under_review', 'rejected', 'suspended'])
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('backend.psychologists.verifications', compact('psychologists'));
    }

    public function verification(Psychologist $psychologist): View
    {
        $professional = Professional::query()
            ->with('user.roles')
            ->whereKey($psychologist->getKey())
            ->firstOrFail();

        return view('backend.psychologists.verification', [
            'psychologist' => $psychologist,
            'professional' => $professional,
            'verificationLogs' => $this->verification->paginatedVerificationHistory($professional),
        ]);
    }

    public function startReview(Psychologist $psychologist): RedirectResponse
    {
        $this->verification->startReview($psychologist);

        return back()->with('success', __('backend.psychologists.started_review'));
    }

    public function verify(Psychologist $psychologist): RedirectResponse
    {
        $this->verification->verify($psychologist);

        return back()->with('success', __('backend.psychologists.verified_ok'));
    }

    public function reject(Request $request, Psychologist $psychologist): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $this->verification->reject($psychologist, $validated['reason']);

        return back()->with('success', __('backend.psychologists.rejected_ok'));
    }

    public function suspend(Request $request, Psychologist $psychologist): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $this->verification->suspend($psychologist, $validated['reason']);

        return back()->with('success', __('backend.psychologists.suspended_ok'));
    }

    public function restore(Psychologist $psychologist): RedirectResponse
    {
        $this->verification->restore($psychologist);

        return back()->with('success', __('backend.psychologists.restored_ok'));
    }

    /*
    public function approve(Psychologist $psychologist): RedirectResponse
    {
        $psychologist->loadMissing('user');

        $psychologist->update([
            'is_verified' => true,
            'is_accepting_patients' => true,
        ]);

        Professional::query()
            ->where('user_id', $psychologist->user_id)
            ->update([
                'is_verified' => true,
                'is_accepting_patients' => true,
                'status' => true,
            ]);

        $this->mailTemplates->sendPsychologistStatusMail($psychologist, 'psychologist_approved');

        if ($psychologist->user) {
            $notifications = app(\App\Services\NotificationService::class);
            $notifications->sendPayload(
                $psychologist->user,
                \App\Notifications\AccountStatusNotification::profileApproved(),
            );
            $notifications->sendPayload(
                $psychologist->user,
                \App\Notifications\AccountStatusNotification::changed(__('frontend.notifications.account_status_activated')),
            );
        }

        return back()->with('success', __('backend.psychologists.approved'));
    }

    public function revoke(Psychologist $psychologist): RedirectResponse
    {
        $psychologist->loadMissing('user');

        $psychologist->update([
            'is_verified' => false,
            'is_accepting_patients' => false,
        ]);

        Professional::query()
            ->where('user_id', $psychologist->user_id)
            ->update([
                'is_verified' => false,
                'is_accepting_patients' => false,
            ]);

        $this->mailTemplates->sendPsychologistStatusMail($psychologist, 'psychologist_revoked');

        if ($psychologist->user) {
            app(\App\Services\NotificationService::class)->sendPayload(
                $psychologist->user,
                \App\Notifications\AccountStatusNotification::changed(__('frontend.notifications.account_status_deactivated')),
            );
        }

        return back()->with('success', __('backend.psychologists.revoked'));
    }
    */
}
