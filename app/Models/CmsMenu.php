<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    use HasCmsTranslations;

    public const TYPE_PAGE = 'page';

    public const TYPE_CATEGORY = 'category';

    public const TYPE_URL = 'url';

    public const TYPE_ROUTE = 'route';

    protected $table = 'cms_menus';

    protected $fillable = [
        'location',
        'parent_id',
        'type',
        'reference_id',
        'url',
        'route_name',
        'target',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'reference_id' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CmsMenuTranslation::class, 'cms_menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function getTitleAttribute(): ?string
    {
        return $this->translate()?->title;
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'reference_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'reference_id');
    }
}
