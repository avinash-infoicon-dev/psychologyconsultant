<?php

namespace App\Services;

use App\Exceptions\TwoFactorException;
use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    protected function codeKey(User $user): string
    {
        return "email_verify:code:{$user->id}";
    }

    protected function attemptsKey(User $user): string
    {
        return "email_verify:attempts:{$user->id}";
    }

    public function sendCode(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $length = (int) config('security.email_verification.code_length', 6);
        $expiry = (int) config('security.email_verification.code_expiry_minutes', 30);

        $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        Cache::put($this->codeKey($user), Hash::make($code), now()->addMinutes($expiry));
        Cache::forget($this->attemptsKey($user));
        //dd($code);
        Mail::to($user->email)->send(new EmailVerificationCodeMail($code, $expiry, $user->name));
    }

    /**
     * @throws TwoFactorException
     */
    public function verifyCode(User $user, string $code): bool
    {
        if ($user->hasVerifiedEmail()) {
            return true;
        }

        $maxAttempts = (int) config('security.email_verification.max_attempts', 5);
        $attempts = (int) Cache::get($this->attemptsKey($user), 0);

        if ($attempts >= $maxAttempts) {
            throw new TwoFactorException(__('auth_api.email_verify_too_many_attempts'));
        }

        $hashed = Cache::get($this->codeKey($user));

        if (! $hashed || ! Hash::check($code, $hashed)) {
            Cache::put(
                $this->attemptsKey($user),
                $attempts + 1,
                now()->addMinutes((int) config('security.email_verification.code_expiry_minutes', 30))
            );

            return false;
        }

        $user->markEmailAsVerified();

        Cache::forget($this->codeKey($user));
        Cache::forget($this->attemptsKey($user));

        return true;
    }
}
