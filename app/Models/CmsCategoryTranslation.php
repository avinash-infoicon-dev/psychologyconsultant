<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsCategoryTranslation extends Model
{
    protected $fillable = [
        'cms_category_id',
        'locale',
        'name',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'cms_category_id');
    }
}
