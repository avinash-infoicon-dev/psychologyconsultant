<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChangedNotification;
use App\Services\NotificationService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

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

    /**
     * Upload, replace or remove the admin's profile photo.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $oldPath = $user->avatar_path;

        // Remove existing photo when asked, or when replacing it with a new upload.
        if ($request->boolean('remove_avatar') || $request->hasFile('avatar')) {
            $this->deleteStoredAvatar($oldPath);
            $user->forceFill(['avatar_path' => null])->save();
        }

        if ($request->hasFile('avatar')) {
            $stored = $request->file('avatar')->store('users/'.$user->id.'/avatar', 'public');
            $user->forceFill(['avatar_path' => 'storage/'.$stored])->save();
        }

        return redirect()
            ->route('admin.profile.show')
            ->with('success', __('backend.profile.avatar_updated'));
    }

    protected function deleteStoredAvatar(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'storage/')) {
            return;
        }

        $relative = substr($path, strlen('storage/'));

        // Only ever delete files inside our managed uploads directory.
        if (str_starts_with($relative, 'users/')) {
            Storage::disk('public')->delete($relative);
        }
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
        $this->notifications->sendPayload($user, PasswordChangedNotification::payload());

        return redirect()
            ->route('admin.profile.show')
            ->with('success', __('backend.profile.password_updated'));
    }
}
