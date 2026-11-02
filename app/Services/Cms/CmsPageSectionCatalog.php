<?php

namespace App\Services\Cms;

use App\Models\CmsBlogPost;
use App\Models\CmsFaq;
use App\Models\CmsPage;
use App\Models\CmsTeamMember;
use App\Models\CmsTestimonial;
use App\Models\HomeSlider;
use Illuminate\Support\Facades\Route;

class CmsPageSectionCatalog
{
    /**
     * Existing content blocks for a CMS page. Does not create or delete sections.
     *
     * @return list<array{
     *   order: int,
     *   type: string,
     *   title: ?string,
     *   items: int,
     *   is_active: bool,
     *   edit_url: ?string,
     *   items_url: ?string
     * }>
     */
    public function for(CmsPage $page): array
    {
        $editUrl = route('admin.cms.pages.edit', $page);

        return match ($page->template) {
            CmsPage::TEMPLATE_HOME => [
                $this->row(1, __('backend.cms.sections.types.slider'), null, HomeSlider::query()->count(), $editUrl, $this->routeIfExists('admin.cms.sliders.index')),
                $this->row(2, __('backend.cms.sections.types.features'), $this->plain($page->layout('feature_1_title')), 3, $this->routeIfExists('admin.cms.home.edit') ?: $editUrl, null),
                // $this->row(2, __('backend.cms.sections.types.team'), $this->relatedLayout('about', 'team_title'), CmsTeamMember::query()->count(), $this->relatedEditUrl('about') ?: $editUrl, $this->routeIfExists('admin.cms.team.index')),
                $this->row(3, __('backend.cms.sections.types.testimonials'), $this->relatedLayout('about', 'testimonials_title'), CmsTestimonial::query()->count(), $this->relatedEditUrl('about') ?: $editUrl, $this->routeIfExists('admin.cms.testimonials.index')),
                $this->row(4, __('backend.cms.sections.types.faq'), $this->relatedLayout('faq', 'faq_title'), CmsFaq::query()->count(), $this->relatedEditUrl('faq') ?: $editUrl, $this->routeIfExists('admin.cms.faqs.index')),
                $this->row(5, __('backend.cms.sections.types.services'), $this->relatedLayout('services', 'section_title'), 3, $this->routeIfExists('admin.cms.services.edit') ?: $this->relatedEditUrl('services'), null),
            ],
            CmsPage::TEMPLATE_SERVICES => [
                $this->row(1, __('backend.cms.sections.types.services'), $this->plain($page->layout('section_title') ?: $page->title), 3, $this->routeIfExists('admin.cms.services.edit') ?: $editUrl, null),
            ],
            CmsPage::TEMPLATE_ABOUT => [
                $this->row(1, __('backend.cms.sections.types.about'), $this->plain($page->layout('heading') ?: $page->title), 0, $editUrl, null),
                // $this->row(2, __('backend.cms.sections.types.team'), $this->plain($page->layout('team_title')), CmsTeamMember::query()->count(), $editUrl, $this->routeIfExists('admin.cms.team.index')),
                $this->row(3, __('backend.cms.sections.types.testimonials'), $this->plain($page->layout('testimonials_title')), CmsTestimonial::query()->count(), $editUrl, $this->routeIfExists('admin.cms.testimonials.index')),
            ],
            CmsPage::TEMPLATE_FAQ => [
                $this->row(1, __('backend.cms.sections.types.faq'), $this->plain($page->layout('faq_title') ?: $page->title), CmsFaq::query()->count(), $editUrl, $this->routeIfExists('admin.cms.faqs.index')),
                // $this->row(2, __('backend.cms.sections.types.cta'), $this->plain($page->layout('cta_title')), 0, $editUrl, null),
            ],
            CmsPage::TEMPLATE_BLOG => [
                $this->row(1, __('backend.cms.sections.types.blog'), $this->plain($page->title), CmsBlogPost::query()->count(), $editUrl, $this->routeIfExists('admin.blog.posts.index')),
                $this->row(2, __('backend.cms.sections.types.cta'), $this->plain($page->layout('cta_title')), 0, $editUrl, null),
            ],
            CmsPage::TEMPLATE_CONTACT => [
                $this->row(1, __('backend.cms.sections.types.contact'), $this->plain($page->layout('heading') ?: $page->title), 0, $editUrl, null),
                $this->row(2, __('backend.cms.sections.types.map'), $this->plain($page->layout('side_heading')), 0, $editUrl, null),
                $this->row(3, __('backend.cms.sections.types.cta'), $this->plain($page->layout('cta_title')), 0, $editUrl, null),
            ],
            default => [
                $this->row(1, __('backend.cms.sections.types.content'), $page->title, 0, $editUrl, null),
            ],
        };
    }

    public function countFor(CmsPage $page): int
    {
        return match ($page->template) {
            CmsPage::TEMPLATE_HOME => 5,
            CmsPage::TEMPLATE_SERVICES => 1,
            CmsPage::TEMPLATE_ABOUT => 3,
            CmsPage::TEMPLATE_FAQ => 2,
            CmsPage::TEMPLATE_BLOG => 2,
            CmsPage::TEMPLATE_CONTACT => 3,
            default => 1,
        };
    }

    /**
     * @return array{
     *   order: int,
     *   type: string,
     *   title: ?string,
     *   items: int,
     *   is_active: bool,
     *   edit_url: ?string,
     *   items_url: ?string
     * }
     */
    protected function row(int $order, string $type, ?string $title, int $items, ?string $editUrl, ?string $itemsUrl): array
    {
        return [
            'order' => $order,
            'type' => $type,
            'title' => $title,
            'items' => $items,
            'is_active' => true,
            'edit_url' => $editUrl,
            'items_url' => $itemsUrl,
        ];
    }

    protected function routeIfExists(string $name): ?string
    {
        return Route::has($name) ? route($name) : null;
    }

    protected function relatedPage(string $slug): ?CmsPage
    {
        static $pages = [];

        if (! array_key_exists($slug, $pages)) {
            $pages[$slug] = CmsPage::query()->where('slug', $slug)->with('translations')->first();
        }

        return $pages[$slug];
    }

    protected function relatedLayout(string $slug, string $key): ?string
    {
        return $this->plain($this->relatedPage($slug)?->layout($key));
    }

    protected function plain(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?? '');

        return $text !== '' ? $text : null;
    }

    protected function relatedEditUrl(string $slug): ?string
    {
        $page = $this->relatedPage($slug);

        return $page ? route('admin.cms.pages.edit', $page) : null;
    }
}
