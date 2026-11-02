<?php

namespace App\Http\Controllers\Frontend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        $category = CmsCategory::query()
            ->active()
            ->where('slug', $slug)
            ->with('translations')
            ->firstOrFail();

        $pages = $category->pages()
            ->published()
            ->with('translations')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('frontend.cms.categories.show', compact('category', 'pages'));
    }
}
