<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Exceptions\TwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorVerifyRequest;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login_2fa_email')) {
            return redirect()->route('frontend.auth.login');
        }

        return view('frontend.auth.two-factor', [
            'email' => $request->session()->get('login_2fa_email'),
        ]);
    }

    public function verify(TwoFactorVerifyRequest $request): RedirectResponse
    {
        $email = $request->session()->get('login_2fa_email');

        if (! $email) {
            return redirect()->route('frontend.auth.login');
        }

        try {
            $result = $this->authService->completeTwoFactorLogin(
                $email,
                $request->string('code'),
            );
        } catch (AuthenticationException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        } catch (TwoFactorException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        if ($result['status'] === 'invalid_code') {
            return back()->withErrors(['code' => __('auth_api.two_factor_invalid_code')]);
        }

        $user = $result['user'];

        if (! $user->isPatient() && ! $user->isPsychologist()) {
            $request->session()->forget(['login_2fa_email', 'login_remember']);

            return redirect()
                ->route('frontend.auth.login')
                ->withErrors(['email' => __('frontend.auth.unauthorized_role')]);
        }

        Auth::login($user, (bool) $request->session()->pull('login_remember', false));
        $request->session()->regenerate();
        $request->session()->forget('login_2fa_email');

        if ($user->locale) {
            $request->session()->put('locale', $user->locale);
        }

        return app(LoginController::class)->redirectAfterLogin($request, $user);
    }
}
