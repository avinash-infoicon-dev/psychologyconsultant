<?php

namespace App\Http\Controllers\Frontend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use App\Models\CmsPage;
use App\Models\CmsTeamMember;
use App\Models\CmsTestimonial;
use App\Models\Professional;
use App\Services\Cms\CmsServicesPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        if ($slug === 'services' && ! CmsPage::query()->where('slug', 'services')->exists()) {
            app(CmsServicesPageService::class)->ensure();
        }

        $page = CmsPage::query()
            ->published()
            ->where('slug', $slug)
            ->with(['translations', 'category.translations'])
            ->firstOrFail();

        if ($page->isBlogTemplate()) {
            return redirect()->route('frontend.blog');
        }

        $testimonials = collect();
        $teamMembers = collect();
        $psychologists = collect();

        $faqs = collect();

        if ($page->isAboutTemplate()) {
            $testimonials = CmsTestimonial::query()->active()->with('translations')->orderBy('sort_order')->get();
            $teamMembers = CmsTeamMember::query()->active()->with('translations')->orderBy('sort_order')->get();
            $psychologists = Professional::query()
                ->psychologists()
                ->listedForPatients()
                ->with(['user', 'specializations.translations', 'professionalType'])
                ->orderByDesc('is_featured')
                ->orderByDesc('rating')
                ->orderBy('first_name')
                ->limit(4)
                ->get();
        }

        if ($page->isFaqTemplate()) {
            $faqs = CmsFaq::query()->active()->with('translations')->orderBy('sort_order')->get();
        }

        return view('frontend.cms.pages.show', compact('page', 'testimonials', 'teamMembers', 'psychologists', 'faqs'));
    }
}
