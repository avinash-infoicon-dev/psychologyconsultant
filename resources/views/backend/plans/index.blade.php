@extends('backend.layouts.app')

@section('title', __('backend.plans.title'))
@section('page_title', __('backend.plans.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.plans') }}</li>
@endsection

@push('styles')
  <style>
    .admin-plans {
      --ap-accent: #3567d6;
      --ap-ink: #17243b;
      --ap-muted: #68778d;
      --ap-line: #e8edf4;
    }

    /* ---------- Hero ---------- */
    .admin-plans__hero {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      overflow: hidden;
      padding: 1.5rem 1.75rem;
      border: 1px solid #dfe8fb;
      border-radius: 1rem;
      background: linear-gradient(115deg, #f3f7ff 0%, #fff 58%, #f5f8ff 100%);
      box-shadow: 0 8px 26px rgba(34, 65, 120, .06);
    }

    .admin-plans__hero::after {
      position: absolute;
      top: -5.5rem;
      right: 7rem;
      width: 14rem;
      height: 14rem;
      border: 1.5rem solid rgba(53, 103, 214, .045);
      border-radius: 50%;
      content: "";
      pointer-events: none;
    }

    .admin-plans__heading {
      display: flex;
      align-items: center;
      gap: 1rem;
      min-width: 0;
    }

    .admin-plans__hero-icon {
      display: grid;
      width: 3.25rem;
      height: 3.25rem;
      flex: 0 0 3.25rem;
      place-items: center;
      border-radius: .95rem;
      background: linear-gradient(145deg, #477be7, #2855bd);
      box-shadow: 0 8px 18px rgba(53, 103, 214, .23);
      color: #fff;
      font-size: 1.35rem;
    }

    .admin-plans__title {
      margin: 0;
      color: var(--ap-ink);
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .admin-plans__actions {
      position: relative;
      z-index: 1;
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .admin-plans__btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      min-height: 2.5rem;
      padding-inline: 1rem;
      border: 1px solid transparent;
      border-radius: .65rem;
      font-size: .85rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      white-space: nowrap;
    }

    .admin-plans__btn--primary {
      background: var(--ap-accent);
      color: #fff;
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
    }

    .admin-plans__btn--primary:hover { background: #2855bd; color: #fff; }

    .admin-plans__btn--ghost {
      border-color: #dce6fa;
      background: rgba(255, 255, 255, .86);
      color: #315ebd;
    }

    .admin-plans__btn--ghost:hover { background: #eaf0fe; color: #2855bd; }

    .admin-plans__btn--subtle {
      border-color: var(--ap-line);
      background: #fff;
      color: #5f6e82;
    }

    .admin-plans__btn--subtle:hover { background: #f4f7ff; color: #315ebd; }

    /* ---------- Filters ---------- */
    .admin-plans__filters,
    .admin-plans__results {
      overflow: hidden;
      border: 1px solid var(--ap-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
    }

    .admin-plans__filters {
      margin-top: 1.15rem;
      padding: 1.1rem 1.25rem 1.2rem;
    }

    .admin-plans__filters-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .85rem;
    }

    .admin-plans__filters-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--ap-ink);
      font-size: .95rem;
      font-weight: 650;
    }

    .admin-plans__filters-title i { color: var(--ap-accent); }

    .admin-plans__clear {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      color: #65748b;
      font-size: .825rem;
      font-weight: 600;
      text-decoration: none;
    }

    .admin-plans__clear:hover { color: var(--ap-accent); }

    .admin-plans__filter-form {
      display: grid;
      grid-template-columns: minmax(200px, 1.4fr) minmax(160px, .8fr) auto;
      gap: .65rem;
      align-items: center;
    }

    .admin-plans__filter-form .form-control,
    .admin-plans__filter-form .form-select {
      min-height: 2.6rem;
      border-color: #e0e6ef;
      border-radius: .65rem;
      box-shadow: none;
    }

    .admin-plans__filter-form .form-control:focus,
    .admin-plans__filter-form .form-select:focus {
      border-color: #85a8f3;
      box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1);
    }

    .admin-plans__search {
      display: inline-flex;
      min-height: 2.6rem;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      padding-inline: 1rem;
      border: 0;
      border-radius: .65rem;
      background: var(--ap-accent);
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
      color: #fff;
      font-size: .875rem;
      font-weight: 600;
    }

    .admin-plans__search:hover { background: #2855bd; color: #fff; }

    /* ---------- Results ---------- */
    .admin-plans__results { margin-top: 1.15rem; }

    .admin-plans__results-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--ap-line);
    }

    .admin-plans__results-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--ap-ink);
      font-size: .98rem;
      font-weight: 700;
    }

    .admin-plans__results-title i { color: var(--ap-accent); }

    .admin-plans__range { color: var(--ap-muted); font-size: .82rem; }

    .admin-plans__table-wrap { overflow-x: auto; }

    .admin-plans__table {
      min-width: 1200px;
      margin: 0;
      vertical-align: top;
    }

    .admin-plans__table thead th {
      padding: .8rem 1rem;
      border-bottom: 1px solid var(--ap-line);
      background: #f8faff;
      color: #77859a;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .admin-plans__table tbody td {
      padding: .9rem 1rem;
      border-color: #eef1f6;
      color: #344258;
      font-size: .84rem;
    }

    .admin-plans__table tbody tr { transition: background-color .15s ease; }
    .admin-plans__table tbody tr:hover { background: #f8faff; }

    .admin-plans__name { display: block; color: #28364d; font-weight: 650; }

    .admin-plans__slug {
      display: inline-block;
      margin-top: .2rem;
      padding: .12rem .4rem;
      border-radius: .35rem;
      background: #f3f5f9;
      color: #7a8699;
      font-size: .72rem;
    }

    .admin-plans__num { font-variant-numeric: tabular-nums; }

    .admin-plans__muted { color: #a3adbd; }

    .admin-plans__features {
      max-width: 200px;
      color: #64738a;
      font-size: .78rem;
      line-height: 1.45;
    }

    /* ---------- Badges / chips ---------- */
    .admin-plans__type {
      display: inline-flex;
      padding: .25rem .5rem;
      border-radius: .45rem;
      background: #eef2fb;
      color: #3e5f9e;
      font-size: .72rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .admin-plans__chip {
      display: inline-flex;
      align-items: center;
      padding: .22rem .5rem;
      margin: 0 .25rem .25rem 0;
      border: 1px solid #e1e9fa;
      border-radius: .4rem;
      background: #f4f7ff;
      color: #3e5f9e;
      font-size: .72rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .admin-plans__recommended {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      margin-left: .35rem;
      padding: .18rem .5rem;
      border-radius: 999px;
      background: #eaf0fe;
      color: #2f5cc0;
      font-size: .68rem;
      font-weight: 700;
      vertical-align: middle;
    }

    /* ---------- Status pill ---------- */
    .admin-plans__status {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .3rem .6rem;
      border: 1px solid transparent;
      border-radius: 999px;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .02em;
      white-space: nowrap;
    }

    .admin-plans__status::before {
      width: .45rem;
      height: .45rem;
      border-radius: 50%;
      background: currentColor;
      content: "";
    }

    .admin-plans__status--active { border-color: #bce8d2; background: #e8f6ef; color: #16794c; }
    .admin-plans__status--draft { border-color: #ecd9a8; background: #fbf3df; color: #8a6410; }
    .admin-plans__status--archived { border-color: #e1e9fa; background: #f4f7ff; color: #5f6e82; }

    /* ---------- Price ---------- */
    .admin-plans__price { color: #28364d; font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .admin-plans__currency { color: #8a97aa; font-weight: 500; font-size: .78rem; }

    /* ---------- Actions cell ---------- */
    .admin-plans__row-actions { display: flex; flex-wrap: wrap; gap: .4rem; white-space: nowrap; }

    /* ---------- Empty ---------- */
    .admin-plans__empty { padding: 3.5rem 1rem !important; text-align: center; }
    .admin-plans__empty-icon {
      display: grid;
      width: 3.25rem;
      height: 3.25rem;
      margin: 0 auto .85rem;
      place-items: center;
      border-radius: 1rem;
      background: #f1f5fb;
      color: #8292aa;
      font-size: 1.35rem;
    }

    /* ---------- Pagination ---------- */
    .admin-plans__pagination { padding: .9rem 1.25rem; border-top: 1px solid var(--ap-line); }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
      .admin-plans__filter-form { grid-template-columns: 1fr; }
      .admin-plans__hero { align-items: flex-start; flex-direction: column; }
    }

    @media (max-width: 767.98px) {
      .admin-plans__hero { padding: 1.1rem; }
      .admin-plans__filters { padding: 1rem; }
      .admin-plans__results-head { align-items: flex-start; flex-direction: column; gap: .25rem; padding: .9rem 1rem; }
    }

    /* ---------- Dark mode ---------- */
    [data-bs-theme="dark"] .admin-plans {
      --ap-ink: #e4ebf6;
      --ap-muted: #93a2b8;
      --ap-line: #29374d;
    }

    [data-bs-theme="dark"] .admin-plans__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940);
    }

    [data-bs-theme="dark"] .admin-plans__title,
    [data-bs-theme="dark"] .admin-plans__filters-title,
    [data-bs-theme="dark"] .admin-plans__results-title,
    [data-bs-theme="dark"] .admin-plans__name,
    [data-bs-theme="dark"] .admin-plans__price {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-plans__filters,
    [data-bs-theme="dark"] .admin-plans__results,
    [data-bs-theme="dark"] .admin-plans__btn--subtle {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .admin-plans__btn--ghost {
      border-color: #354765;
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .admin-plans__filter-form .form-control,
    [data-bs-theme="dark"] .admin-plans__filter-form .form-select {
      border-color: #334258;
      background: #111a29;
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-plans__table thead th {
      border-color: #29374d;
      background: #202d40;
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .admin-plans__table tbody td {
      border-color: #29374d;
      color: #c4cfdf;
    }

    [data-bs-theme="dark"] .admin-plans__table tbody tr:hover { background: #202d40; }

    [data-bs-theme="dark"] .admin-plans__slug,
    [data-bs-theme="dark"] .admin-plans__empty-icon {
      background: #202d40;
      color: #b5c2d5;
    }

    [data-bs-theme="dark"] .admin-plans__type,
    [data-bs-theme="dark"] .admin-plans__chip,
    [data-bs-theme="dark"] .admin-plans__recommended,
    [data-bs-theme="dark"] .admin-plans__status--archived {
      border-color: #354765;
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .admin-plans__status--active { border-color: #1f5138; background: #14331f; color: #5fd39b; }
    [data-bs-theme="dark"] .admin-plans__status--draft { border-color: #5a4a1e; background: #382f16; color: #e3c46a; }

    [data-bs-theme="dark"] .admin-plans__features { color: #a5b2c5; }
    [data-bs-theme="dark"] .admin-plans__btn--subtle { color: #b5c2d5; }
  </style>
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('status');
  @endphp

  <div class="admin-plans">
    {{-- ================= Hero ================= --}}
    <section class="admin-plans__hero">
      <div class="admin-plans__heading">
        <span class="admin-plans__hero-icon" aria-hidden="true"><i class="bi bi-tags"></i></span>
        <h1 class="admin-plans__title">{{ __('backend.plans.title') }}</h1>
      </div>
      <div class="admin-plans__actions">
        @permission('plans.create')
          <a href="{{ route('admin.plans.create') }}" class="admin-plans__btn admin-plans__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            {{ __('backend.plans.create') }}
          </a>
        @endpermission
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="admin-plans__filters" aria-labelledby="plans-filter-title">
      <div class="admin-plans__filters-head">
        <h2 class="admin-plans__filters-title" id="plans-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </h2>
        @if ($hasFilters)
          <a class="admin-plans__clear" href="{{ route('admin.plans.index') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="admin-plans__filter-form" action="{{ route('admin.plans.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="draft" @selected(request('status') === 'draft' || request('status') === 'inactive')>{{ __('backend.plans.statuses.draft') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.plans.statuses.active') }}</option>
          <option value="archived" @selected(request('status') === 'archived')>{{ __('backend.plans.statuses.archived') }}</option>
        </select>
        <button class="btn admin-plans__search" type="submit">
          <i class="bi bi-search" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="admin-plans__results" aria-labelledby="plans-results-title">
      <div class="admin-plans__results-head">
        <h2 class="admin-plans__results-title" id="plans-results-title">
          <i class="bi bi-list-check" aria-hidden="true"></i>
          {{ __('backend.menu.plans') }}
        </h2>
        <span class="admin-plans__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($plans->firstItem() ?? 0),
            'last' => number_format($plans->lastItem() ?? 0),
            'total' => number_format($plans->total()),
          ]) }}
        </span>
      </div>

      <div class="admin-plans__table-wrap">
        <table class="table admin-plans__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.plans.plan_type') }}</th>
              <th>{{ __('backend.plans.price') }}</th>
              <th>{{ __('backend.plans.motto') }}</th>
              <th>{{ __('backend.plans.billing_interval') }}</th>
              <th>{{ __('backend.plans.exchange_quota') }}</th>
              <th>{{ __('backend.plans.max_text_chars') }}</th>
              <th>{{ __('backend.plans.max_audio_seconds') }}</th>
              <th>{{ __('backend.plans.response_time') }}</th>
              <th>{{ __('backend.plans.features') }}</th>
              <th>{{ __('backend.plans.channels') }}</th>
              <th>{{ __('backend.plans.subscriptions_count') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($plans as $plan)
              @php($planStatus = $plan->status ?: ($plan->is_active ? 'active' : 'draft'))
              <tr>
                <td>
                  <span class="admin-plans__name">
                    {{ $plan->name }}
                    @if ($plan->is_recommended)
                      <span class="admin-plans__recommended">
                        <i class="bi bi-star-fill" aria-hidden="true"></i>{{ __('backend.plans.recommended') }}
                      </span>
                    @endif
                  </span>
                  <code class="admin-plans__slug">{{ $plan->slug }}</code>
                </td>
                <td><span class="admin-plans__type">{{ __('backend.plans.types.'.$plan->plan_type) }}</span></td>
                <td class="admin-plans__price">
                  {{ $plan->displayPrice() }} <span class="admin-plans__currency">{{ $plan->currency }}</span>
                </td>
                <td>{{ $plan->motto ?: '—' }}</td>
                <td>{{ __('backend.plans.intervals.'.$plan->billing_interval) }}</td>
                <td class="admin-plans__num">{{ number_format($plan->exchange_quota) }}</td>
                <td class="admin-plans__num">
                  @if ($plan->max_text_chars === null)
                    <span class="admin-plans__muted">—</span>
                  @elseif ((int) $plan->max_text_chars === 0)
                    {{ __('backend.plans.unlimited') }}
                  @else
                    {{ number_format($plan->max_text_chars) }}
                  @endif
                </td>
                <td class="admin-plans__num">
                  @if ($plan->max_audio_seconds === null)
                    <span class="admin-plans__muted">—</span>
                  @elseif ((int) $plan->max_audio_seconds === 0)
                    {{ __('backend.plans.unlimited') }}
                  @else
                    {{ number_format($plan->max_audio_seconds) }}
                  @endif
                </td>
                <td>{{ $plan->response_time_label ?: ($plan->response_time_hours ? '≤ '.$plan->response_time_hours.'h' : '—') }}</td>
                <td>
                  @if (is_array($plan->features) && count($plan->features))
                    <div class="admin-plans__features">{{ \Illuminate\Support\Str::limit(implode(' · ', $plan->features), 48) }}</div>
                  @else
                    <span class="admin-plans__muted">—</span>
                  @endif
                </td>
                <td>
                  @forelse (($plan->channels ?? []) as $channel)
                    <span class="admin-plans__chip">{{ __('backend.plans.channel_options.'.$channel) }}</span>
                  @empty
                    <span class="admin-plans__muted">—</span>
                  @endforelse
                </td>
                <td class="admin-plans__num">{{ $plan->subscriptions_count }}</td>
                <td>
                  @if ($planStatus === 'active')
                    <span class="admin-plans__status admin-plans__status--active">{{ __('backend.plans.statuses.active') }}</span>
                  @elseif ($planStatus === 'archived')
                    <span class="admin-plans__status admin-plans__status--archived">{{ __('backend.plans.statuses.archived') }}</span>
                  @else
                    <span class="admin-plans__status admin-plans__status--draft">{{ __('backend.plans.statuses.draft') }}</span>
                  @endif
                </td>
                <td>
                  <div class="admin-plans__row-actions">
                    @permission('plans.edit')
                      <a href="{{ route('admin.plans.edit', $plan) }}" class="admin-plans__btn admin-plans__btn--ghost">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>{{ __('backend.edit') }}
                      </a>
                    @endpermission
                    @permission('plans.delete')
                      @unless ($planStatus === 'archived')
                        <form action="{{ route('admin.plans.archive', $plan) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.plans.confirm_archive')))">
                          @csrf
                          <button type="submit" class="admin-plans__btn admin-plans__btn--subtle">
                            <i class="bi bi-archive" aria-hidden="true"></i>{{ __('backend.plans.archive') }}
                          </button>
                        </form>
                      @endunless
                    @endpermission
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="14" class="admin-plans__empty">
                  <span class="admin-plans__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  {{ request()->filled('q') ? __('backend.empty_filtered') : __('backend.no_records') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($plans->hasPages())
        <div class="admin-plans__pagination">{{ $plans->links() }}</div>
      @endif
    </section>
  </div>
@endsection
