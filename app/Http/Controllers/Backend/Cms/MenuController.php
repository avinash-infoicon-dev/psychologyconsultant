<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsCategory;
use App\Models\CmsMenu;
use App\Models\CmsPage;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $location = $request->input('location', 'header');

        $menus = CmsMenu::query()
            ->with(['translations', 'children.translations'])
            ->roots()
            ->when($location, fn ($q) => $q->where('location', $location))
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('backend.cms.menus.index', [
            'menus' => $menus,
            'locations' => config('cms.locations', []),
            'location' => $location,
        ]);
    }

    public function create(Request $request): View
    {
        return view('backend.cms.menus.create', $this->formData($request->input('location', 'header')));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $menu = CmsMenu::query()->create($data['menu']);
            $menu->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.cms.menus.index', ['location' => $data['menu']['location']])
            ->with('success', __('backend.cms.menus.created'));
    }

    public function edit(CmsMenu $menu): View
    {
        $menu->load('translations');

        return view('backend.cms.menus.edit', array_merge(
            $this->formData($menu->location, $menu),
            ['menu' => $menu]
        ));
    }

    public function update(Request $request, CmsMenu $menu): RedirectResponse
    {
        $data = $this->validated($request, $menu);

        DB::transaction(function () use ($menu, $data) {
            $menu->update($data['menu']);
            $menu->syncTranslations($data['translations']);
        });

        return redirect()
            ->route('admin.cms.menus.index', ['location' => $data['menu']['location']])
            ->with('success', __('backend.cms.menus.updated'));
    }

    public function destroy(CmsMenu $menu): RedirectResponse
    {
        $location = $menu->location;
        $menu->children()->update(['parent_id' => null]);
        $menu->delete();

        return redirect()
            ->route('admin.cms.menus.index', ['location' => $location])
            ->with('success', __('backend.cms.menus.deleted'));
    }

    protected function formData(string $location, ?CmsMenu $menu = null): array
    {
        return [
            'locales' => Locales::codes(),
            'locations' => config('cms.locations', []),
            'types' => config('cms.menu_types', []),
            'pages' => CmsPage::query()->with('translations')->orderBy('sort_order')->get(),
            'categories' => CmsCategory::query()->with('translations')->orderBy('sort_order')->get(),
            'parents' => CmsMenu::query()
                ->with('translations')
                ->where('location', $location)
                ->when($menu, fn ($q) => $q->where('id', '!=', $menu->id))
                ->orderBy('sort_order')
                ->get(),
            'location' => $location,
        ];
    }

    protected function validated(Request $request, ?CmsMenu $menu = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
        }

        $types = array_keys(config('cms.menu_types', []));
        $locations = array_keys(config('cms.locations', []));

        $data = $request->validate(array_merge([
            'location' => ['required', Rule::in($locations)],
            'parent_id' => ['nullable', 'integer', 'exists:cms_menus,id'],
            'type' => ['required', Rule::in($types)],
            'reference_id' => ['nullable', 'integer'],
            'url' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $type = $data['type'];
        $referenceId = $data['reference_id'] ?? null;

        if ($type === CmsMenu::TYPE_PAGE) {
            $request->validate(['reference_id' => ['required', 'integer', 'exists:cms_pages,id']]);
        } elseif ($type === CmsMenu::TYPE_CATEGORY) {
            $request->validate(['reference_id' => ['required', 'integer', 'exists:cms_categories,id']]);
        } elseif ($type === CmsMenu::TYPE_URL) {
            $request->validate(['url' => ['required', 'string', 'max:500']]);
            $referenceId = null;
        } elseif ($type === CmsMenu::TYPE_ROUTE) {
            $request->validate(['route_name' => ['required', 'string', 'max:255']]);
            $referenceId = null;
        }

        if (! empty($data['parent_id']) && $menu && (int) $data['parent_id'] === $menu->id) {
            abort(422, __('backend.cms.menus.cannot_parent_self'));
        }

        return [
            'menu' => [
                'location' => $data['location'],
                'parent_id' => $data['parent_id'] ?? null,
                'type' => $type,
                'reference_id' => in_array($type, [CmsMenu::TYPE_PAGE, CmsMenu::TYPE_CATEGORY], true) ? $referenceId : null,
                'url' => $data['url'] ?? null,
                'route_name' => $type === CmsMenu::TYPE_ROUTE ? ($data['route_name'] ?? null) : null,
                'target' => $data['target'],
                'icon' => $data['icon'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'sort_order' => $data['sort_order'] ?? 0,
            ],
            'translations' => $data['translations'],
        ];
    }
}
