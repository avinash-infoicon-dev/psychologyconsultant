<?php

namespace App\Http\Controllers\Backend\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use App\Models\CmsPage;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $faqs = CmsFaq::query()
            ->with('translations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->whereHas('translations', function ($t) use ($q) {
                    $t->where('question', 'like', $q)->orWhere('answer', 'like', $q);
                });
            })
            ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'active');
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $faqPage = CmsPage::query()->where('slug', 'faq')->first();

        return view('backend.cms.faqs.index', compact('faqs', 'faqPage'));
    }

    public function create(): View
    {
        return view('backend.cms.faqs.create', ['locales' => Locales::codes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $item = CmsFaq::query()->create([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $item->syncTranslations($data['translations']);
        });

        return redirect()->route('admin.cms.faqs.index')
            ->with('success', __('backend.cms.faqs.created'));
    }

    public function edit(CmsFaq $faq): View
    {
        $faq->load('translations');

        return view('backend.cms.faqs.edit', [
            'faq' => $faq,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, CmsFaq $faq): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($faq, $data) {
            $faq->update([
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $faq->syncTranslations($data['translations']);
        });

        return redirect()->route('admin.cms.faqs.index')
            ->with('success', __('backend.cms.faqs.updated'));
    }

    public function destroy(CmsFaq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.cms.faqs.index')
            ->with('success', __('backend.cms.faqs.deleted'));
    }

    protected function validated(Request $request): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.question"] = ['required', 'string', 'max:500'];
            $localeRules["translations.{$locale}.answer"] = ['required', 'string'];
        }

        $data = $request->validate(array_merge([
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        return [
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $data['sort_order'] ?? 0,
            'translations' => $data['translations'],
        ];
    }
}
