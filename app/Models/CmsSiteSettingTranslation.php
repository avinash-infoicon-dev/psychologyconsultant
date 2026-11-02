<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsSiteSettingTranslation extends Model
{
    protected $fillable = [
        'cms_site_setting_id',
        'locale',
        'brand',
        'tagline',
        'offcanvas_info_title',
        'availability',
        'footer_links_title',
        'help_title',
        'help_phone_label',
        'help_email_label',
        'newsletter_title',
        'newsletter_text',
        'copyright',
        'logo_path',
        'logo_dark_path',
        'footer_logo_path',
        'footer_background_path',
        'login_hero_path',
        'login_hero_subtitle',
        'login_hero_title',
        'login_hero_text',
        'login_kicker',
        'login_visual_title',
        'login_visual_text',
        'login_point_1',
        'login_point_2',
        'login_point_3',
        'login_point_4',
        'login_form_title',
        'login_form_subtitle',
        'register_hero_path',
        'register_hero_subtitle',
        'register_hero_title',
        'register_hero_text',
        'register_kicker',
        'register_heading',
        'register_intro',
        'register_form_title',
        'register_form_subtitle',
        'register_psychologist_copy',
    ];

    protected function casts(): array
    {
        return [
            'register_psychologist_copy' => 'array',
        ];
    }

    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(CmsSiteSetting::class, 'cms_site_setting_id');
    }
}
