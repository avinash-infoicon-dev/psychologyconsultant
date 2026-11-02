<?php

namespace App\Services\Cms;

use App\Models\CmsCategory;
use App\Models\CmsPage;
use App\Support\CmsHomeDefaults;
use App\Support\Locales;

class CmsHomePageService
{
    public function ensure(): CmsPage
    {
        $categoryId = CmsCategory::query()->where('slug', 'company')->value('id');

        $page = CmsPage::query()->firstOrCreate(
            ['slug' => 'home'],
            [
                'cms_category_id' => $categoryId,
                'template' => CmsPage::TEMPLATE_HOME,
                'is_active' => true,
                'sort_order' => 0,
                'published_at' => now(),
            ]
        );

        if ($page->template !== CmsPage::TEMPLATE_HOME) {
            $page->update(['template' => CmsPage::TEMPLATE_HOME]);
        }

        $page->load('translations');

        foreach (Locales::codes() as $locale) {
            $existing = $page->translations->firstWhere('locale', $locale);
            $defaults = CmsHomeDefaults::featureLayout($locale);
            $layout = is_array($existing?->layout) ? $existing->layout : [];
            $merged = $layout + $defaults;

            if ($existing) {
                if ($merged !== $layout) {
                    $existing->update(['layout' => $merged]);
                }
                continue;
            }

            $page->translations()->create([
                'locale' => $locale,
                'title' => $locale === 'de' ? 'Startseite' : ($locale === 'fr_CH' ? 'Accueil' : 'Home'),
                'content' => 'Homepage.',
                'meta_title' => $locale === 'de' ? 'Startseite' : ($locale === 'fr_CH' ? 'Accueil' : 'Home'),
                'layout' => $merged,
            ]);
        }

        return $page->fresh('translations');
    }

    public function published(): ?CmsPage
    {
        return CmsPage::query()
            ->published()
            ->where(function ($query) {
                $query->where('slug', 'home')
                    ->orWhere('template', CmsPage::TEMPLATE_HOME);
            })
            ->with('translations')
            ->first();
    }
}
