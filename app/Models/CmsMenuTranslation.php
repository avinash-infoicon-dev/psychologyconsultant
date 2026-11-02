<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsMenuTranslation extends Model
{
    protected $fillable = [
        'cms_menu_id',
        'locale',
        'title',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(CmsMenu::class, 'cms_menu_id');
    }
}
