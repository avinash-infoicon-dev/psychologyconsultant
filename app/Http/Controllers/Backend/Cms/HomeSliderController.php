<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Services\Cms\HomeSliderImageService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeSliderController extends Controller
{
    public function __construct(protected HomeSliderImageService $images) {}

    public function index(Request $request): View
    {
        $sliders = HomeSlider::query()
            ->with('translations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('translations', function ($t) use ($q) {
                    $t->where('title', 'like', $q)
                        ->orWhere('subtitle', 'like', $q)
                        ->orWhere('button_text', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('backend.cms.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        return view('backend.cms.sliders.create', [
            'locales' => Locales::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $slider = HomeSlider::query()->create([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->syncTranslationsWithImages($slider, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.cms.sliders.index')
            ->with('success', __('backend.cms.sliders.created'));
    }

    public function edit(HomeSlider $slider): View
    {
        $slider->load('translations');

        return view('backend.cms.sliders.edit', [
            'slider' => $slider,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, HomeSlider $slider): RedirectResponse
    {
        $slider->loadMissing('translations');
        $data = $this->validated($request, $slider);

        DB::transaction(function () use ($slider, $request, $data) {
            $slider->update([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->syncTranslationsWithImages($slider, $request, $data['translations']);
        });

        return redirect()
            ->route('admin.cms.sliders.index')
            ->with('success', __('backend.cms.sliders.updated'));
    }

    public function destroy(HomeSlider $slider): RedirectResponse
    {
        $this->images->deleteForSlider($slider);
        $slider->delete();

        return redirect()
            ->route('admin.cms.sliders.index')
            ->with('success', __('backend.cms.sliders.deleted'));
    }

    protected function validated(Request $request, ?HomeSlider $slider = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.title"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.subtitle"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.button_text"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.button_url"] = ['nullable', 'string', 'max:500'];
            $localeRules["translations.{$locale}.image"] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
            $localeRules["translations.{$locale}.remove_image"] = ['nullable', 'boolean'];
        }

        $data = $request->validate(array_merge([
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $hasImage = false;
        foreach (Locales::codes() as $locale) {
            if ($request->hasFile("translations.{$locale}.image")) {
                $hasImage = true;
                break;
            }
            $existing = $slider?->translations->firstWhere('locale', $locale);
            if ($existing?->image_path && ! $request->boolean("translations.{$locale}.remove_image")) {
                $hasImage = true;
                break;
            }
        }

        if (! $hasImage) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'translations.'.(Locales::codes()[0] ?? 'en').'.image' => __('backend.cms.sliders.image_required'),
            ]);
        }

        $translations = $data['translations'];
        foreach ($translations as $locale => $row) {
            unset($translations[$locale]['image'], $translations[$locale]['remove_image']);
        }

        return [
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'translations' => $translations,
        ];
    }

    protected function syncTranslationsWithImages(HomeSlider $slider, Request $request, array $translations): void
    {
        $slider->load('translations');

        foreach (Locales::codes() as $locale) {
            $row = $translations[$locale] ?? [];
            unset($row['image'], $row['remove_image']);

            $existing = $slider->translations->firstWhere('locale', $locale);
            $path = $existing?->image_path;
            $originalName = $existing?->image_original_name;

            if ($request->boolean("translations.{$locale}.remove_image")) {
                $this->images->delete($path);
                $path = null;
                $originalName = null;
            }

            $file = $request->file("translations.{$locale}.image");
            if ($file) {
                $this->images->delete($path);
                $stored = $this->images->store($slider, $locale, $file);
                $path = $stored['image_path'];
                $originalName = $stored['image_original_name'];
            }

            $row['image_path'] = $path;
            $row['image_original_name'] = $originalName;
            $row['button_url'] = $row['button_url'] ?: '#contact-form';

            $slider->translations()->updateOrCreate(
                ['locale' => $locale],
                $row
            );
        }
    }
}
