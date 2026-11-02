<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsBlogCategory extends Model
{
    use HasCmsTranslations;

    protected $fillable = [
        'slug',
        'is_active',
        'sort_order',
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
        return $this->hasMany(CmsBlogCategoryTranslation::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CmsBlogPost::class, 'cms_blog_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getNameAttribute(): ?string
    {
        return $this->translate()?->name;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translate()?->description;
    }
}
