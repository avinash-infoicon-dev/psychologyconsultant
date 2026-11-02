<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Setting::orderBy('group')->orderBy('key')->get()]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['required'],
            'settings.*.type' => ['sometimes', 'string', 'in:string,integer,boolean,json'],
            'settings.*.group' => ['sometimes', 'string'],
        ]);

        $updated = [];

        foreach ($data['settings'] as $item) {
            $updated[] = Setting::set(
                $item['key'],
                $item['value'],
                $item['type'] ?? 'string',
                $item['group'] ?? 'general',
            );
        }

        return response()->json(['data' => $updated]);
    }
}
