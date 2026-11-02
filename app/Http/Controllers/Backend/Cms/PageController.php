<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use App\Models\CmsPage;
use App\Services\Cms\CmsPagePdfService;
use App\Services\Cms\CmsPageSectionCatalog;
use App\Services\Cms\CmsServicesPageService;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected CmsPagePdfService $pdfs,
        protected CmsUploadService $uploads,
        protected CmsPageSectionCatalog $sections,
    ) {}

    public function index(Request $request): View
    {
        $pages = CmsPage::query()
            ->with(['translations', 'category.translations'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('slug', 'like', $q)
                        ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', $q));
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('cms_category_id', $request->integer('category'));
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = CmsCategory::query()->with('translations')->orderBy('sort_order')->get();

        $sectionCounts = [];
        foreach ($pages as $page) {
            $sectionCounts[$page->id] = $this->sections->countFor($page);
        }

        return view('backend.cms.pages.index', compact('pages', 'categories', 'sectionCounts'));
    }

    public function sections(CmsPage $page): View
    {
        $page->load('translations');

        return view('backend.cms.pages.sections', [
            'page' => $page,
            'sections' => $this->sections->for($page),
        ]);
    }

    public function create(): View
    {
        return view('backend.cms.pages.create', [
            'locales' => Locales::codes(),
            'categories' => CmsCategory::query()->with('translations')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $page = CmsPage::query()->create([
                'cms_category_id' => $data['cms_category_id'],
                'slug' => $data['slug'],
                'template' => $data['template'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
                'published_at' => $data['published_at'],
            ]);
            $this->syncTranslationsWithPdfs($page, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.cms.pages.index')
            ->with('success', __('backend.cms.pages.created'));
    }

    public function edit(CmsPage $page): View
    {
        $page->load('translations');

        return view('backend.cms.pages.edit', [
            'page' => $page,
            'locales' => Locales::codes(),
            'categories' => CmsCategory::query()->with('translations')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, CmsPage $page): RedirectResponse
    {
        $data = $this->validated($request, $page);

        DB::transaction(function () use ($page, $request, $data) {
            $page->update([
                'cms_category_id' => $data['cms_category_id'],
                'slug' => $data['slug'],
                'template' => $data['template'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
                'published_at' => $data['published_at'],
            ]);
            $this->syncTranslationsWithPdfs($page, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.cms.pages.index')
            ->with('success', __('backend.cms.pages.updated'));
    }

    public function destroy(CmsPage $page): RedirectResponse
    {
        $this->pdfs->deleteForPage($page);
        $page->delete();

        return redirect()
            ->route('admin.cms.pages.index')
            ->with('success', __('backend.cms.pages.deleted'));
    }

    protected function validated(Request $request, ?CmsPage $page = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.content"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.meta_title"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.meta_description"] = ['nullable', 'string', 'max:500'];
            $localeRules["translations.{$locale}.pdf"] = ['nullable', 'file', 'mimes:pdf', 'max:10240'];
            $localeRules["translations.{$locale}.remove_pdf"] = ['nullable', 'boolean'];
            $localeRules["translations.{$locale}.layout"] = ['nullable', 'array'];
            $localeRules["translations.{$locale}.layout.*"] = ['nullable', 'string', 'max:8000'];
            foreach ($this->layoutImageKeys() as $imageKey) {
                $localeRules["translations.{$locale}.{$imageKey}"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
                $localeRules["translations.{$locale}.remove_{$imageKey}"] = ['nullable', 'boolean'];
            }
        }

        $data = $request->validate(array_merge([
            'cms_category_id' => ['nullable', 'integer', 'exists:cms_categories,id'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_pages', 'slug')->ignore($page?->id)],
            'template' => ['nullable', 'string', Rule::in([CmsPage::TEMPLATE_DEFAULT, CmsPage::TEMPLATE_HOME, CmsPage::TEMPLATE_ABOUT, CmsPage::TEMPLATE_FAQ, CmsPage::TEMPLATE_BLOG, CmsPage::TEMPLATE_CONTACT, CmsPage::TEMPLATE_SERVICES])],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $primaryTitle = $data['translations'][Locales::codes()[0] ?? 'en']['title']
            ?? collect($data['translations'])->pluck('title')->first();

        $translations = $data['translations'];
        foreach ($translations as $locale => $row) {
            foreach ($this->layoutImageKeys() as $imageKey) {
                unset(
                    $translations[$locale][$imageKey],
                    $translations[$locale]['remove_'.$imageKey],
                );
            }
            unset(
                $translations[$locale]['pdf'],
                $translations[$locale]['remove_pdf'],
            );
        }

        return [
            'cms_category_id' => $data['cms_category_id'] ?? null,
            'slug' => $data['slug'] ?: Str::slug($primaryTitle),
            'template' => $data['template'] ?? CmsPage::TEMPLATE_DEFAULT,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'published_at' => $data['published_at'] ?? null,
            'translations' => $translations,
        ];
    }

    protected function syncTranslationsWithPdfs(CmsPage $page, Request $request, array $translations): void
    {
        $page->load('translations');

        foreach (Locales::codes() as $locale) {
            $row = $translations[$locale] ?? [];
            foreach ($this->layoutImageKeys() as $imageKey) {
                unset($row[$imageKey], $row['remove_'.$imageKey]);
            }
            unset(
                $row['pdf'],
                $row['remove_pdf'],
            );

            $existing = $page->translations->firstWhere('locale', $locale);
            $layout = is_array($existing?->layout) ? $existing->layout : [];
            $layout = array_merge($layout, $row['layout'] ?? []);
            unset($row['layout']);

            $path = $existing?->pdf_path;
            $originalName = $existing?->pdf_original_name;

            if ($request->boolean("translations.{$locale}.remove_pdf")) {
                $this->pdfs->delete($path);
                $path = null;
                $originalName = null;
            }

            $file = $request->file("translations.{$locale}.pdf");
            if ($file) {
                $this->pdfs->delete($path);
                $stored = $this->pdfs->store($page, $locale, $file);
                $path = $stored['pdf_path'];
                $originalName = $stored['pdf_original_name'];
            }

            $row['pdf_path'] = $path;
            $row['pdf_original_name'] = $originalName;
            $row['layout'] = $this->syncLayoutImages($page, $locale, $request, $layout);

            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                $row
            );
        }
    }

    protected function syncLayoutImages(CmsPage $page, string $locale, Request $request, array $layout): array
    {
        $map = [];
        foreach ($this->layoutImageKeys() as $imageKey) {
            $map[$imageKey] = $imageKey;
        }

        foreach ($map as $fileKey => $layoutKey) {
            if ($request->boolean("translations.{$locale}.remove_{$fileKey}")) {
                $this->uploads->delete($layout[$layoutKey] ?? null, 'cms/pages/');
                $layout[$layoutKey] = null;
            }

            $file = $request->file("translations.{$locale}.{$fileKey}");
            if ($file) {
                $this->uploads->delete($layout[$layoutKey] ?? null, 'cms/pages/');
                $layout[$layoutKey] = $this->uploads->store('cms/pages/'.$page->id.'/layout/'.$locale, $file)['path'];
            }
        }

        return $layout;
    }

    /**
     * @return list<string>
     */
    protected function layoutImageKeys(): array
    {
        $keys = [
            'hero_background',
            'shape_image',
            'banner_image',
            'about_image',
            'cta_image',
            'contact_background',
            'funfact_background',
            'funfact_circle',
            'social_background',
            'video_image',
        ];

        for ($n = 1; $n <= CmsServicesPageService::MAX_CARDS; $n++) {
            $keys[] = 'item_'.$n.'_image';
        }

        return $keys;
    }
}
