<?php

namespace App\Services\Cms;

use App\Models\CmsCategory;
use App\Models\CmsMenu;
use App\Models\CmsPage;
use App\Support\CmsServicesDefaults;
use App\Support\Locales;
use Illuminate\Support\Facades\Route;

class CmsServicesPageService
{
    public const MAX_CARDS = 20;

    public function ensure(): CmsPage
    {
        $categoryId = CmsCategory::query()->where('slug', 'company')->value('id');

        $page = CmsPage::query()->firstOrCreate(
            ['slug' => 'services'],
            [
                'cms_category_id' => $categoryId,
                'template' => CmsPage::TEMPLATE_SERVICES,
                'is_active' => true,
                'sort_order' => 2,
                'published_at' => now(),
            ]
        );

        if ($page->template !== CmsPage::TEMPLATE_SERVICES) {
            $page->update(['template' => CmsPage::TEMPLATE_SERVICES]);
        }

        $defaults = CmsServicesDefaults::translations();
        $page->load('translations');

        foreach (Locales::codes() as $locale) {
            if ($page->translations->firstWhere('locale', $locale)) {
                continue;
            }

            $row = $defaults[$locale] ?? $defaults['en'] ?? null;
            if (! $row) {
                continue;
            }

            $page->translations()->create([
                'locale' => $locale,
                'title' => $row['title'],
                'content' => $row['content'],
                'meta_title' => $row['meta_title'],
                'meta_description' => $row['meta_description'],
                'layout' => $row['layout'],
            ]);
        }

        $this->ensureHeaderMenu($page->fresh('translations'));

        return $page->fresh('translations');
    }

    /**
     * @param  array<string, mixed>  $layout
     * @return list<int>
     */
    public static function cardIndexes(array $layout): array
    {
        $found = [];
        foreach ($layout as $key => $_) {
            if (preg_match('/^item_(\d+)_/', (string) $key, $m)) {
                $n = (int) $m[1];
                if ($n >= 1 && $n <= self::MAX_CARDS) {
                    $found[$n] = true;
                }
            }
        }

        $indexes = array_map('intval', array_keys($found));
        sort($indexes);

        return $indexes !== [] ? $indexes : [1, 2, 3];
    }

    /**
     * @return list<array{title: string, desc: string, icon: string, bg: string, image: string, delay: string}>
     */
    public static function frontendCards(?CmsPage $page): array
    {
        $layout = is_array($page?->layout()) ? $page->layout() : [];
        $indexes = $page ? self::cardIndexes($layout) : [1, 3, 2];
        $items = [];
        $delays = ['.3', '.5', '.7'];

        foreach (array_values($indexes) as $i => $n) {
            $title = trim((string) ($page?->layout('item_'.$n.'_title') ?: (
                in_array($n, [1, 2, 3], true) ? __('frontend.home.service_item_'.$n.'_title') : ''
            )));
            if ($title === '') {
                continue;
            }

            $fallbackFile = $n === 2 ? 'blog-3.webp' : 'blog-1.webp';
            $image = $page?->layoutUrl('item_'.$n.'_image')
                ?: asset('frontendassets/assets/img/blog/'.$fallbackFile);

            $items[] = [
                'title' => $title,
                'desc' => (string) ($page?->layout('item_'.$n.'_desc') ?: (
                    in_array($n, [1, 2, 3], true) ? __('frontend.home.service_item_'.$n.'_desc') : ''
                )),
                'icon' => (string) ($page?->layout('item_'.$n.'_icon') ?: 'fa-solid fa-brain'),
                'bg' => (string) ($page?->layout('item_'.$n.'_bg') ?: ($i % 2 === 1 ? 'primary' : 'secondary')),
                'image' => $image,
                'delay' => $delays[$i % 3],
            ];
        }

        return $items;
    }

    public function published(): ?CmsPage
    {
        return CmsPage::query()
            ->published()
            ->where(function ($query) {
                $query->where('slug', 'services')
                    ->orWhere('template', CmsPage::TEMPLATE_SERVICES);
            })
            ->with('translations')
            ->first();
    }

    protected function ensureHeaderMenu(CmsPage $page): void
    {
        $existing = CmsMenu::query()
            ->where('location', 'header')
            ->where(function ($query) use ($page) {
                $query->where(function ($inner) use ($page) {
                    $inner->where('type', CmsMenu::TYPE_PAGE)
                        ->where('reference_id', $page->id);
                })->orWhere(function ($inner) {
                    $inner->where('type', CmsMenu::TYPE_ROUTE)
                        ->where('route_name', 'frontend.services');
                })->orWhere(function ($inner) {
                    $inner->where('url', '/services');
                });
            })
            ->first();

        $menu = $existing ?: CmsMenu::query()->create([
            'location' => 'header',
            'type' => Route::has('frontend.services') ? CmsMenu::TYPE_ROUTE : CmsMenu::TYPE_PAGE,
            'route_name' => Route::has('frontend.services') ? 'frontend.services' : null,
            'reference_id' => $page->id,
            'parent_id' => null,
            'url' => '/services',
            'target' => '_self',
            'icon' => 'bi bi-grid',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        if (! $existing) {
            $menu->syncTranslations(Locales::pick([
                'en' => ['title' => 'Service'],
                'fr_CH' => ['title' => 'Service'],
                'de' => ['title' => 'Service'],
                'it' => ['title' => 'Servizio'],
            ]));
        }
    }
}
