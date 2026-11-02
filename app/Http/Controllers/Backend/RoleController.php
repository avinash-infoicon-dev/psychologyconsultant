<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', $q)
                        ->orWhere('slug', 'like', $q)
                        ->orWhere('description', 'like', $q);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('backend.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('backend.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_full_access' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_system' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?: Str::slug($data['name'], '_'),
            'description' => $data['description'] ?? null,
            'is_full_access' => $request->boolean('is_full_access'),
            'is_active' => $request->boolean('is_active', true),
            'is_system' => $request->boolean('is_system'),
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('backend.roles.created'));
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group');
        $role->load('permissions');

        return view('backend.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('roles', 'slug')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'is_full_access' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_system' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($role->is_system && $data['slug'] !== $role->slug) {
            return back()->withErrors(['slug' => __('backend.roles.cannot_rename_system')])->withInput();
        }

        $role->update([
            'name' => $data['name'],
            'slug' => $role->is_system ? $role->slug : $data['slug'],
            'description' => $data['description'] ?? null,
            'is_full_access' => $request->boolean('is_full_access'),
            'is_active' => $request->boolean('is_active'),
            'is_system' => $role->is_system ? true : $request->boolean('is_system'),
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('backend.roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->withErrors(['role' => __('backend.roles.cannot_delete_system')]);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => __('backend.roles.has_users')]);
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('backend.roles.deleted'));
    }
}
