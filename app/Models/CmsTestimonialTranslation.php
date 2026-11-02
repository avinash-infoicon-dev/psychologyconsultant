<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsTestimonialTranslation extends Model
{
    protected $fillable = [
        'cms_testimonial_id',
        'locale',
        'author_name',
        'author_role',
        'quote',
    ];

    public function testimonial(): BelongsTo
    {
        return $this->belongsTo(CmsTestimonial::class, 'cms_testimonial_id');
    }
}
