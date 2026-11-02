<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwt
{
    public function __construct(private JwtService $jwt)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => __('auth_api.unauthenticated')], 401);
        }

        $user = $this->jwt->userFromToken($token);

        if (! $user || ! $user->is_active || ! $user->canLoginToApi()) {
            return response()->json(['message' => __('auth_api.unauthenticated')], 401);
        }

        // Request user (for $request->user()) and Auth guard (for Gate / $this->authorize()).
        $request->setUserResolver(fn () => $user);
        Auth::setUser($user);

        if (! $request->query('locale') && ! $request->header('Accept-Language') && $user->locale && Locales::isSupported($user->locale)) {
            App::setLocale($user->locale);
        }

        return $next($request);
    }
}
