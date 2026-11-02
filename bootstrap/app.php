<?php

use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\CheckCountryRestriction;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureBackendAccess;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SetApiLocale;
use App\Http\Middleware\SetLocale;
use App\Support\Locales;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/Backend/Backendwebroute.php'));

            Route::middleware('web')
                ->group(base_path('routes/Frontend/Frontendwebroute.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/API/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', SetLocale::class);
        $middleware->appendToGroup('api', SetApiLocale::class);

        // Honour X-Forwarded-Proto when https is terminated in front of PHP.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth.jwt' => AuthenticateJwt::class,
            'backend.access' => EnsureBackendAccess::class,
            'geoblock' => CheckCountryRestriction::class,
            'permission' => CheckPermission::class,
            'role' => EnsureUserHasRole::class,
            'verify.webhook.signature' => \App\Http\Middleware\VerifyWebhookSignature::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin');
            }

            return route('frontend.auth.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.dashboard');
            }

            $user = $request->user();

            if ($user?->isPsychologist()) {
                return route('frontend.psychologist.dashboard');
            }

            if ($user?->isPatient()) {
                return route('frontend.patient.dashboard');
            }

            return route('frontend.home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            Locales::applyFromRequest($request, true);

            return null;
        });
    })->create();
