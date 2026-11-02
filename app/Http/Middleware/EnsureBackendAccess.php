<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackendAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            auth()->logout();

            return redirect()
                ->route('admin')
                ->withErrors(['email' => __('backend.auth.inactive')]);
        }

        if (! $user->hasAnyRbacRole(['superadmin', 'admin', 'psychologist', 'legal_advisor'])) {
            auth()->logout();

            return redirect()
                ->route('admin')
                ->withErrors(['email' => __('backend.auth.unauthorized')]);
        }

        return $next($request);
    }
}
