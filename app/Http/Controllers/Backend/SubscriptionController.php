<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $statuses = ['trialing', 'active', 'past_due', 'cancelled', 'expired'];

        $subscriptions = Subscription::query()
            ->with(['patient.user', 'plan.translations'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('patient.user', function ($userQuery) use ($q) {
                    $userQuery->where('name', 'like', $q)
                        ->orWhere('email', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), $statuses, true), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('plan_id'), function ($query) use ($request) {
                $query->where('plan_id', $request->integer('plan_id'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $plans = Plan::query()->with('translations')->orderBy('sort_order')->orderBy('name')->get();

        return view('backend.subscriptions.index', compact('subscriptions', 'plans', 'statuses'));
    }
}
