<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Email-delivered one-time-passcode 2FA. Deliberately not TOTP —
 * this platform's users are patients/psychologists on a healthcare
 * app, not developers with authenticator apps, so a mailed code with
 * a short expiry is the better UX trade-off for the "challenge/verify"
 * flow the routes expose.
 *
 * Codes are hashed before being cached, never stored or logged in
 * plaintext, and rate-limited via a per-user attempt counter.
 */
class TwoFactorService
{
    protected function codeKey(User $user): string
    {
        return "2fa:code:{$user->id}";
    }

    protected function attemptsKey(User $user): string
    {
        return "2fa:attempts:{$user->id}";
    }

    public function isGloballyEnabled(): bool
    {
        return (bool) Setting::get(
            'security.two_factor.enabled',
            config('security.two_factor.enabled', true),
        );
    }

    /** @return list<string> */
    public function requiredForRoles(): array
    {
        return Setting::get(
            'security.two_factor.required_for_roles',
            config('security.two_factor.required_for_roles', []),
        );
    }

    public function isRequiredFor(User $user): bool
    {
        //if (! config('security.two_factor.enabled', true)) {
        if (! $this->isGloballyEnabled()) {
            return false;
        }

        if ($user->two_factor_exempt) {
            return false;
        }

        if ($user->two_factor_enabled) {
            return true;
        }

        //return $user->hasAnyRbacRole(config('security.two_factor.required_for_roles', []));
        return $user->hasAnyRbacRole($this->requiredForRoles());
    }

    public function userRequirementReason(User $user): string
    {
        if (! $this->isGloballyEnabled()) {
            return 'global_off';
        }

        if ($user->two_factor_exempt) {
            return 'exempt';
        }

        if ($user->two_factor_enabled) {
            return 'user_enabled';
        }

        if ($user->hasAnyRbacRole($this->requiredForRoles())) {
            return 'role';
        }

        return 'none';
    }

    /** Generate, store, and email a fresh code. Returns nothing sensitive to the caller. */
    public function sendCode(User $user): void
    {
        $length = config('security.two_factor.code_length', 6);
        $expiry = config('security.two_factor.code_expiry_minutes', 10);

        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        Cache::put($this->codeKey($user), Hash::make($code), now()->addMinutes($expiry));
        Cache::forget($this->attemptsKey($user));

        Mail::to($user->email)->send(new TwoFactorCodeMail($code, $expiry));
    }

    /**
     * @throws \App\Exceptions\TwoFactorException
     */
    public function verifyCode(User $user, string $code): bool
    {
        $maxAttempts = config('security.two_factor.max_attempts', 5);
        $attempts = (int) Cache::get($this->attemptsKey($user), 0);

        if ($attempts >= $maxAttempts) {
            throw new \App\Exceptions\TwoFactorException(
                __('auth_api.two_factor_too_many_attempts')
            );
        }

        $hashed = Cache::get($this->codeKey($user));

        if (! $hashed || ! Hash::check($code, $hashed)) {
            Cache::put($this->attemptsKey($user), $attempts + 1, now()->addMinutes(
                config('security.two_factor.code_expiry_minutes', 10)
            ));

            return false;
        }

        Cache::forget($this->codeKey($user));
        Cache::forget($this->attemptsKey($user));

        return true;
    }
}
