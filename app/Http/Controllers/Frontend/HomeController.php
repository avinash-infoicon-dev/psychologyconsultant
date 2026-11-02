<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use App\Models\CmsPage;
use App\Models\HomeSlider;
use App\Models\CmsTeamMember;
use App\Models\CmsTestimonial;
use App\Models\Professional;
use App\Services\Cms\CmsHomePageService;
use App\Services\Cms\CmsServicesPageService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $homeSliders = HomeSlider::query()
            ->active()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $testimonials = CmsTestimonial::query()
            ->active()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $teamMembers = CmsTeamMember::query()
            ->active()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $psychologists = Professional::query()
            ->psychologists()
            ->listedForPatients()
            ->with(['user', 'specializations.translations', 'professionalType'])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->orderBy('first_name')
            ->limit(4)
            ->get();

        $faqs = CmsFaq::query()
            ->active()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        $aboutPage = CmsPage::query()
            ->published()
            ->where('slug', 'about')
            ->with('translations')
            ->first();

        $faqPage = CmsPage::query()
            ->published()
            ->where('slug', 'faq')
            ->with('translations')
            ->first();

        $servicesPage = app(CmsServicesPageService::class)->published()
            ?? app(CmsServicesPageService::class)->ensure();

        $homePage = app(CmsHomePageService::class)->published()
            ?? app(CmsHomePageService::class)->ensure();

        return view('frontend.home', compact('homeSliders', 'testimonials', 'teamMembers', 'psychologists', 'aboutPage', 'faqs', 'faqPage', 'servicesPage', 'homePage'));
    }
}
