<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecuritySettingsController extends Controller
{
    public function edit(): View
    {
        $roles = Role::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $twoFactorEnabled = Setting::get(
            'security.two_factor.enabled',
            config('security.two_factor.enabled', true),
        );

        $requiredForRoles = Setting::get(
            'security.two_factor.required_for_roles',
            config('security.two_factor.required_for_roles', []),
        );

        return view('backend.settings.security', compact('roles', 'twoFactorEnabled', 'requiredForRoles'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'two_factor_enabled' => ['nullable', 'boolean'],
            'required_for_roles' => ['nullable', 'array'],
            'required_for_roles.*' => ['string', 'exists:roles,slug'],
        ]);

        Setting::set(
            'security.two_factor.enabled',
            $request->boolean('two_factor_enabled'),
            'boolean',
            'security',
        );

        Setting::set(
            'security.two_factor.required_for_roles',
            $data['required_for_roles'] ?? [],
            'json',
            'security',
        );

        return redirect()
            ->route('admin.settings.security')
            ->with('success', __('backend.settings.security.updated'));
    }
}
