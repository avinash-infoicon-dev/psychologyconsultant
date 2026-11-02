<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasCmsTranslations;

    public const TEMPLATE_DEFAULT = 'default';

    public const TEMPLATE_ABOUT = 'about';

    public const TEMPLATE_FAQ = 'faq';

    public const TEMPLATE_HOME = 'home';

    public const TEMPLATE_BLOG = 'blog';

    public const TEMPLATE_CONTACT = 'contact';

    public const TEMPLATE_SERVICES = 'services';

    protected $table = 'cms_pages';

    protected $fillable = [
        'uuid',
        'cms_category_id',
        'slug',
        'template',
        'is_active',
        'sort_order',
        'published_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (CmsPage $page) {
            $page->ensureUuid();
        });
    }

    /**
     * Ensure uuid is present even when model events are disabled
     * (e.g. DatabaseSeeder uses WithoutModelEvents).
     */
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
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CmsPageTranslation::class, 'cms_page_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'cms_category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->active()->where(function (Builder $q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        });
    }

    public function getTitleAttribute(): ?string
    {
        return $this->translate()?->title;
    }

    public function getContentAttribute(): ?string
    {
        return $this->translate()?->content;
    }

    public function getMetaTitleAttribute(): ?string
    {
        return $this->translate()?->meta_title;
    }

    public function getMetaDescriptionAttribute(): ?string
    {
        return $this->translate()?->meta_description;
    }

    public function getPdfPathAttribute(): ?string
    {
        return $this->translate()?->pdf_path;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->translate()?->pdf_url;
    }

    public function hasPdf(): bool
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->contains(fn ($tr) => filled($tr->pdf_path));
        }

        return $this->translations()->whereNotNull('pdf_path')->exists();
    }

    public function isFaqTemplate(): bool
    {
        return $this->template === self::TEMPLATE_FAQ;
    }

    public function isBlogTemplate(): bool
    {
        return $this->template === self::TEMPLATE_BLOG || $this->slug === 'blog';
    }

    public function isAboutTemplate(): bool
    {
        return $this->template === self::TEMPLATE_ABOUT;
    }

    public function isContactTemplate(): bool
    {
        return $this->template === self::TEMPLATE_CONTACT || $this->slug === 'contact';
    }

    public function isHomeTemplate(): bool
    {
        return $this->template === self::TEMPLATE_HOME || $this->slug === 'home';
    }

    public function isServicesTemplate(): bool
    {
        return $this->template === self::TEMPLATE_SERVICES || $this->slug === 'services';
    }

    public function publicPath(): string
    {
        return match (true) {
            $this->isHomeTemplate() => '/',
            $this->isFaqTemplate() => '/faq',
            $this->isBlogTemplate() => '/blog',
            $this->isContactTemplate() => '/contact',
            $this->isAboutTemplate() || $this->slug === 'about' => '/about',
            $this->isServicesTemplate() => '/services',
            default => '/'.$this->slug,
        };
    }

    public function layout(?string $key = null, mixed $default = null): mixed
    {
        $layout = $this->translate()?->layout ?? [];
        if (! is_array($layout)) {
            $layout = [];
        }

        if ($key === null) {
            return $layout;
        }

        return $layout[$key] ?? $default;
    }

    public function layoutUrl(string $key, ?string $fallback = null): ?string
    {
        return \App\Support\PublicMedia::url($this->layout($key))
            ?: \App\Support\PublicMedia::url($fallback);
    }
}
