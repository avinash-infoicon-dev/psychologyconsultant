<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsBlogPostTranslation extends Model
{
    protected $fillable = [
        'cms_blog_post_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'author_name',
        'author_role',
        'author_bio',
        'quote',
        'quote_author',
        'tags',
        'read_time',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CmsBlogPost::class, 'cms_blog_post_id');
    }
}
