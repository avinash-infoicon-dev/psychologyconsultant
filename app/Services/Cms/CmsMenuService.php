<?php

namespace App\Services\Cms;

use App\Models\CmsCategory;
use App\Models\CmsMenu;
use App\Models\CmsPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class CmsMenuService
{
    /**
     * Active root menus for a location, with resolved href + children.
     *
     * @return Collection<int, array{menu: CmsMenu, title: string, href: string, target: string, icon: ?string, children: array}>
     */
    public function forLocation(string $location, ?string $locale = null): Collection
    {
        $locale = $locale ?: app()->getLocale();

        $menus = CmsMenu::query()
            ->active()
            ->location($location)
            ->roots()
            ->with([
                'translations',
                'children' => fn ($q) => $q->active()->orderBy('sort_order')->with('translations'),
            ])
            ->orderBy('sort_order')
            ->get();

        return $this->ensureServicesAfterAbout(
            $menus->map(fn (CmsMenu $menu) => $this->mapItem($menu, $locale))->values()
        );
    }

    /**
     * Resolve the public URL for a menu item.
     * Preference: linked active page/category/route → custom url → config default.
     */
    public function resolveUrl(CmsMenu $menu): string
    {
        //$default = config('cms.default_url', '/');
        $default = route('frontend.home');

        return match ($menu->type) {
            CmsMenu::TYPE_PAGE => $this->resolvePageUrl($menu->reference_id) ?? $this->fallbackUrl($menu, $default),
            CmsMenu::TYPE_CATEGORY => $this->resolveCategoryUrl($menu->reference_id) ?? $this->fallbackUrl($menu, $default),
            CmsMenu::TYPE_ROUTE => $this->resolveRouteUrl($menu->route_name) ?? $this->fallbackUrl($menu, $default),
            CmsMenu::TYPE_URL => $this->fallbackUrl($menu, $default),
            default => $this->fallbackUrl($menu, $default),
        };
    }

    protected function mapItem(CmsMenu $menu, string $locale): array
    {
        $title = $menu->translate($locale)?->title ?? $menu->title ?? '';

        return [
            'menu' => $menu,
            'title' => $title,
            'href' => $this->resolveUrl($menu),
            'target' => $menu->target ?: '_self',
            'icon' => $menu->icon,
            'children' => $menu->children
                ->map(fn (CmsMenu $child) => $this->mapItem($child, $locale))
                ->values()
                ->all(),
        ];
    }

    protected function resolvePageUrl(?int $pageId): ?string
    {
        if (! $pageId) {
            return null;
        }

        $page = CmsPage::query()->published()->find($pageId);

        if (! $page) {
            return null;
        }

        if ($page->slug === 'home' && Route::has('frontend.home')) {
            return route('frontend.home');
        }

        if ($page->slug === 'faq' && Route::has('frontend.faq')) {
            return route('frontend.faq');
        }

        if ($page->slug === 'about' && Route::has('frontend.about')) {
            return route('frontend.about');
        }

        if ($page->slug === 'blog' && Route::has('frontend.blog')) {
            return route('frontend.blog');
        }

        if ($page->slug === 'contact' && Route::has('frontend.contact')) {
            return route('frontend.contact');
        }

        if ($page->isServicesTemplate() && Route::has('frontend.services')) {
            return route('frontend.services');
        }

        return route('frontend.cms.pages.show', $page->slug);
    }

    protected function resolveCategoryUrl(?int $categoryId): ?string
    {
        if (! $categoryId) {
            return null;
        }

        $category = CmsCategory::query()->active()->find($categoryId);

        return $category ? route('frontend.cms.categories.show', $category->slug) : null;
    }

    protected function resolveRouteUrl(?string $routeName): ?string
    {
        if (! $routeName || ! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  Collection<int, array{menu: CmsMenu, title: string, href: string, target: string, icon: ?string, children: array}>  $items
     * @return Collection<int, array{menu: CmsMenu, title: string, href: string, target: string, icon: ?string, children: array}>
     */
    protected function ensureServicesAfterAbout(Collection $items): Collection
    {
        if (! Route::has('frontend.services')) {
            return $items;
        }

        $servicesHref = route('frontend.services');
        $alreadyListed = $items->contains(function (array $item) use ($servicesHref) {
            return rtrim((string) $item['href'], '/') === rtrim($servicesHref, '/')
                || str_contains((string) $item['href'], '/services');
        });

        if ($alreadyListed) {
            return $items;
        }

        $entry = [
            'menu' => null,
            'title' => __('frontend.nav.service'),
            'href' => $servicesHref,
            'target' => '_self',
            'icon' => 'bi bi-grid',
            'children' => [],
        ];

        $aboutIndex = $items->search(function (array $item) {
            $href = (string) $item['href'];

            return str_contains($href, '/about')
                || strcasecmp((string) $item['title'], 'About') === 0
                || strcasecmp((string) $item['title'], __('frontend.nav.about')) === 0;
        });

        if ($aboutIndex === false) {
            $items->splice(1, 0, [$entry]);
        } else {
            $items->splice($aboutIndex + 1, 0, [$entry]);
        }

        return $items->values();
    }

    protected function fallbackUrl(CmsMenu $menu, string $default): string
    {
        $url = trim((string) $menu->url);

        if ($url === '' || $url === '/') {
            return $default;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '//')) {
            return $url;
        }

        // Keep relative paths inside the Laravel app (not server document root).
        return url($url);
    }
}
