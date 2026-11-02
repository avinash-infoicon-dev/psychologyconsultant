<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Payment;
// use App\Models\Permission;
use App\Models\Psychologist;
// use App\Models\Role;
use App\Models\Subscription;
use App\Services\Messaging\ExchangeLedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ExchangeLedgerService $ledger) {}

    public function index(): View|RedirectResponse
    {
        $user = auth()->user()->load('roles');
        $role = $user->primaryRole();

        // Previous RBAC-focused stats (kept for reference)
        // $stats = [
        //     'users' => User::query()->count(),
        //     'patients' => User::whereHas('roles', function ($query) {
        //         $query->where('slug', 'patient')->where('is_active', true);
        //     })->count(),
        //     'Psychologists' => User::whereHas('roles', function ($query) {
        //         $query->where('slug', 'psychologist')->where('is_active', true);
        //     })->count(),
        //     'Legal Advisors' => User::whereHas('roles', function ($query) {
        //         $query->where('slug', 'legal_advisor')->where('is_active', true);
        //     })->count(),
        //     'roles' => Role::query()->count(),
        //     'permissions' => Permission::query()->count(),
        //     'active_users' => User::query()->where('is_active', true)->count(),
        // ];

        // Same metrics as API Admin DashboardController
        $monthStart = now()->startOfMonth();
        $exchangesReady = Schema::hasTable('exchanges');

        $stats = [
            'patients' => [
                'total' => Patient::count(),
                'new_this_month' => Patient::where('created_at', '>=', $monthStart)->count(),
            ],
            'psychologists' => [
                'total' => Psychologist::psychologists()->count(),
                'verified' => Psychologist::psychologists()->where('verification_status', Psychologist::VERIFICATION_VERIFIED)->count(),
                'pending_verification' => Psychologist::psychologists()->whereIn('verification_status', [
                    Psychologist::VERIFICATION_SUBMITTED,
                    Psychologist::VERIFICATION_UNDER_REVIEW,
                ])->count(),
                'accepting_patients' => Psychologist::psychologists()->where('is_accepting_patients', true)->count(),
                // Verified professionals are treated as active on the platform.
                'active' => Psychologist::psychologists()->where('verification_status', Psychologist::VERIFICATION_VERIFIED)->count(),
            ],
            'subscriptions' => [
                'active' => Subscription::where('status', 'active')->count(),
                'new_this_month' => Subscription::where('created_at', '>=', $monthStart)->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'cancelled_this_month' => Subscription::where('status', 'cancelled')
                    ->where('cancelled_at', '>=', $monthStart)->count(),
            ],
            'activity' => [
                'active_conversations' => \App\Models\Conversation::where('status', 'active')->count(),
                'new_requests' => \App\Models\PatientPsychologistRelationship::where('requested_at', '>=', $monthStart)->count(),
                'pending_requests' => \App\Models\PatientPsychologistRelationship::where('status', 'pending')->count(),
                'exchanges_consumed' => $exchangesReady
                    ? \App\Models\Exchange::where('status', \App\Models\Exchange::STATUS_COMPLETED)->count()
                    : 0,
                'exchanges_consumed_month' => $exchangesReady
                    ? \App\Models\Exchange::query()
                        ->where('status', \App\Models\Exchange::STATUS_COMPLETED)
                        ->where('completed_at', '>=', $monthStart)
                        ->count()
                    : 0,
            ],
            'revenue' => [
                'this_month' => (int) Payment::where('status', 'succeeded')
                    ->where('paid_at', '>=', $monthStart)
                    ->sum('amount'),
                'last_30_days' => (int) Payment::where('status', 'succeeded')
                    ->where('paid_at', '>=', now()->subDays(30))
                    ->sum('amount'),
            ],
            'messages' => [
                'sent_today' => Message::where('created_at', '>=', now()->startOfDay())->count(),
            ],
            'exchanges' => $this->ledger->overviewStats(),
        ];

        return match ($role?->slug) {
            'superadmin' => view('backend.dashboard.superadmin', compact('user', 'stats')),
            'admin' => view('backend.dashboard.admin', compact('user', 'stats')),
            'psychologist' => view('backend.dashboard.psychologist', compact('user', 'stats')),
            'legal_advisor' => view('backend.dashboard.legal_advisor', compact('user', 'stats')),
            default => redirect()->route('admin.logout'),
        };
    }
}
