<?php

namespace App\Models;

use App\Models\Concerns\HasVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Professional extends Model
{
    use HasFactory, HasVerificationStatus, SoftDeletes;

    protected $fillable = [
        'user_id',
        'uuid',
        'professional_type_id',
        'first_name',
        'last_name',
        'slug',
        'title',
        'profile_image',
        'cover_image',
        'gender',
        'date_of_birth',
        'email',
        'phone',
        'website',
        'license_number',
        'specialization',
        'experience_years',
        'consultation_fee',
        'bio',
        'languages',
        'credentials',
        'organization',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'online_consultation',
        'offline_consultation',
        'rating',
        'total_reviews',
        'is_verified',
        'verification_status',
        'verification_reason',
        'is_accepting_patients',
        'max_active_patients',
        'is_featured',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Professional $professional) {
            $professional->ensureUuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function save(array $options = []): bool
    {
        $this->ensureUuid();

        return parent::save($options);
    }

    public function ensureUuid(): void
    {
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'languages' => 'array',
            'credentials' => 'array',
            'online_consultation' => 'boolean',
            'offline_consultation' => 'boolean',
            'rating' => 'decimal:2',
            'is_verified' => 'boolean',
            'is_accepting_patients' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionalType(): BelongsTo
    {
        return $this->belongsTo(ProfessionalType::class);
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'professional_specialization');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(ProfessionalGallery::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProfessionalDocument::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(ProfessionalSocialLink::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function settings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProfessionalSetting::class, 'professional_id');
    }

    /** Active absences whose date range includes now. */
    public function currentAbsences(): HasMany
    {
        return $this->absences()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->orderBy('ends_at');
    }

    public function currentAbsence(): ?Absence
    {
        if ($this->relationLoaded('currentAbsences')) {
            return $this->currentAbsences->first();
        }

        return $this->currentAbsences()->first();
    }

    public function scopePsychologists($query)
    {
        return $query->whereHas(
            'professionalType',
            fn ($typeQuery) => $typeQuery->where('slug', 'psychologist')
        );
    }

    /**
     * Legacy API/form field name used by psychologist profile endpoints.
     */
    public function getYearsExperienceAttribute(): int
    {
        return (int) ($this->attributes['experience_years'] ?? 0);
    }

    public function setYearsExperienceAttribute(mixed $value): void
    {
        $this->attributes['experience_years'] = (int) $value;
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name
            ?? trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    public function getProfileImageUrlAttribute(): string
    {
        return $this->assetUrl($this->profile_image, 'frontendassets/images/default-professional.svg');
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->assetUrl($this->cover_image, 'frontendassets/images/default-professional-cover.svg');
    }

    private function assetUrl(?string $path, string $default): string
    {
        if (! $path) {
            return asset($default);
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }

        return asset(ltrim($path, '/'));
    }
}
