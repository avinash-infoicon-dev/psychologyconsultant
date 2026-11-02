<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Exceptions\TwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\ResendEmailVerificationRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function show(Request $request): View
    {
        return view('frontend.auth.verify-email', [
            'email' => $request->string('email'),
        ]);
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        try {
            $result = $this->authService->verifyEmail(
                $request->string('email'),
                $request->string('code'),
            );
        } catch (TwoFactorException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        if ($result['status'] === 'invalid_code') {
            return back()->withErrors(['code' => __('auth_api.email_verify_invalid_code')]);
        }

        // Old: psychologists were sent back to login until admin verified.
        // $user = $result['user']->loadMissing('psychologist', 'roles');
        // if ($user->isPsychologist() && ! $user->psychologist?->is_verified) {
        //     return redirect()
        //         ->route('frontend.auth.login')
        //         ->with('success', __('auth_api.email_verified_awaiting_admin'));
        // }

        return redirect()
            ->route('frontend.auth.login')
            ->with('success', __('auth_api.email_verified'));
    }

    public function resend(ResendEmailVerificationRequest $request): RedirectResponse
    {
        $this->authService->resendEmailVerification($request->string('email'));

        return back()->with('success', __('auth_api.email_verify_resent'));
    }
}
