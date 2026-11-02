<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsPageTranslation extends Model
{
    protected $fillable = [
        'cms_page_id',
        'locale',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'layout',
        'pdf_path',
        'pdf_original_name',
    ];

    protected function casts(): array
    {
        return [
            'layout' => 'array',
        ];
    }

    public function getPdfUrlAttribute(): ?string
    {
        $path = $this->pdf_path;
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $relative = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : ltrim($path, '/');

        return Storage::disk('public')->url($relative);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}
