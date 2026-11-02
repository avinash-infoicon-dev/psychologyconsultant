<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsBlogPost extends Model
{
    use HasCmsTranslations;

    protected $fillable = [
        'slug',
        'cms_blog_category_id',
        'is_active',
        'sort_order',
        'published_at',
        'cover_path',
        'author_photo_path',
        'video_url',
    ];

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
        return $this->hasMany(CmsBlogPostTranslation::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsBlogCategory::class, 'cms_blog_category_id');
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

    public function getExcerptAttribute(): ?string
    {
        return $this->translate()?->excerpt;
    }

    public function getContentAttribute(): ?string
    {
        return $this->translate()?->content;
    }

    public function getAuthorNameAttribute(): ?string
    {
        return $this->translate()?->author_name;
    }

    public function getAuthorRoleAttribute(): ?string
    {
        return $this->translate()?->author_role;
    }

    public function getAuthorBioAttribute(): ?string
    {
        return $this->translate()?->author_bio;
    }

    public function getQuoteAttribute(): ?string
    {
        return $this->translate()?->quote;
    }

    public function getQuoteAuthorAttribute(): ?string
    {
        return $this->translate()?->quote_author;
    }

    public function getReadTimeAttribute(): ?string
    {
        return $this->translate()?->read_time;
    }

    public function getTagsAttribute(): ?string
    {
        return $this->translate()?->tags;
    }

    public function tagList(): array
    {
        $tags = $this->tags;

        if (! $tags) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $tags))));
    }

    public function getCoverUrlAttribute(): ?string
    {
        return PublicMedia::url($this->cover_path);
    }

    public function getAuthorPhotoUrlAttribute(): ?string
    {
        return PublicMedia::url($this->author_photo_path);
    }
}
