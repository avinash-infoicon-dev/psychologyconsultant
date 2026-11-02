<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    use HasCmsTranslations;

    protected $fillable = ['name', 'slug', 'description', 'status', 'archived_at'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SpecializationTranslation::class);
    }

    public function professionals(): BelongsToMany
    {
        return $this->belongsToMany(Professional::class, 'professional_specialization');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /** Active and not archived — offered on new psychologist profiles. */
    public function isSelectable(): bool
    {
        return (bool) $this->status && ! $this->isArchived();
    }

    public function archive(): void
    {
        $this->update([
            'status' => false,
            'archived_at' => $this->archived_at ?? now(),
        ]);
    }

    public function scopeSelectable($query)
    {
        return $query->where('status', true)->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    public function getNameAttribute(?string $value): ?string
    {
        $translated = $this->translate()?->name;

        return ($translated !== null && $translated !== '') ? $translated : $value;
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        $translated = $this->translate()?->description;

        return ($translated !== null && $translated !== '') ? $translated : $value;
    }
}
