<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\JwtService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(protected JwtService $jwt) {}

    public function create(): View
    {
        return view('frontend.auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->only('email'));

        return back()->with('success', __('auth_api.password_reset_sent'));
    }

    public function edit(Request $request, string $token): View
    {
        return view('frontend.auth.reset-password', [
            'token' => $token,
            'email' => old('email', $request->string('email')->toString()),
        ]);
    }

    public function update(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'password_changed_at' => now(),
                    'remember_token' => Str::random(60),
                ])->save();

                $this->jwt->revokeAllForUser($user->id);

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()
            ->route('frontend.auth.login')
            ->with('success', __('auth_api.password_reset_success'));
    }
}
