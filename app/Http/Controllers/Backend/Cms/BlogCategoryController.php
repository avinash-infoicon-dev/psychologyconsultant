<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsBlogCategory;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = CmsBlogCategory::query()
            ->with('translations')
            ->withCount('posts')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('slug', 'like', $q)
                        ->orWhereHas('translations', fn ($t) => $t->where('name', 'like', $q));
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('backend.cms.blog.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('backend.cms.blog.categories.create', [
            'locales' => Locales::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $category = CmsBlogCategory::query()->create([
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $category->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', __('backend.cms.blog.categories.created'));
    }

    public function edit(CmsBlogCategory $blogCategory): View
    {
        $blogCategory->load('translations');

        return view('backend.cms.blog.categories.edit', [
            'category' => $blogCategory,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, CmsBlogCategory $blogCategory): RedirectResponse
    {
        $data = $this->validated($request, $blogCategory);

        DB::transaction(function () use ($blogCategory, $data) {
            $blogCategory->update([
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $blogCategory->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', __('backend.cms.blog.categories.updated'));
    }

    public function destroy(CmsBlogCategory $blogCategory): RedirectResponse
    {
        if ($blogCategory->posts()->exists()) {
            return back()->withErrors(['category' => __('backend.cms.blog.categories.cannot_delete_with_posts')]);
        }

        $blogCategory->delete();

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', __('backend.cms.blog.categories.deleted'));
    }

    protected function validated(Request $request, ?CmsBlogCategory $category = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.description"] = ['nullable', 'string'];
        }

        $data = $request->validate(array_merge([
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_blog_categories', 'slug')->ignore($category?->id)],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $primaryName = $data['translations'][Locales::codes()[0] ?? 'en']['name']
            ?? collect($data['translations'])->pluck('name')->first();

        return [
            'slug' => $data['slug'] ?: Str::slug($primaryName),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'translations' => $data['translations'],
        ];
    }
}
