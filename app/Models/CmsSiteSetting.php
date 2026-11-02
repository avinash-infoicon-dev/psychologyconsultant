<?php

namespace App\Models;

use App\Models\Concerns\HasCmsTranslations;
use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsSiteSetting extends Model
{
    use HasCmsTranslations;

    public const DEFAULT_LOGO = 'frontendassets/assets/img/logo.webp';

    public const DEFAULT_LOGO_DARK = 'frontendassets/assets/img/logo-dark.webp';

    public const DEFAULT_FOOTER_BG = 'frontendassets/assets/img/footer/footer-1-bg.webp';

    protected $fillable = [
        'logo_path',
        'logo_dark_path',
        'footer_logo_path',
        'footer_background_path',
        'phone',
        'email',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CmsSiteSettingTranslation::class);
    }

    public function getBrandAttribute(): ?string
    {
        return $this->translate()?->brand;
    }

    public function getTaglineAttribute(): ?string
    {
        return $this->translate()?->tagline;
    }

    public function getOffcanvasInfoTitleAttribute(): ?string
    {
        return $this->translate()?->offcanvas_info_title;
    }

    public function getAvailabilityAttribute(): ?string
    {
        return $this->translate()?->availability;
    }

    public function getFooterLinksTitleAttribute(): ?string
    {
        return $this->translate()?->footer_links_title;
    }

    public function getHelpTitleAttribute(): ?string
    {
        return $this->translate()?->help_title;
    }

    public function getHelpPhoneLabelAttribute(): ?string
    {
        return $this->translate()?->help_phone_label;
    }

    public function getHelpEmailLabelAttribute(): ?string
    {
        return $this->translate()?->help_email_label;
    }

    public function getNewsletterTitleAttribute(): ?string
    {
        return $this->translate()?->newsletter_title;
    }

    public function getNewsletterTextAttribute(): ?string
    {
        return $this->translate()?->newsletter_text;
    }

    public function translatedPath(string $column): ?string
    {
        return $this->translate()?->{$column} ?: $this->{$column};
    }

    public function logoUrl(string $variant = 'light'): string
    {
        if ($variant === 'dark') {
            return PublicMedia::url($this->translatedPath('logo_dark_path'))
                ?: PublicMedia::url($this->translatedPath('logo_path'))
                ?: (string) PublicMedia::url(self::DEFAULT_LOGO_DARK);
        }

        return PublicMedia::url($this->translatedPath('logo_path'))
            ?: (string) PublicMedia::url(self::DEFAULT_LOGO);
    }

    public function footerLogoUrl(): string
    {
        return PublicMedia::url($this->translatedPath('footer_logo_path')) ?: $this->logoUrl();
    }

    public function footerBackgroundUrl(): string
    {
        return PublicMedia::url($this->translatedPath('footer_background_path'))
            ?: (string) PublicMedia::url(self::DEFAULT_FOOTER_BG);
    }

    public function brandName(): string
    {
        return $this->brand ?: (string) __('frontend.brand');
    }

    public function taglineText(): string
    {
        return $this->tagline ?: (string) __('frontend.tagline');
    }

    public function copyrightText(): string
    {
        $raw = $this->translate()?->copyright
            ?: '© :year '.config('app.name').'. :brand.';

        return strtr($raw, [
            ':year' => (string) date('Y'),
            ':brand' => $this->brandName(),
        ]);
    }

    public function loginCopy(string $column, ?string $fallback = null): string
    {
        $value = $this->translate()?->{$column};

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        return (string) ($fallback ?? '');
    }

    public function loginHeroUrl(): string
    {
        return PublicMedia::url($this->translate()?->login_hero_path)
            ?: PublicMedia::url(PublicMedia::aboutDefault('hero-bg.webp'))
            ?: (string) asset('frontendassets/assets/img/hero/hero-bg.webp');
    }

    public function registerCopy(string $column, ?string $fallback = null): string
    {
        return $this->loginCopy($column, $fallback);
    }

    public function registerHeroUrl(): string
    {
        return PublicMedia::url($this->translate()?->register_hero_path)
            ?: $this->loginHeroUrl();
    }

    public function psychWizardCopy(string $key, ?string $fallback = null): string
    {
        $copy = $this->translate()?->register_psychologist_copy;
        $value = is_array($copy) ? ($copy[$key] ?? null) : null;

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        return (string) ($fallback ?? '');
    }

    public function phoneHref(): string
    {
        $phone = trim((string) $this->phone);

        return $phone !== '' ? 'tel:'.preg_replace('/\s+/', '', $phone) : '#';
    }

    public function emailHref(): string
    {
        $email = trim((string) $this->email);

        return $email !== '' ? 'mailto:'.$email : '#';
    }
}
