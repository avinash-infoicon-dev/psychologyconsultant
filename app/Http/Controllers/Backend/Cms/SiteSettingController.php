<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Services\Cms\CmsSiteSettingService;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function __construct(
        protected CmsUploadService $uploads,
        protected CmsSiteSettingService $siteSettings,
    ) {}

    public function edit(): View
    {
        $site = $this->siteSettings->current();
        $site->load('translations');

        return view('backend.cms.site.edit', [
            'site' => $site,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $site = $this->siteSettings->current();

        DB::transaction(function () use ($site, $request, $data) {
            $site->update([
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            foreach (Locales::codes() as $locale) {
                $attrs = $data['translations'][$locale] ?? [];
                $existing = $site->translations->firstWhere('locale', $locale);

                foreach ([
                    'logo' => 'logo_path',
                    'logo_dark' => 'logo_dark_path',
                    'footer_logo' => 'footer_logo_path',
                    'footer_background' => 'footer_background_path',
                ] as $fileKey => $column) {
                    $path = $existing?->{$column};
                    if ($request->boolean("translations.{$locale}.remove_{$fileKey}")) {
                        $this->uploads->delete($path, 'cms/site/');
                        $path = null;
                    }
                    if ($file = $request->file("translations.{$locale}.{$fileKey}")) {
                        $this->uploads->delete($path, 'cms/site/');
                        $path = $this->uploads->store('cms/site/'.$locale, $file)['path'];
                    }
                    $attrs[$column] = $path;
                    unset($attrs[$fileKey], $attrs['remove_'.$fileKey]);
                }

                $site->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $attrs
                );
            }
        });

        $this->siteSettings->forget();

        return redirect()->route('admin.cms.site.edit')
            ->with('success', __('backend.cms.site.updated'));
    }

    public function login(): View
    {
        $site = $this->siteSettings->current();
        $site->load('translations');

        return view('backend.cms.site.login', [
            'site' => $site,
            'locales' => Locales::codes(),
        ]);
    }

    public function updateLogin(Request $request): RedirectResponse
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            foreach ([
                'login_hero_subtitle',
                'login_hero_title',
                'login_kicker',
                'login_visual_title',
                'login_point_1',
                'login_point_2',
                'login_point_3',
                'login_point_4',
                'login_form_title',
            ] as $field) {
                $localeRules["translations.{$locale}.{$field}"] = ['nullable', 'string', 'max:255'];
            }
            $localeRules["translations.{$locale}.login_hero_text"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.login_visual_text"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.login_form_subtitle"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.login_hero"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.remove_login_hero"] = ['nullable', 'boolean'];
        }

        $data = $request->validate(array_merge([
            'translations' => ['required', 'array'],
        ], $localeRules));

        $site = $this->siteSettings->current();
        $site->load('translations');

        DB::transaction(function () use ($site, $request, $data) {
            foreach (Locales::codes() as $locale) {
                $attrs = $data['translations'][$locale] ?? [];
                $existing = $site->translations->firstWhere('locale', $locale);
                $path = $existing?->login_hero_path;

                if ($request->boolean("translations.{$locale}.remove_login_hero")) {
                    $this->uploads->delete($path, 'cms/site/');
                    $path = null;
                }
                if ($file = $request->file("translations.{$locale}.login_hero")) {
                    $this->uploads->delete($path, 'cms/site/');
                    $path = $this->uploads->store('cms/site/'.$locale.'/login', $file)['path'];
                }

                unset($attrs['login_hero'], $attrs['remove_login_hero']);
                $attrs['login_hero_path'] = $path;

                $site->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $attrs
                );
            }
        });

        $this->siteSettings->forget();

        return redirect()->route('admin.cms.login.edit')
            ->with('success', __('backend.cms.site.login_updated'));
    }

    public function register(): View
    {
        $site = $this->siteSettings->current();
        $site->load('translations');

        return view('backend.cms.site.register', [
            'site' => $site,
            'locales' => Locales::codes(),
        ]);
    }

    public function updateRegister(Request $request): RedirectResponse
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            foreach ([
                'register_hero_subtitle',
                'register_hero_title',
                'register_kicker',
                'register_heading',
                'register_form_title',
            ] as $field) {
                $localeRules["translations.{$locale}.{$field}"] = ['nullable', 'string', 'max:255'];
            }
            $localeRules["translations.{$locale}.register_hero_text"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.register_intro"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.register_form_subtitle"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.register_hero"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.remove_register_hero"] = ['nullable', 'boolean'];
            $localeRules["translations.{$locale}.psych"] = ['nullable', 'array'];
            $localeRules["translations.{$locale}.psych.*"] = ['nullable', 'string', 'max:2000'];
        }

        $data = $request->validate(array_merge([
            'translations' => ['required', 'array'],
        ], $localeRules));

        $site = $this->siteSettings->current();
        $site->load('translations');

        DB::transaction(function () use ($site, $request, $data) {
            foreach (Locales::codes() as $locale) {
                $attrs = $data['translations'][$locale] ?? [];
                $existing = $site->translations->firstWhere('locale', $locale);
                $path = $existing?->register_hero_path;

                if ($request->boolean("translations.{$locale}.remove_register_hero")) {
                    $this->uploads->delete($path, 'cms/site/');
                    $path = null;
                }
                if ($file = $request->file("translations.{$locale}.register_hero")) {
                    $this->uploads->delete($path, 'cms/site/');
                    $path = $this->uploads->store('cms/site/'.$locale.'/register', $file)['path'];
                }

                unset($attrs['register_hero'], $attrs['remove_register_hero']);
                $attrs['register_hero_path'] = $path;
                $attrs['register_psychologist_copy'] = $attrs['psych'] ?? ($existing?->register_psychologist_copy ?? []);
                unset($attrs['psych']);

                $site->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $attrs
                );
            }
        });

        $this->siteSettings->forget();

        return redirect()->route('admin.cms.register.edit')
            ->with('success', __('backend.cms.site.register_updated'));
    }

    protected function validated(Request $request): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.brand"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.tagline"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.offcanvas_info_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.availability"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.footer_links_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.help_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.help_phone_label"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.help_email_label"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.newsletter_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.newsletter_text"] = ['nullable', 'string', 'max:2000'];
            $localeRules["translations.{$locale}.copyright"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.logo"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.logo_dark"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.footer_logo"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.footer_background"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];
            $localeRules["translations.{$locale}.remove_logo"] = ['nullable', 'boolean'];
            $localeRules["translations.{$locale}.remove_logo_dark"] = ['nullable', 'boolean'];
            $localeRules["translations.{$locale}.remove_footer_logo"] = ['nullable', 'boolean'];
            $localeRules["translations.{$locale}.remove_footer_background"] = ['nullable', 'boolean'];
        }

        return $request->validate(array_merge([
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'translations' => ['required', 'array'],
        ], $localeRules));
    }
}
