<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\Cms\CmsHomePageService;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeContentController extends Controller
{
    public function __construct(
        protected CmsHomePageService $home,
        protected CmsUploadService $uploads,
    ) {}

    public function edit(): View
    {
        $page = $this->home->ensure();

        return view('backend.cms.site.home', [
            'page' => $page,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $page = $this->home->ensure();
        $data = $this->validated($request);

        DB::transaction(function () use ($page, $request, $data) {
            $page->load('translations');

            foreach (Locales::codes() as $locale) {
                $row = $data['translations'][$locale] ?? [];
                $existing = $page->translations->firstWhere('locale', $locale);
                $layout = is_array($existing?->layout) ? $existing->layout : [];
                $layout = array_merge($layout, $row['layout'] ?? []);
                unset($row['layout']);

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

        return redirect()->route('admin.cms.home.edit')
            ->with('success', __('backend.cms.pages.home_updated'));
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
            $localeRules["translations.{$locale}.video_image"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.remove_video_image"] = ['nullable', 'boolean'];
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
        if ($request->boolean("translations.{$locale}.remove_video_image")) {
            $this->uploads->delete($layout['video_image'] ?? null, 'cms/pages/');
            $layout['video_image'] = null;
        }

        $file = $request->file("translations.{$locale}.video_image");
        if ($file) {
            $this->uploads->delete($layout['video_image'] ?? null, 'cms/pages/');
            $layout['video_image'] = $this->uploads->store('cms/pages/'.$page->id.'/layout/'.$locale, $file)['path'];
        }

        return $layout;
    }
}
