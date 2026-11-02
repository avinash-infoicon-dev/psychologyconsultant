<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function index(Request $request): View
    {
        $items = $request->user()->notifications()->latest()->paginate(30);
        return view('backend.notifications.index', [
            'notifications' => $items,
            'unreadCount' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->notifications->inboxSummary($request->user()),
        ]);
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->notifications->markAllRead($request->user());

        return back()->with('success', __('backend.notifications.marked_all_read'));
    }
}
