<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,support')
     * for "any of these roles".
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('frontend.auth.login'));
        }

        if (! $user->hasAnyRbacRole($roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'You do not have access to this resource.'], 403);
            }

            // Send the user to their own dashboard instead of a dead-end 403 page
            // (common after login when session still holds a cross-role "intended" URL).
            if ($user->isPsychologist()) {
                return redirect()
                    ->route('frontend.psychologist.dashboard')
                    ->with('error', __('backend.auth.forbidden'));
            }

            if ($user->isPatient()) {
                return redirect()
                    ->route('frontend.patient.dashboard')
                    ->with('error', __('backend.auth.forbidden'));
            }

            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('frontend.auth.login');
        }

        if (! $user->is_active) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'This account has been deactivated.'], 403);
            }

            abort(403, __('backend.auth.inactive'));
        }
        // Previous experiment: inactive psychologists could still open the dashboard.
        // if (! $user->is_active) {
        //     if (! $user->isPsychologist()) {
        //         abort(403, __('backend.auth.inactive'));
        //     }
        // }

        if ($user->isPsychologist() && $user->isPsychologistSuspended()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => __('auth_api.psychologist_suspended')], 403);
            }

            abort(403, __('auth_api.psychologist_suspended'));
        }
        // Old patient-facing copy on the psychologist dashboard:
        // abort(403, __('frontend.chat.psychologist_unavailable'));

        return $next($request);
    }
}
