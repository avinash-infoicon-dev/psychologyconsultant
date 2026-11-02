<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(20);

        return view('backend.permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        $groups = Permission::query()->distinct()->orderBy('group')->pluck('group')->filter()->values();

        return view('backend.permissions.create', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:permissions,slug'],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Permission::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?: Str::slug($data['name'], '.'),
            'group' => $data['group'] ?: 'general',
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('backend.permissions.created'));
    }

    public function edit(Permission $permission): View
    {
        $groups = Permission::query()->distinct()->orderBy('group')->pluck('group')->filter()->values();

        return view('backend.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions', 'slug')->ignore($permission->id)],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'group' => $data['group'] ?: 'general',
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('backend.permissions.updated'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->roles()->detach();
        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('backend.permissions.deleted'));
    }
}
