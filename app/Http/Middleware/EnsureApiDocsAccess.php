<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict Scramble API docs (/docs/api) to authenticated admin / superadmin only.
 */
class EnsureApiDocsAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('admin'));
        }

        if (! $user->is_active) {
            auth()->logout();

            return redirect()
                ->route('admin')
                ->withErrors(['email' => __('backend.auth.inactive')]);
        }

        if (! $user->hasAnyRbacRole(['superadmin', 'admin'])) {
            abort(403, __('backend.auth.forbidden'));
        }

        return $next($request);
    }
}
