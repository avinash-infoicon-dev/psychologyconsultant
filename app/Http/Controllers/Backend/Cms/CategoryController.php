<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = CmsCategory::query()
            ->with('translations')
            ->withCount('pages')
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

        return view('backend.cms.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('backend.cms.categories.create', [
            'locales' => Locales::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $category = CmsCategory::query()->create([
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $category->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.cms.categories.index')
            ->with('success', __('backend.cms.categories.created'));
    }

    public function edit(CmsCategory $category): View
    {
        $category->load('translations');

        return view('backend.cms.categories.edit', [
            'category' => $category,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, CmsCategory $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        DB::transaction(function () use ($category, $data) {
            $category->update([
                'slug' => $data['slug'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $category->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.cms.categories.index')
            ->with('success', __('backend.cms.categories.updated'));
    }

    public function destroy(CmsCategory $category): RedirectResponse
    {
        if ($category->pages()->exists()) {
            return back()->withErrors(['category' => __('backend.cms.categories.cannot_delete_with_pages')]);
        }

        $category->delete();

        return redirect()
            ->route('admin.cms.categories.index')
            ->with('success', __('backend.cms.categories.deleted'));
    }

    protected function validated(Request $request, ?CmsCategory $category = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.description"] = ['nullable', 'string'];
        }

        $data = $request->validate(array_merge([
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_categories', 'slug')->ignore($category?->id)],
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
