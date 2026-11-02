<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\CountryRestriction;
use App\Models\Professional;
use App\Models\Specialization;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function create(): View
    {
        return view('frontend.auth.register', [
            'allowedCountries' => CountryRestriction::allowedOptions(),
            'specializations' => Specialization::query()
                ->with('translations')
                ->selectable()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function emailAvailable(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $taken = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['email'])])
            ->exists();

        return response()->json([
            'available' => ! $taken,
            'message' => $taken ? __('auth_api.email_already_registered') : null,
            'login_url' => route('frontend.auth.login'),
            'login_label' => __('frontend.nav.login'),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $user = $this->authService->register($data);
        } catch (AuthenticationException $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }

        $user->loadMissing('professional');
        $isSubmitted = $user->professional
            && $user->professional->verification_status !== Professional::VERIFICATION_DRAFT;

        $message = $user->hasRbacRole('psychologist')
            ? (
                $isSubmitted
                    ? __('auth_api.register_psychologist_pending')
                    : __('auth_api.register_psychologist_draft')
            )
            : __('auth_api.register_verify_email');

        return redirect()
            ->route('frontend.auth.verify-email', ['email' => $user->email])
            ->with('success', $message);
    }
}
