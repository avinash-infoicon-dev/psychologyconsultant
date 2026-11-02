<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Traits\Auditable;
use App\Traits\HasRbac;
use App\Traits\HasRolesAndPermissions;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'locale',
    'is_active',
    'two_factor_enabled',
    'two_factor_exempt',
    'two_factor_secret',
    'two_factor_confirmed_at',
    'avatar_path',
    'password_changed_at',
    'email_verified_at',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasFactory, HasRbac, HasRolesAndPermissions, MustVerifyEmailTrait, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_exempt' => 'boolean',
        ];
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function psychologist(): HasOne
    {
        return $this->hasOne(Psychologist::class);
    }

    public function professional(): HasOne
    {
        return $this->hasOne(Professional::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function receivedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function isPatient(): bool
    {
        return $this->hasRbacRole('patient');
    }

    public function isPsychologist(): bool
    {
        return $this->hasRbacRole('psychologist');
    }

    public function isAdmin(): bool
    {
        return $this->hasRbacRole('admin');
    }

    /** Psychologist may log in unless the account is suspended. */
    public function canLoginToApi(): bool
    {
        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($this->isPsychologist()) {
            // Old: blocked until is_verified.
            // return (bool) ($this->professional?->is_verified ?? $this->psychologist?->is_verified);
            return ! $this->isPsychologistSuspended();
        }

        return true;
    }

    public function isPsychologistSuspended(): bool
    {
        if (! $this->isPsychologist()) {
            return false;
        }

        $profile = $this->professional ?? $this->psychologist;

        return (bool) $profile?->isVerificationSuspended();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
