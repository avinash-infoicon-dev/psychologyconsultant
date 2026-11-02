<?php

namespace App\Models;

use App\Models\Concerns\HasVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Backward-compatible model name for psychologist-specific application code.
 *
 * The old psychologists table is disabled; all profiles now use professionals.
 */
class Psychologist extends Model
{
    use HasFactory, HasVerificationStatus, SoftDeletes;

    // Legacy table:
    // protected $table = 'psychologists';
    protected $table = 'professionals';

    protected $fillable = [
        'user_id', 'uuid', 'professional_type_id', 'first_name', 'last_name',
        'slug', 'license_number', 'specialization', 'bio', 'experience_years',
        'years_experience', 'is_verified', 'verification_status', 'verification_reason',
        'is_accepting_patients', 'max_active_patients', 'languages', 'credentials', 'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Psychologist $psychologist) {
            $psychologist->ensureUuid();
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
            'is_verified' => 'boolean',
            'is_accepting_patients' => 'boolean',
            'status' => 'boolean',
            'languages' => 'array',
            'credentials' => 'array',
        ];
    }

    public function getYearsExperienceAttribute(): int
    {
        return (int) ($this->attributes['experience_years'] ?? 0);
    }

    public function setYearsExperienceAttribute(mixed $value): void
    {
        $this->attributes['experience_years'] = (int) $value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professionalType(): BelongsTo
    {
        return $this->belongsTo(ProfessionalType::class);
    }

    public function scopePsychologists($query)
    {
        return $query->whereHas(
            'professionalType',
            fn ($typeQuery) => $typeQuery->where('slug', 'psychologist')
        );
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }

    public function activePatientCount(): int
    {
        return $this->patients()->count();
    }

    public function hasCapacity(): bool
    {
        return $this->is_accepting_patients
            && $this->activePatientCount() < $this->max_active_patients;
    }
}
