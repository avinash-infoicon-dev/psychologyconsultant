<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->hasAnyRbacRole(['superadmin', 'admin', 'psychologist', 'legal_advisor'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('backend.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => __('backend.auth.failed')]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_active || ! $user->hasAnyRbacRole(['superadmin', 'admin', 'psychologist', 'legal_advisor'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('backend.auth.unauthorized')]);
        }

        if ($user->locale) {
            $request->session()->put('locale', $user->locale);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin');
    }
}
