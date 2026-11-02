<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Psychologist;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $monthStart = now()->startOfMonth();
        $exchangesReady = Schema::hasTable('exchanges');

        return response()->json([
            'data' => [
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
            ],
        ]);
    }
}
