<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsTeamMember extends Model
{
    use HasCmsTranslations;

    protected $fillable = [
        'is_active',
        'sort_order',
        'photo_path',
        'profile_url',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
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
        return $this->hasMany(CmsTeamMemberTranslation::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getNameAttribute(): ?string
    {
        return $this->translate()?->name;
    }

    public function getRoleAttribute(): ?string
    {
        return $this->translate()?->role;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return PublicMedia::url($this->photo_path);
    }
}
