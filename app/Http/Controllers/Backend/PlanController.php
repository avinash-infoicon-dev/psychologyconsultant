<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Payment\StripeService;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class PlanController extends Controller
{
    public function __construct(protected StripeService $stripe) {}

    public function index(Request $request): View
    {
        $plans = Plan::query()
            ->with('translations')
            ->withCount('subscriptions')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', $q)
                        ->orWhere('slug', 'like', $q)
                        ->orWhere('description', 'like', $q)
                        ->orWhere('motto', 'like', $q)
                        ->orWhereHas('translations', function ($t) use ($q) {
                            $t->where('name', 'like', $q)
                                ->orWhere('motto', 'like', $q)
                                ->orWhere('description', 'like', $q);
                        });
                });
            })
            // Legacy boolean filter:
            // ->when(in_array($request->input('status'), ['active', 'inactive'], true), function ($query) use ($request) {
            //     $query->where('is_active', $request->input('status') === 'active');
            // })
            ->when($this->requestedStatus($request), function ($query, string $status) {
                $query->where('status', $status);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('backend.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('backend.plans.create', [
            'locales' => Locales::codes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$planData, $translations] = $this->validated($request);
        $planData['slug'] = $planData['slug'] ?: Str::slug($planData['name']);

        try {
            DB::transaction(function () use ($planData, $translations) {
                $plan = Plan::query()->create($planData);
                $plan->syncTranslations($translations);

                $stripeIds = $this->stripe->createCatalogPlan(
                    $plan->name,
                    (int) $plan->price,
                    (string) $plan->currency,
                    (string) $plan->billing_interval,
                    $plan->description,
                    [
                        'local_plan_id' => (string) $plan->id,
                        'slug' => (string) $plan->slug,
                    ],
                );

                $plan->update([
                    'plan_id' => $stripeIds['plan_id'],
                    'stripe_price_id' => $stripeIds['stripe_price_id'],
                ]);
            });
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['plan' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.plans.index')
            ->with('success', __('backend.plans.created'));
    }

    public function edit(Plan $plan): View
    {
        $plan->load('translations');

        return view('backend.plans.edit', [
            'plan' => $plan,
            'locales' => Locales::codes(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        //dd($request->all());
        [$planData, $translations] = $this->validated($request, $plan);

        DB::transaction(function () use ($plan, $planData, $translations) {
            $plan->update($planData);
            $plan->syncTranslations($translations);
        });

        return redirect()
            ->route('admin.plans.index')
            ->with('success', __('backend.plans.updated'));
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        // Plans used by customers must not be removed. Archive instead of delete.
        // if ($plan->subscriptions()->exists()) {
        //     return back()->withErrors(['plan' => __('backend.plans.cannot_delete_with_subscriptions')]);
        // }
        //
        // $plan->delete();
        //
        // return redirect()
        //     ->route('admin.plans.index')
        //     ->with('success', __('backend.plans.deleted'));

        return $this->archive($plan);
    }

    public function archive(Plan $plan): RedirectResponse
    {
        if ($plan->isArchived()) {
            return back()->with('success', __('backend.plans.already_archived'));
        }

        $plan->archive();

        return redirect()
            ->route('admin.plans.index')
            ->with('success', __('backend.plans.archived'));
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, array<string, mixed>>}
     */
    protected function validated(Request $request, ?Plan $plan = null): array
    {
        $localeRules = [];
        foreach (Locales::codes() as $locale) {
            $localeRules["translations.{$locale}.name"] = ['required', 'string', 'max:255'];
            $localeRules["translations.{$locale}.motto"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.description"] = ['nullable', 'string'];
            $localeRules["translations.{$locale}.response_time_label"] = ['nullable', 'string', 'max:255'];
            $localeRules["translations.{$locale}.features_text"] = ['nullable', 'string'];
        }

        $data = $request->validate(array_merge([
            // Legacy single-language fields (kept for old posts / fallback columns):
            // 'name' => ['required', 'string', 'max:255'],
            // 'description' => ['nullable', 'string'],
            // 'motto' => ['nullable', 'string', 'max:255'],
            // 'response_time_label' => ['nullable', 'string', 'max:255'],
            // 'features_text' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('plans', 'slug')->ignore($plan?->id)],
            'plan_type' => ['required', Rule::in([Plan::TYPE_SUBSCRIPTION, Plan::TYPE_ON_DEMAND, Plan::TYPE_ADDON])],
            'price_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            // Old intervals only:
            // 'billing_interval' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            // Flat list (UI filtered by plan type):
            // 'billing_interval' => ['required', Rule::in(['monthly', 'quarterly', 'yearly', 'per_exchange'])],
            'billing_interval' => [
                'required',
                Rule::in(
                    $request->input('plan_type') === Plan::TYPE_SUBSCRIPTION
                        ? ['monthly', 'quarterly', 'yearly']
                        : ['per_exchange']
                ),
            ],
            'exchange_quota' => ['required', 'integer', 'min:1'],
            'max_text_chars' => ['nullable', 'integer', 'min:0'],
            'max_audio_seconds' => ['nullable', 'integer', 'min:0'],
            'patient_messages_per_exchange' => ['nullable', 'integer', 'min:0'],
            'psychologist_messages_per_exchange' => ['nullable', 'integer', 'min:0'],
            'response_time_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'channels' => ['nullable', 'array'],
            'channels.*' => ['string', Rule::in([Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION, Plan::CHANNEL_AUDIO])],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'string', 'max:255'],
            // 'is_active' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Plan::STATUS_DRAFT, Plan::STATUS_ACTIVE, Plan::STATUS_ARCHIVED])],
            'is_recommended' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
        ], $localeRules));

        $translations = [];
        foreach ($data['translations'] as $locale => $row) {
            $features = collect(preg_split('/\r\n|\r|\n/', $row['features_text'] ?? ''))
                ->map(fn (string $line) => trim($line))
                ->filter()
                ->values()
                ->all();

            $translations[$locale] = [
                'name' => $row['name'],
                'motto' => $row['motto'] ?? null,
                'description' => $row['description'] ?? null,
                'response_time_label' => $row['response_time_label'] ?? null,
                'features' => $features ?: null,
            ];
        }

        $primaryLocale = Locales::codes()[0] ?? 'fr_CH';
        $primary = $translations[$primaryLocale] ?? reset($translations) ?: [];
        $channels = array_values(array_unique($data['channels'] ?? []));

        $planData = [
            // Fallback columns stay in the current admin locale / primary tab.
            'name' => $primary['name'] ?? '',
            'slug' => $data['slug'] ?? null,
            'plan_type' => $data['plan_type'],
            'description' => $primary['description'] ?? null,
            'motto' => $primary['motto'] ?? null,
            'price' => (int) round(((float) $data['price_amount']) * 100),
            'currency' => strtoupper($data['currency']),
            'billing_interval' => $data['billing_interval'],
            'exchange_quota' => $data['exchange_quota'],
            'max_text_chars' => $this->nullableInt($data['max_text_chars'] ?? null),
            'max_audio_seconds' => $this->nullableInt($data['max_audio_seconds'] ?? null),
            'patient_messages_per_exchange' => $this->nullableInt($data['patient_messages_per_exchange'] ?? null),
            'psychologist_messages_per_exchange' => $this->nullableInt($data['psychologist_messages_per_exchange'] ?? null),
            'response_time_hours' => $data['response_time_hours'] ?? null,
            'response_time_label' => $primary['response_time_label'] ?? null,
            'channels' => $channels ?: null,
            'features' => $primary['features'] ?? null,
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
            'plan_id' => $data['plan_id'] ?? null,
            'status' => $data['status'],
            // Keep is_active in sync so existing catalogue queries still work.
            // 'is_active' => $request->boolean('is_active', false),
            'is_active' => $data['status'] === Plan::STATUS_ACTIVE,
            'is_recommended' => $request->boolean('is_recommended', false),
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        // Price, currency and billing stay locked after create (Stripe prices are immutable).
        if ($plan) {
            $planData['price'] = $plan->price;
            $planData['currency'] = $plan->currency;
            $planData['billing_interval'] = $plan->billing_interval;
            $planData['stripe_price_id'] = $plan->stripe_price_id;
            $planData['plan_id'] = $plan->plan_id;
        }

        return [$planData, $translations];
    }

    protected function requestedStatus(Request $request): ?string
    {
        $status = (string) $request->input('status', '');

        // Legacy filter value from before draft/archived.
        if ($status === 'inactive') {
            $status = Plan::STATUS_DRAFT;
        }

        return in_array($status, [Plan::STATUS_DRAFT, Plan::STATUS_ACTIVE, Plan::STATUS_ARCHIVED], true)
            ? $status
            : null;
    }

    protected function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}