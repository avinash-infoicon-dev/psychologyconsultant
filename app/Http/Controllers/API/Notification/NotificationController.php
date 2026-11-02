<?php

namespace App\Http\Controllers\API\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(30);

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'unread_count' => $request->user()->notifications()->whereNull('read_at')->count(),
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'unread_count' => $request->user()->notifications()->whereNull('read_at')->count(),
            ],
        ]);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->markAsRead();

        return response()->json(['data' => $notification->fresh()]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $updated = $this->notifications->markAllRead($request->user());

        return response()->json([
            'data' => ['updated' => $updated],
        ]);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->delete();

        return response()->json(['message' => 'ok']);
    }

    public function preferences(Request $request): JsonResponse
    {
        // Old: returned admin channelSettings() only.
        // return response()->json([
        //     'data' => $this->notifications->channelSettings(),
        // ]);

        $user = $request->user();

        return response()->json([
            'data' => [
                'admin' => $this->notifications->channelSettings(),
                'rows' => $this->notifications->userPreferenceRows($user),
                'editable' => $this->notifications->editablePreferenceKeys($user),
                'user' => $this->notifications->preferencesFor($user),
            ],
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        // Old: patients/psychologists could save per-user NotificationPreference rows.
        // $validated = $request->validate(...);
        // return response()->json(['data' => $this->notifications->updatePreferences($request->user(), $validated)]);
        // Old: admin-only, always 403.
        // return response()->json([
        //     'message' => 'Notification channels are configured by an administrator.',
        // ], 403);

        $editable = $this->notifications->editablePreferenceKeys($request->user());
        $request->validate(
            collect($editable)->mapWithKeys(fn (string $key) => [$key => ['sometimes', 'boolean']])->all()
        );

        $values = [];
        foreach ($editable as $key) {
            $values[$key] = $request->boolean($key);
        }

        return response()->json([
            'data' => $this->notifications->updatePreferences($request->user(), $values),
        ]);
    }
}
