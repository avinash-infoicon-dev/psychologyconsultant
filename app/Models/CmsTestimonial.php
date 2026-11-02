<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsTestimonial extends Model
{
    use HasCmsTranslations;

    protected $fillable = [
        'is_active',
        'sort_order',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CmsTestimonialTranslation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getAuthorNameAttribute(): ?string
    {
        return $this->translate()?->author_name;
    }

    public function getAuthorRoleAttribute(): ?string
    {
        return $this->translate()?->author_role;
    }

    public function getQuoteAttribute(): ?string
    {
        return $this->translate()?->quote;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return PublicMedia::url($this->photo_path);
    }
}
