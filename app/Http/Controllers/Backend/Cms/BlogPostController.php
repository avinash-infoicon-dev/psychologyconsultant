<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsBlogCategory;
use App\Models\CmsBlogPost;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function __construct(protected CmsUploadService $uploads) {}

    public function index(Request $request): View
    {
        $posts = CmsBlogPost::query()
            ->with(['translations', 'category.translations'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('slug', 'like', $q)
                        ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', $q)->orWhere('author_name', 'like', $q));
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('cms_blog_category_id', $request->integer('category'));
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $categories = CmsBlogCategory::query()->with('translations')->orderBy('sort_order')->get();

        return view('backend.cms.blog.posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        return view('backend.cms.blog.posts.create', [
            'locales' => Locales::codes(),
            'categories' => CmsBlogCategory::query()->with('translations')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $post = CmsBlogPost::query()->create([
                'slug' => $data['slug'],
                'cms_blog_category_id' => $data['cms_blog_category_id'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
                'published_at' => $data['published_at'],
                'video_url' => $data['video_url'],
            ]);
            $this->syncMediaAndTranslations($post, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('success', __('backend.cms.blog.posts.created'));
    }

    public function edit(CmsBlogPost $blogPost): View
    {
        $blogPost->load(['translations', 'category']);

        return view('backend.cms.blog.posts.edit', [
            'post' => $blogPost,
            'locales' => Locales::codes(),
            'categories' => CmsBlogCategory::query()->with('translations')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, CmsBlogPost $blogPost): RedirectResponse
    {
        $data = $this->validated($request, $blogPost);

        DB::transaction(function () use ($blogPost, $request, $data) {
            $blogPost->update([
                'slug' => $data['slug'],
                'cms_blog_category_id' => $data['cms_blog_category_id'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
                'published_at' => $data['published_at'],
                'video_url' => $data['video_url'],
            ]);
            $this->syncMediaAndTranslations($blogPost, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('success', __('backend.cms.blog.posts.updated'));
    }

    public function destroy(CmsBlogPost $blogPost): RedirectResponse
    {
        $this->uploads->delete($blogPost->cover_path, 'cms/blog/');
        $this->uploads->delete($blogPost->author_photo_path, 'cms/blog/');
        $blogPost->delete();

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('success', __('backend.cms.blog.posts.deleted'));
    }

    protected function validated(Request $request, ?CmsBlogPost $post = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.excerpt"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.content"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.author_name"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.author_role"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.author_bio"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.quote"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.quote_author"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.tags"] = ['nullable', 'string', 'max:500'];
            $localeRules["translations.{$locale}.read_time"] = ['nullable', 'string', 'max:50'];
        }

        $data = $request->validate(array_merge([
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('cms_blog_posts', 'slug')->ignore($post?->id)],
            'cms_blog_category_id' => ['nullable', 'integer', 'exists:cms_blog_categories,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'author_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_cover' => ['nullable', 'boolean'],
            'remove_author_photo' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $primaryTitle = $data['translations'][Locales::codes()[0] ?? 'en']['title']
            ?? collect($data['translations'])->pluck('title')->first();

        return [
            'slug' => $data['slug'] ?: Str::slug($primaryTitle),
            'cms_blog_category_id' => $data['cms_blog_category_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'published_at' => $data['published_at'] ?? now(),
            'video_url' => $data['video_url'] ?? null,
            'translations' => $data['translations'],
        ];
    }

    protected function syncMediaAndTranslations(CmsBlogPost $post, Request $request, array $translations): void
    {
        if ($request->boolean('remove_cover')) {
            $this->uploads->delete($post->cover_path, 'cms/blog/');
            $post->cover_path = null;
        }
        if ($cover = $request->file('cover')) {
            $this->uploads->delete($post->cover_path, 'cms/blog/');
            $post->cover_path = $this->uploads->store('cms/blog/'.$post->id, $cover)['path'];
        }

        if ($request->boolean('remove_author_photo')) {
            $this->uploads->delete($post->author_photo_path, 'cms/blog/');
            $post->author_photo_path = null;
        }
        if ($photo = $request->file('author_photo')) {
            $this->uploads->delete($post->author_photo_path, 'cms/blog/');
            $post->author_photo_path = $this->uploads->store('cms/blog/'.$post->id.'/author', $photo)['path'];
        }

        $post->save();
        $post->syncTranslations($translations);
    }
}
