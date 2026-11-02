<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct(private JwtService $jwt)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->string('role')));
        }

        if ($request->filled('search')) {
            $term = "%{$request->string('search')}%";
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
        }

        $users = $query->latest()->paginate(30);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /** Admin-created accounts — typically support/admin staff, not patients/psychologists (they self-register). */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'support'])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRbacRole($data['role']);

        ActivityLog::record('admin.user.created', $user, ['role' => $data['role']]);

        return response()->json(['data' => new UserResource($user)], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => new UserResource($user->load('roles'))]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->update($data);

        ActivityLog::record('admin.user.updated', $user, $data);

        return response()->json(['data' => new UserResource($user->fresh())]);
    }

    /** Soft-deletes — clinical/financial history must survive account removal. */
    public function destroy(User $user): JsonResponse
    {
        $this->jwt->revokeAllForUser($user->id);
        $user->delete();

        ActivityLog::record('admin.user.deleted', $user);

        return response()->json(['message' => 'User deactivated and removed.']);
    }
}
