<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogsController extends Controller
{
    public function activity(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->integer('user_id'));
            })
            ->when($request->filled('action'), function ($query) use ($request) {
                $query->where('action', 'like', '%'.$request->input('action').'%');
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('action', 'like', $q)
                        ->orWhere('description', 'like', $q)
                        ->orWhere('ip_address', 'like', $q)
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', $q)
                                ->orWhere('email', 'like', $q);
                        });
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('backend.logs.activity', compact('logs'));
    }

    public function audit(Request $request): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($request->filled('auditable_type'), function ($query) use ($request) {
                $query->where('auditable_type', 'like', '%'.$request->input('auditable_type').'%');
            })
            ->when($request->filled('auditable_id'), function ($query) use ($request) {
                $query->where('auditable_id', $request->integer('auditable_id'));
            })
            ->when($request->filled('event') && in_array($request->input('event'), ['created', 'updated', 'deleted', 'restored'], true), function ($query) use ($request) {
                $query->where('event', $request->input('event'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('auditable_type', 'like', $q)
                        ->orWhere('event', 'like', $q)
                        ->orWhere('ip_address', 'like', $q)
                        ->orWhereHas('user', function ($userQuery) use ($q) {
                            $userQuery->where('name', 'like', $q)
                                ->orWhere('email', 'like', $q);
                        });
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('backend.logs.audit', compact('logs'));
    }
}
