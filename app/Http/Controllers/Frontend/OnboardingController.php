<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CountryRestriction;
use App\Models\Plan;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function plans(): View|RedirectResponse
    {
        if (auth()->user()?->isPatient()) {
            return redirect()->route('frontend.patient.subscription.plans');
        }

        $plans = Plan::query()
            ->with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.onboarding.plans', [
            'plans' => $plans,
        ]);
    }

    public function account(Plan $plan): RedirectResponse
    {
        if (! $plan->is_active) {
            return redirect()
                ->route('frontend.onboarding.plans')
                ->withErrors(['plan' => __('frontend.subscription.plan_unavailable')]);
        }

        $user = auth()->user();

        if ($user?->isPatient()) {
            return redirect()->route('frontend.patient.subscription.plans');
        }

        if ($user?->isPsychologist()) {
            return redirect()
                ->route('frontend.onboarding.plans')
                ->with('onboarding_notice', __('frontend.onboarding.psychologist_notice'));
        }

        if ($user) {
            return redirect()
                ->route('frontend.onboarding.plans')
                ->with('onboarding_notice', __('frontend.onboarding.patient_account_required'));
        }

        session()->put('url.intended', route('frontend.patient.subscription.checkout', [
            'plan' => $plan,
            'intent' => $plan->isPerExchange() ? 'addon' : 'subscribe',
        ]));

        return redirect()->route('frontend.auth.register');

        // Custom account form is unused. Registration uses the existing page.
        // $plan->loadMissing('translations');
        // return view('frontend.onboarding.account', [
        //     'plan' => $plan,
        //     'allowedCountries' => CountryRestriction::allowedOptions(),
        // ]);
    }

    public function store(Request $request, Plan $plan): RedirectResponse
    {
        if (! $plan->is_active) {
            return redirect()
                ->route('frontend.onboarding.plans')
                ->withErrors(['plan' => __('frontend.subscription.plan_unavailable')]);
        }

        if ($request->user()?->isPatient()) {
            return redirect()->route('frontend.patient.subscription.checkout', [
                'plan' => $plan,
                'intent' => $plan->isPerExchange() ? 'addon' : 'subscribe',
            ]);
        }

        $password = config('security.password');
        $allowedCountryCodes = CountryRestriction::allowedOptions()->keys()->all();

        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => array_filter([
                'required',
                'confirmed',
                'string',
                'min:'.($password['min_length'] ?? 10),
                ($password['require_uppercase'] ?? false) ? 'regex:/[A-Z]/' : null,
                ($password['require_number'] ?? false) ? 'regex:/[0-9]/' : null,
                ($password['require_symbol'] ?? false) ? 'regex:/[^A-Za-z0-9]/' : null,
            ]),
            'date_of_birth' => ['required', 'date', 'before:-18 years'],
            'country' => ['required', 'string', 'size:2', Rule::in($allowedCountryCodes)],
            'accept_terms' => ['accepted'],
            'accept_privacy_policy' => ['accepted'],
        ]);

        $data['role'] = 'patient';
        $data['intent'] = 'submit';
        $data['name'] = trim($data['first_name'].' '.$data['last_name']);
        $data['locale'] = app()->getLocale();

        try {
            $user = $this->authService->register($data);
        } catch (AuthenticationException $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }

        $request->session()->put('url.intended', route('frontend.patient.subscription.checkout', [
            'plan' => $plan,
            'intent' => $plan->isPerExchange() ? 'addon' : 'subscribe',
        ]));

        return redirect()
            ->route('frontend.auth.verify-email', ['email' => $user->email])
            ->with('success', __('auth_api.register_verify_email'));
    }
}
