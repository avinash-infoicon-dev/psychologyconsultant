<?php

namespace App\Http\Controllers\Frontend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsBlogCategory;
use App\Models\CmsBlogPost;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = $this->publishedQuery($request)
            ->paginate(3)
            ->withQueryString();

        return view('frontend.blog.index', $this->sidebarData() + [
            'posts' => $posts,
            'activeCategory' => null,
        ]);
    }

    public function category(Request $request, string $slug): View
    {
        $activeCategory = CmsBlogCategory::query()
            ->active()
            ->where('slug', $slug)
            ->with('translations')
            ->firstOrFail();

        $posts = $this->publishedQuery($request)
            ->where('cms_blog_category_id', $activeCategory->id)
            ->paginate(3)
            ->withQueryString();

        return view('frontend.blog.index', $this->sidebarData() + [
            'posts' => $posts,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(string $slug): View
    {
        $post = CmsBlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with(['translations', 'category.translations'])
            ->firstOrFail();

        $next = CmsBlogPost::query()
            ->published()
            ->with('translations')
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                $query->where('published_at', '<', $post->published_at)
                    ->orWhere(function ($inner) use ($post) {
                        $inner->where('published_at', $post->published_at)
                            ->where('id', '<', $post->id);
                    });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        return view('frontend.blog.show', $this->sidebarData() + [
            'post' => $post,
            'next' => $next,
        ]);
    }

    protected function publishedQuery(Request $request)
    {
        return CmsBlogPost::query()
            ->published()
            ->with(['translations', 'category.translations'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('translations', function ($t) use ($q) {
                    $t->where('title', 'like', $q)
                        ->orWhere('excerpt', 'like', $q)
                        ->orWhere('content', 'like', $q);
                });
            })
            ->orderByDesc('published_at')
            ->orderBy('sort_order');
    }

    protected function sidebarData(): array
    {
        $recentPosts = CmsBlogPost::query()
            ->published()
            ->with(['translations', 'category.translations'])
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $categories = CmsBlogCategory::query()
            ->active()
            ->with('translations')
            ->withCount(['posts as posts_count' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $page = CmsPage::query()
            ->published()
            ->where('slug', 'blog')
            ->with('translations')
            ->first();

        return compact('recentPosts', 'categories', 'page');
    }
}
