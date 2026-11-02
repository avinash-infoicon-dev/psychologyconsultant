<?php

namespace App\Http\Controllers\API\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['ios', 'android', 'web'])],
            'fcm_token' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $device = $request->user()->devices()->updateOrCreate(
            ['fcm_token' => $data['fcm_token']],
            [...$data, 'last_used_at' => now()]
        );

        return response()->json(['data' => $device], 201);
    }

    public function destroy(Request $request, string $fcmToken): JsonResponse
    {
        $request->user()->devices()->where('fcm_token', $fcmToken)->delete();

        return response()->json(['message' => 'Device unregistered.']);
    }
}
