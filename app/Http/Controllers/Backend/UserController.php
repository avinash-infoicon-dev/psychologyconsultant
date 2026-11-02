<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(protected TwoFactorService $twoFactor) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['roles', 'psychologist'])
            ->whereHas('roles', function ($query) {$query->where('is_active', true);})
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', $q)
                        ->orWhere('email', 'like', $q)
                        ->orWhere('phone', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('backend.users.index', [
            'users' => $users,
            'twoFactor' => $this->twoFactor,
            'twoFactorGloballyEnabled' => $this->twoFactor->isGloballyEnabled(),
        ]);
    }

    public function create(): View
    {
        $roles = Role::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('backend.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', Rule::in(Locales::codes())],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? config('app.locale'),
            'password' => $data['password'],
            'is_active' => $request->boolean('is_active', true),
            'password_changed_at' => now(),
        ]);

        $user->roles()->sync($data['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('backend.users.created'));
    }

    public function edit(User $user): View
    {
        $roles = Role::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $user->load('roles');

        $twoFactorPolicy = $this->resolveTwoFactorPolicy($user);

        return view('backend.users.edit', compact('user', 'roles', 'twoFactorPolicy'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'locale' => ['nullable', 'string', Rule::in(Locales::codes())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['nullable', 'boolean'],
            'two_factor_policy' => ['nullable', 'string', 'in:default,required,exempt'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? $user->locale,
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
            $payload['password_changed_at'] = now();
        }

        $user->update($payload);
        $this->applyTwoFactorPolicy($user, $data['two_factor_policy'] ?? 'default');
        $user->roles()->sync($data['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('backend.users.updated'));
    }

    public function toggleTwoFactor(User $user): RedirectResponse
    {
        if ($this->twoFactor->isRequiredFor($user)) {
            $user->update([
                'two_factor_enabled' => false,
                'two_factor_exempt' => true,
            ]);

            return back()->with('success', __('backend.users.two_factor_disabled'));
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_exempt' => false,
        ]);

        return back()->with('success', __('backend.users.two_factor_enabled_msg'));
    }

    protected function applyTwoFactorPolicy(User $user, string $policy): void
    {
        match ($policy) {
            'required' => $user->update(['two_factor_enabled' => true, 'two_factor_exempt' => false]),
            'exempt' => $user->update(['two_factor_enabled' => false, 'two_factor_exempt' => true]),
            default => $user->update(['two_factor_enabled' => false, 'two_factor_exempt' => false]),
        };
    }

    protected function resolveTwoFactorPolicy(User $user): string
    {
        if ($user->two_factor_exempt) {
            return 'exempt';
        }

        if ($user->two_factor_enabled) {
            return 'required';
        }

        return 'default';
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => __('backend.users.cannot_delete_self')]);
        }

        if ($user->hasRbacRole('superadmin') && User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'superadmin'))->count() <= 1) {
            return back()->withErrors(['user' => __('backend.users.cannot_delete_last_superadmin')]);
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('backend.users.deleted'));
    }
}
