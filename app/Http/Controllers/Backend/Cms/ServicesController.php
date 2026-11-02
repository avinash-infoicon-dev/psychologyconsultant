<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\Cms\CmsServicesPageService;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServicesController extends Controller
{
    public function __construct(
        protected CmsServicesPageService $services,
        protected CmsUploadService $uploads,
    ) {}

    public function edit(): View
    {
        $page = $this->services->ensure();

        return view('backend.cms.site.services', [
            'page' => $page,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $page = $this->services->ensure();
        $data = $this->validated($request);

        DB::transaction(function () use ($page, $request, $data) {
            $page->load('translations');

            foreach (Locales::codes() as $locale) {
                $row = $data['translations'][$locale] ?? [];
                $existing = $page->translations->firstWhere('locale', $locale);
                $layout = is_array($existing?->layout) ? $existing->layout : [];
                $layout = array_merge($layout, $row['layout'] ?? []);
                unset($row['layout']);

                $layout = $this->pruneRemovedCards($page, $locale, $request, $layout);
                $layout = $this->syncLayoutImages($page, $locale, $request, $layout);

                $page->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $row['title'] ?? $existing?->title,
                        'content' => $row['content'] ?? $existing?->content,
                        'meta_title' => $row['meta_title'] ?? $existing?->meta_title,
                        'meta_description' => $row['meta_description'] ?? $existing?->meta_description,
                        'layout' => $layout,
                    ]
                );
            }
        });

        return redirect()->route('admin.cms.services.edit')
            ->with('success', __('backend.cms.pages.services_updated'));
    }

    /**
     * @return array{translations: array<string, array<string, mixed>>}
     */
    protected function validated(Request $request): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.content"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.meta_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.meta_description"] = ['nullable', 'string', 'max:500'];
            $localeRules["translations.{$locale}.layout"] = ['nullable', 'array'];
            $localeRules["translations.{$locale}.layout.*"] = ['nullable', 'string', 'max:8000'];
            $localeRules["translations.{$locale}.hero_background"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.remove_hero_background"] = ['nullable', 'boolean'];
            for ($n = 1; $n <= CmsServicesPageService::MAX_CARDS; $n++) {
                $localeRules["translations.{$locale}.item_{$n}_image"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
                $localeRules["translations.{$locale}.remove_item_{$n}_image"] = ['nullable', 'boolean'];
                $localeRules["translations.{$locale}.remove_item_{$n}"] = ['nullable', 'boolean'];
            }
        }

        return $request->validate(array_merge([
            'translations' => ['required', 'array'],
        ], $localeRules));
    }

    /**
     * @param  array<string, mixed>  $layout
     * @return array<string, mixed>
     */
    protected function syncLayoutImages(CmsPage $page, string $locale, Request $request, array $layout): array
    {
        $keys = ['hero_background'];
        for ($n = 1; $n <= CmsServicesPageService::MAX_CARDS; $n++) {
            $keys[] = 'item_'.$n.'_image';
        }

        foreach ($keys as $key) {
            if ($request->boolean("translations.{$locale}.remove_{$key}")) {
                $this->uploads->delete($layout[$key] ?? null, 'cms/pages/');
                $layout[$key] = null;
            }

            $file = $request->file("translations.{$locale}.{$key}");
            if ($file) {
                $this->uploads->delete($layout[$key] ?? null, 'cms/pages/');
                $layout[$key] = $this->uploads->store('cms/pages/'.$page->id.'/layout/'.$locale, $file)['path'];
            }
        }

        return $layout;
    }

    /**
     * @param  array<string, mixed>  $layout
     * @return array<string, mixed>
     */
    protected function pruneRemovedCards(CmsPage $page, string $locale, Request $request, array $layout): array
    {
        for ($n = 1; $n <= CmsServicesPageService::MAX_CARDS; $n++) {
            if (! $request->boolean("translations.{$locale}.remove_item_{$n}")) {
                continue;
            }

            $this->uploads->delete($layout['item_'.$n.'_image'] ?? null, 'cms/pages/');
            unset(
                $layout['item_'.$n.'_title'],
                $layout['item_'.$n.'_desc'],
                $layout['item_'.$n.'_icon'],
                $layout['item_'.$n.'_bg'],
                $layout['item_'.$n.'_image'],
            );
        }

        return $layout;
    }
}
