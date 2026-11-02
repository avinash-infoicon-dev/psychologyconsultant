<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsBlogCategoryTranslation extends Model
{
    protected $fillable = [
        'cms_blog_category_id',
        'locale',
        'name',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsBlogCategory::class, 'cms_blog_category_id');
    }
}
