<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user()->load('roles');

        return view('backend.profile.show', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            //'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', Rule::in(Locales::codes())],
        ]);

        $user->update([
            'name' => $data['name'],
            //'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? $user->locale,
        ]);

        if (! empty($data['locale'])) {
            $request->session()->put('locale', $data['locale']);
        }

        return redirect()
            ->route('admin.profile.show')
            ->with('success', __('backend.profile.updated'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('backend.profile.current_password_incorrect')],
            ]);
        }

        $user->update([
            'password' => $data['password'],
            'password_changed_at' => now(),
        ]);

        return redirect()
            ->route('admin.profile.show')
            ->with('success', __('backend.profile.password_updated'));
    }
}
