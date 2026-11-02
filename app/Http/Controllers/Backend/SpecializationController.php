<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecializationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $filtered = Specialization::query()
            ->with('translations')
            ->withCount('professionals')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', $q)
                        ->orWhere('slug', 'like', $q)
                        ->orWhere('description', 'like', $q)
                        ->orWhereHas('translations', function ($t) use ($q) {
                            $t->where('name', 'like', $q)
                                ->orWhere('description', 'like', $q);
                        });
                });
            });

        // Legacy: mixed active/inactive including archived.
        // $specializations = (clone $filtered)
        //     ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
        //         $query->where('status', $status === 'active');
        //     })
        //     ->orderBy('name')
        //     ->paginate(15)
        //     ->withQueryString();

        $specializationsQuery = $filtered->clone();
        if ($status === 'archived') {
            $specializationsQuery->archived();
        } else {
            $specializationsQuery->notArchived();
            if (in_array($status, ['active', 'inactive'], true)) {
                $specializationsQuery->where('status', $status === 'active');
            }
        }

        $specializations = $specializationsQuery
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $archivedSpecializations = $status
            ? collect()
            : $filtered->clone()->archived()->orderBy('name')->get();

        return view('backend.specializations.index', compact('specializations', 'archivedSpecializations'));
    }

    public function create(): View
    {
        return view('backend.specializations.create', [
            'specialization' => new Specialization(['status' => true]),
            'locales' => Locales::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $translations] = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['status'] = $request->boolean('status');

        DB::transaction(function () use ($data, $translations) {
            $specialization = Specialization::query()->create($data);
            $specialization->syncTranslations($translations);
        });

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', __('backend.specializations.created'));
    }

    public function edit(Specialization $specialization): View
    {
        $specialization->load('translations');

        return view('backend.specializations.edit', [
            'specialization' => $specialization,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, Specialization $specialization): RedirectResponse
    {
        [$data, $translations] = $this->validated($request, $specialization);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['status'] = $request->boolean('status');

        DB::transaction(function () use ($specialization, $data, $translations) {
            if ($specialization->isArchived()) {
                unset($data['status']);
            }
            $specialization->update($data);
            $specialization->syncTranslations($translations);
        });

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', __('backend.specializations.updated'));
    }

    public function destroy(Specialization $specialization): RedirectResponse
    {
        // Hard delete removed — archive so psychologist history is kept.
        // if ($specialization->professionals()->exists()) {
        //     return back()->withErrors([
        //         'specialization' => __('backend.specializations.cannot_delete_with_professionals'),
        //     ]);
        // }
        //
        // $specialization->delete();
        //
        // return redirect()
        //     ->route('admin.specializations.index')
        //     ->with('success', __('backend.specializations.deleted'));

        return $this->archive($specialization);
    }

    public function archive(Specialization $specialization): RedirectResponse
    {
        if ($specialization->isArchived()) {
            return back()->with('success', __('backend.specializations.already_archived'));
        }

        $specialization->archive();

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', __('backend.specializations.archived'));
    }

    public function toggleStatus(Specialization $specialization): RedirectResponse
    {
        if ($specialization->isArchived()) {
            return back()->withErrors([
                'specialization' => __('backend.specializations.cannot_activate_archived'),
            ]);
        }

        $specialization->update([
            'status' => ! $specialization->status,
        ]);

        return back()->with(
            'success',
            $specialization->status
                ? __('backend.specializations.activated')
                : __('backend.specializations.deactivated')
        );
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, array<string, mixed>>}
     */
    protected function validated(Request $request, ?Specialization $specialization = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.description"] = ['nullable', 'string'];
        }

        $data = $request->validate(array_merge([
            // Legacy single-language fields:
            // 'name' => ['required', 'string', 'max:255'],
            // 'description' => ['nullable', 'string'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('specializations', 'slug')->ignore($specialization?->id),
            ],
            'status' => ['nullable', 'boolean'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $translations = [];
        foreach ($data['translations'] as $locale => $row) {
            $translations[$locale] = [
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
            ];
        }

        $primaryLocale = Locales::codes()[0] ?? 'fr_CH';
        $primary = $translations[$primaryLocale] ?? reset($translations) ?: [];

        return [
            [
                'name' => $primary['name'] ?? '',
                'slug' => $data['slug'] ?? null,
                'description' => $primary['description'] ?? null,
            ],
            $translations,
        ];
    }
}
