<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user && ($user->isPatient() || $user->isPsychologist())) {
            return $this->redirectAfterLogin(request(), $user);
        }

        return view('frontend.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $result = $this->authService->attemptLogin(
                $credentials['email'],
                $credentials['password'],
            );
        } catch (AuthenticationException $e) {
             if ($e->getMessage() === __('auth_api.email_not_verified')) {
                    return redirect()
                        ->route('frontend.auth.verify-email', [
                            'email' => $credentials['email'],
                        ])
                        ->with('error', $e->getMessage());
                }
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => $e->getMessage()]);
        }

        if ($result['status'] === 'requires_2fa') {
            $request->session()->put('login_2fa_email', $credentials['email']);
            $request->session()->put('login_remember', $request->boolean('remember'));

            return redirect()->route('frontend.auth.two-factor');
        }

        $user = $result['user'];

        if (! $user->isPatient() && ! $user->isPsychologist()) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('frontend.auth.unauthorized_role')]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->locale) {
            $request->session()->put('locale', $user->locale);
        }

        return $this->redirectAfterLogin($request, $user);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('frontend.home')
            ->with('success', __('frontend.auth.logged_out'));
    }

    public function redirectAfterLogin(Request $request, $user): RedirectResponse
    {
        $default = $this->dashboardRouteFor($user);
        $intended = $request->session()->pull('url.intended');

        if (is_string($intended) && $this->intendedMatchesRole($intended, $user)) {
            return redirect()->to($intended);
        }

        return redirect()->to($default);
    }

    protected function dashboardRouteFor($user): string
    {
        if ($user->isPsychologist()) {
            return route('frontend.psychologist.dashboard');
        }

        if ($user->isPatient()) {
            return route('frontend.patient.dashboard');
        }

        return route('frontend.home');
    }

    /**
     * Ignore stored "intended" URLs from the other role area (e.g. a psychologist
     * must not be sent to /patient/... after login — that yields a 403).
     */
    protected function intendedMatchesRole(string $url, $user): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if ($user->isPsychologist()) {
            return str_contains($path, '/psychologist');
        }

        if ($user->isPatient()) {
            return str_contains($path, '/patient');
        }

        return false;
    }
}
