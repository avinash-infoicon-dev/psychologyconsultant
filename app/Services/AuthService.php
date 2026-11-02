<?php

namespace App\Services;

use App\Exceptions\TwoFactorException;
use App\Models\CountryRestriction;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\ProfessionalType;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected TwoFactorService $twoFactor,
        protected JwtService $jwt,
        protected EmailVerificationService $emailVerification,
    ) {}

    /**
     * Create a user plus their role-specific profile row (Patient or
     * Psychologist). Email stays unverified until the OTP is confirmed.
     * Psychologists may log in after email verification unless suspended;
     * they must still submit a profile for admin verification to appear in search.
     */
    public function register(array $data): User
    {
        if (($data['role'] ?? null) === 'patient') {
            $this->assertCountryAllowed($data['country'] ?? null);
        }

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'locale' => $data['locale'] ?? config('app.locale'),
                'email_verified_at' => null,
                'is_active' => true,
            ]);

            $user->assignRbacRole($data['role']);
            // $user->assignRole($data['role']);

            if ($data['role'] === 'patient') {
                Patient::create([
                    'user_id' => $user->id,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'country' => $data['country'] ?? null,
                ]);
            } else {
                // Legacy profile creation used Psychologist::create([...]).
                $professionalType = ProfessionalType::query()->firstOrCreate(
                    ['slug' => $data['role']],
                    [
                        'name' => Str::headline($data['role']),
                        'status' => true,
                    ]
                );
                $nameParts = explode(' ', trim($data['name']), 2);
                $registration = app(PsychologistRegistrationService::class);
                $firstName = $data['first_name'] ?? $nameParts[0];
                $lastName = $data['last_name'] ?? ($nameParts[1] ?? null);

                $professional = Professional::create(array_merge([
                    'user_id' => $user->id,
                    'professional_type_id' => $professionalType->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'slug' => Str::slug($data['name']).'-'.$user->id,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'license_number' => $data['license_number'] ?? null,
                    'specialization' => $data['specialization'] ?? null,
                    'experience_years' => $data['years_experience'] ?? 0,
                    'is_verified' => false,
                    'verification_status' => Professional::VERIFICATION_DRAFT,
                    'is_accepting_patients' => false,
                    'status' => false,
                ], $registration->overviewAttributes($data, $user)));

                $registration->attachRelated($professional, $data);


                // if (($data['intent'] ?? 'draft') === 'submit') {
                //     app(PsychologistVerificationService::class)->submit($professional);
                // }

                /*
                 * Legacy psychologists profile creation disabled.
                 * Psychologist::create([...]);
                 */
            }

            $this->recordSignupConsents($user);

            return $user;
        });

        //$this->emailVerification->sendCode($user);
        
        if (($data['role'] ?? null) === 'psychologist' && ($data['intent'] ?? 'draft') === 'submit') {
            try {
                app(PsychologistVerificationService::class)->submit($user->professional);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        try {
            $this->emailVerification->sendCode($user);
        } catch (\Throwable $e) {
            report($e);
        }

        return $user->fresh(['roles', 'professional', 'psychologist', 'patient']);
    }

    /**
     * @throws TwoFactorException
     */
    public function verifyEmail(string $email, string $code): array
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return ['status' => 'already_verified', 'user' => $user];
        }

        if (! $this->emailVerification->verifyCode($user, $code)) {
            return ['status' => 'invalid_code'];
        }

        $user->refresh();

        return ['status' => 'verified', 'user' => $user];
    }

    public function resendEmailVerification(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $this->emailVerification->sendCode($user);
    }

    /**
     * Attempt credential login. Returns an array shaped:
     * ['status' => 'ok', 'token' => string, 'user' => User]
     * ['status' => 'requires_2fa', 'user' => User]
     *
     * @throws AuthenticationException
     */
    public function attemptLogin(string $email, string $password, ?string $deviceName = null): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            $this->recordLoginAttempt($user, false, 'invalid_credentials');
            throw new AuthenticationException(__('auth_api.login_invalid_credentials'));
        }

        if (! $user->is_active) {
            $this->recordLoginAttempt($user, false, 'account_inactive');
            throw new AuthenticationException(__('auth_api.account_inactive'));
        }
        // Previous experiment: allow inactive psychologists to log in to view old chats.
        // if (! $user->is_active && ! $user->isPsychologist()) {
        //     $this->recordLoginAttempt($user, false, 'account_inactive');
        //     throw new AuthenticationException(__('auth_api.account_inactive'));
        // }

        if (! $user->hasVerifiedEmail()) {
            $this->recordLoginAttempt($user, false, 'email_unverified');
            $this->emailVerification->sendCode($user);
            throw new AuthenticationException(__('auth_api.email_not_verified'));
        }

        $user->loadMissing(['professional', 'psychologist']);

        // Old: psychologists could not log in until is_verified.
        // if ($user->isPsychologist() && ! ($user->professional?->is_verified ?? $user->psychologist?->is_verified)) {
        //     $this->recordLoginAttempt($user, false, 'psychologist_unverified');
        //     throw new AuthenticationException(__('auth_api.psychologist_not_verified'));
        // }
        if ($user->isPsychologist() && $user->isPsychologistSuspended()) {
            $this->recordLoginAttempt($user, false, 'psychologist_suspended');
            throw new AuthenticationException(__('auth_api.psychologist_suspended'));
        }

        if ($this->twoFactor->isRequiredFor($user)) {
            $this->twoFactor->sendCode($user);
            $this->recordLoginAttempt($user, true, null);

            return ['status' => 'requires_2fa', 'user' => $user];
        }

        $token = $this->issueToken($user, $deviceName);
        $this->recordLoginAttempt($user, true, null);

        return ['status' => 'ok', 'token' => $token, 'user' => $user];
    }

    /**
     * @throws TwoFactorException
     */
    public function completeTwoFactorLogin(string $email, string $code, ?string $deviceName = null): array
    {
        $user = User::where('email', $email)->firstOrFail();

        if (! $user->hasVerifiedEmail()) {
            throw new AuthenticationException(__('auth_api.email_not_verified'));
        }

        $user->loadMissing(['professional', 'psychologist']);

        // Old: psychologists could not complete 2FA login until is_verified.
        // if ($user->isPsychologist() && ! ($user->professional?->is_verified ?? $user->psychologist?->is_verified)) {
        //     throw new AuthenticationException(__('auth_api.psychologist_not_verified'));
        // }
        if ($user->isPsychologist() && $user->isPsychologistSuspended()) {
            throw new AuthenticationException(__('auth_api.psychologist_suspended'));
        }

        if (! $this->twoFactor->verifyCode($user, $code)) {
            return ['status' => 'invalid_code'];
        }

        return ['status' => 'ok', 'token' => $this->issueToken($user, $deviceName), 'user' => $user];
    }

    public function issueToken(User $user, ?string $deviceName = null): string
    {
        return $this->jwt->issueToken($user);
    }

    public function tokenExpiresIn(): int
    {
        return $this->jwt->ttlSeconds();
    }

    public function logout(?string $token = null): void
    {
        if ($token) {
            $this->jwt->invalidate($token);
        }
    }

    public function logoutAllDevices(User $user): void
    {
        $this->jwt->revokeAllForUser($user->id);
    }

    protected function recordLoginAttempt(?User $user, bool $successful, ?string $failureReason): void
    {
        if (! $user) {
            return;
        }

        $user->loginHistory()->create([
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }

    protected function recordSignupConsents(User $user): void
    {
        foreach (config('healthcare.consent.required_types', []) as $type) {
            $user->consents()->create([
                'type' => $type,
                'version' => '1.0',
                'ip_address' => request()?->ip(),
                'accepted_at' => now(),
            ]);
        }
    }

    protected function assertCountryAllowed(?string $countryCode): void
    {
        if (! $countryCode) {
            return;
        }

        // Psychologists enter a free-text country; only ISO-2 patient codes
        // are looked up in country_restrictions.
        if (strlen($countryCode) !== 2) {
            return;
        }

        // Legacy: only rejected explicit blocked status.
        // if (CountryRestriction::statusFor($countryCode) === 'blocked') {
        //     throw new AuthenticationException(__('auth_api.region_blocked'));
        if (CountryRestriction::statusFor($countryCode) !== 'allowed') {
            throw ValidationException::withMessages([
                'country' => __('auth_api.region_blocked'),
            ]);
        }
    }
}
