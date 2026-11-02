<?php

namespace App\Http\Controllers\Frontend;

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
        $items = $request->user()->notifications()->latest()->paginate(20);

        return view('frontend.notifications.index', [
            'notifications' => $items,
            'unreadCount' => $request->user()->notifications()->whereNull('read_at')->count(),
            'preferenceRows' => $this->notifications->userPreferenceRows($request->user()),
            'prefFormId' => 'fe-notif-pref-history',
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->notifications->inboxSummary($request->user()),
        ]);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['data' => $this->notifications->toFeedItem($notification->fresh())]);
        }

        return back();
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $this->notifications->markAllRead($request->user());

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['data' => ['unread_count' => 0]]);
        }

        return back()->with('success', __('frontend.notifications.marked_all_read'));
    }

    public function destroy(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->delete();

        return back()->with('success', __('frontend.notifications.deleted'));
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $editable = $this->notifications->editablePreferenceKeys($request->user());

        $request->validate(
            collect($editable)->mapWithKeys(fn (string $key) => [$key => ['sometimes', 'boolean']])->all()
        );

        $values = [];
        foreach ($editable as $key) {
            $values[$key] = $request->boolean($key);
        }

        $this->notifications->updatePreferences($request->user(), $values);

        return back()->with('success', __('frontend.notifications.preferences_saved'));
    }
}
