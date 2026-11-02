<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsTestimonial;
use App\Services\Cms\CmsUploadService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(protected CmsUploadService $uploads) {}

    public function index(Request $request): View
    {
        $testimonials = CmsTestimonial::query()
            ->with('translations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('translations', function ($t) use ($q) {
                    $t->where('author_name', 'like', $q)
                        ->orWhere('author_role', 'like', $q)
                        ->orWhere('quote', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('backend.cms.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('backend.cms.testimonials.create', ['locales' => Locales::codes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $item = CmsTestimonial::query()->create([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->sync($item, $request, $data['translations']);
        });

        return redirect()->route('admin.cms.testimonials.index')
            ->with('success', __('backend.cms.testimonials.created'));
    }

    public function edit(CmsTestimonial $testimonial): View
    {
        $testimonial->load('translations');

        return view('backend.cms.testimonials.edit', [
            'testimonial' => $testimonial,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, CmsTestimonial $testimonial): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($testimonial, $request, $data) {
            $testimonial->update([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $this->sync($testimonial, $request, $data['translations']);
        });

        return redirect()->route('admin.cms.testimonials.index')
            ->with('success', __('backend.cms.testimonials.updated'));
    }

    public function destroy(CmsTestimonial $testimonial): RedirectResponse
    {
        $this->uploads->delete($testimonial->photo_path, 'cms/testimonials/');
        $testimonial->delete();

        return redirect()->route('admin.cms.testimonials.index')
            ->with('success', __('backend.cms.testimonials.deleted'));
    }

    protected function validated(Request $request): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.author_name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.author_role"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.quote"] = ['required', 'string'];
        }

        $data = $request->validate(array_merge([
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        return [
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'translations' => $data['translations'],
        ];
    }

    protected function sync(CmsTestimonial $item, Request $request, array $translations): void
    {
        $path = $item->photo_path;
        if ($request->boolean('remove_photo')) {
            $this->uploads->delete($path, 'cms/testimonials/');
            $path = null;
        }
        if ($file = $request->file('photo')) {
            $this->uploads->delete($path, 'cms/testimonials/');
            $path = $this->uploads->store('cms/testimonials/'.$item->id, $file)['path'];
        }
        $item->update(['photo_path' => $path]);

        foreach (Locales::codes() as $locale) {
            $item->translations()->updateOrCreate(
                ['locale' => $locale],
                $translations[$locale] ?? []
            );
        }
    }
}
