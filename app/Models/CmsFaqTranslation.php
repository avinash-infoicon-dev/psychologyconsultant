<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsFaqTranslation extends Model
{
    protected $fillable = [
        'cms_faq_id',
        'locale',
        'question',
        'answer',
    ];

    public function faq(): BelongsTo
    {
        return $this->belongsTo(CmsFaq::class, 'cms_faq_id');
    }
}
