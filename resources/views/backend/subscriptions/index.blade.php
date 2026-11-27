@extends('backend.layouts.app')

@section('title', __('backend.subscriptions.title'))
@section('page_title', __('backend.subscriptions.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.subscriptions') }}</li>
@endsection

@push('styles')
  <style>
    .admin-subs {
      --as-accent: #3567d6;
      --as-ink: #17243b;
      --as-muted: #68778d;
      --as-line: #e8edf4;
    }

    /* ---------- Hero ---------- */
    .admin-subs__hero {
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

    .admin-subs__hero::after {
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

    .admin-subs__heading {
      display: flex;
      align-items: center;
      gap: 1rem;
      min-width: 0;
    }

    .admin-subs__hero-icon {
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

    .admin-subs__title {
      margin: 0;
      color: var(--as-ink);
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .admin-subs__total {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: .55rem;
      padding: .65rem .9rem;
      border: 1px solid #dce6fa;
      border-radius: .75rem;
      background: rgba(255, 255, 255, .86);
      color: #315ebd;
      font-size: .875rem;
      font-weight: 600;
      white-space: nowrap;
    }

    /* ---------- Filters ---------- */
    .admin-subs__filters,
    .admin-subs__results {
      overflow: hidden;
      border: 1px solid var(--as-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
    }

    .admin-subs__filters {
      margin-top: 1.15rem;
      padding: 1.1rem 1.25rem 1.2rem;
    }

    .admin-subs__filters-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .85rem;
    }

    .admin-subs__filters-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--as-ink);
      font-size: .95rem;
      font-weight: 650;
    }

    .admin-subs__filters-title i { color: var(--as-accent); }

    .admin-subs__clear {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      color: #65748b;
      font-size: .825rem;
      font-weight: 600;
      text-decoration: none;
    }

    .admin-subs__clear:hover { color: var(--as-accent); }

    .admin-subs__filter-form {
      display: grid;
      grid-template-columns: minmax(200px, 1.4fr) minmax(150px, .8fr) minmax(170px, 1fr) auto;
      gap: .65rem;
      align-items: center;
    }

    .admin-subs__filter-form .form-control,
    .admin-subs__filter-form .form-select {
      min-height: 2.6rem;
      border-color: #e0e6ef;
      border-radius: .65rem;
      box-shadow: none;
    }

    .admin-subs__filter-form .form-control:focus,
    .admin-subs__filter-form .form-select:focus {
      border-color: #85a8f3;
      box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1);
    }

    .admin-subs__search {
      display: inline-flex;
      min-height: 2.6rem;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      padding-inline: 1rem;
      border: 0;
      border-radius: .65rem;
      background: var(--as-accent);
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
      color: #fff;
      font-size: .875rem;
      font-weight: 600;
    }

    .admin-subs__search:hover { background: #2855bd; color: #fff; }

    /* ---------- Results ---------- */
    .admin-subs__results { margin-top: 1.15rem; }

    .admin-subs__results-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--as-line);
    }

    .admin-subs__results-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--as-ink);
      font-size: .98rem;
      font-weight: 700;
    }

    .admin-subs__results-title i { color: var(--as-accent); }

    .admin-subs__range { color: var(--as-muted); font-size: .82rem; }

    .admin-subs__table-wrap { overflow-x: auto; }

    .admin-subs__table {
      min-width: 900px;
      margin: 0;
      vertical-align: middle;
    }

    .admin-subs__table thead th {
      padding: .8rem 1rem;
      border-bottom: 1px solid var(--as-line);
      background: #f8faff;
      color: #77859a;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .admin-subs__table tbody td {
      padding: .9rem 1rem;
      border-color: #eef1f6;
      color: #344258;
      font-size: .84rem;
    }

    .admin-subs__table tbody tr { transition: background-color .15s ease; }
    .admin-subs__table tbody tr:hover { background: #f8faff; }

    .admin-subs__patient {
      display: flex;
      align-items: center;
      gap: .65rem;
      min-width: 160px;
    }

    .admin-subs__avatar {
      display: grid;
      width: 2.1rem;
      height: 2.1rem;
      flex: 0 0 2.1rem;
      place-items: center;
      border-radius: 50%;
      background: #edf2ff;
      color: #4166b4;
      font-size: .7rem;
      font-weight: 700;
    }

    .admin-subs__patient-name { display: block; color: #28364d; font-weight: 650; }
    .admin-subs__patient-email { display: block; margin-top: .1rem; color: #8a97aa; font-size: .75rem; }

    .admin-subs__plan-name { font-weight: 600; color: #344258; }

    /* ---------- Status pill ---------- */
    .admin-subs__status {
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

    .admin-subs__status::before {
      width: .45rem;
      height: .45rem;
      border-radius: 50%;
      background: currentColor;
      content: "";
    }

    .admin-subs__status--success { border-color: #bce8d2; background: #e8f6ef; color: #16794c; }
    .admin-subs__status--warning { border-color: #ecd9a8; background: #fbf3df; color: #8a6410; }
    .admin-subs__status--secondary { border-color: #e1e9fa; background: #f4f7ff; color: #5f6e82; }

    /* ---------- Period / progress ---------- */
    .admin-subs__period { color: #46566e; white-space: nowrap; }
    .admin-subs__period-sep { color: #b3bcc9; padding-inline: .15rem; }

    .admin-subs__usage { min-width: 130px; }

    .admin-subs__usage-text {
      display: flex;
      justify-content: space-between;
      margin-bottom: .3rem;
      font-size: .76rem;
      color: #46566e;
      font-variant-numeric: tabular-nums;
    }

    .admin-subs__bar {
      height: .4rem;
      border-radius: 999px;
      background: #eef1f6;
      overflow: hidden;
    }

    .admin-subs__bar-fill {
      height: 100%;
      border-radius: 999px;
      background: linear-gradient(90deg, #477be7, #2855bd);
    }

    .admin-subs__date { color: #53637a; white-space: nowrap; }

    .admin-subs__muted { color: #a3adbd; }

    /* ---------- Empty ---------- */
    .admin-subs__empty { padding: 3.5rem 1rem !important; text-align: center; }
    .admin-subs__empty-icon {
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
    .admin-subs__pagination { padding: .9rem 1.25rem; border-top: 1px solid var(--as-line); }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
      .admin-subs__filter-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 767.98px) {
      .admin-subs__hero { align-items: flex-start; flex-direction: column; padding: 1.1rem; }
      .admin-subs__filters { padding: 1rem; }
      .admin-subs__filter-form { grid-template-columns: 1fr; }
      .admin-subs__search { width: 100%; }
      .admin-subs__results-head { align-items: flex-start; flex-direction: column; gap: .25rem; padding: .9rem 1rem; }
    }

    /* ---------- Dark mode ---------- */
    [data-bs-theme="dark"] .admin-subs {
      --as-ink: #e4ebf6;
      --as-muted: #93a2b8;
      --as-line: #29374d;
    }

    [data-bs-theme="dark"] .admin-subs__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940);
    }

    [data-bs-theme="dark"] .admin-subs__title,
    [data-bs-theme="dark"] .admin-subs__filters-title,
    [data-bs-theme="dark"] .admin-subs__results-title,
    [data-bs-theme="dark"] .admin-subs__patient-name,
    [data-bs-theme="dark"] .admin-subs__plan-name {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-subs__total,
    [data-bs-theme="dark"] .admin-subs__filters,
    [data-bs-theme="dark"] .admin-subs__results {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .admin-subs__total { color: #a9c1ff; }

    [data-bs-theme="dark"] .admin-subs__filter-form .form-control,
    [data-bs-theme="dark"] .admin-subs__filter-form .form-select {
      border-color: #334258;
      background: #111a29;
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-subs__table thead th {
      border-color: #29374d;
      background: #202d40;
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .admin-subs__table tbody td {
      border-color: #29374d;
      color: #c4cfdf;
    }

    [data-bs-theme="dark"] .admin-subs__table tbody tr:hover { background: #202d40; }

    [data-bs-theme="dark"] .admin-subs__avatar { background: #24334d; color: #b6caff; }

    [data-bs-theme="dark"] .admin-subs__status--success { border-color: #1f5138; background: #14331f; color: #5fd39b; }
    [data-bs-theme="dark"] .admin-subs__status--warning { border-color: #5a4a1e; background: #382f16; color: #e3c46a; }
    [data-bs-theme="dark"] .admin-subs__status--secondary { border-color: #354765; background: #24334d; color: #b6caff; }

    [data-bs-theme="dark"] .admin-subs__period,
    [data-bs-theme="dark"] .admin-subs__usage-text,
    [data-bs-theme="dark"] .admin-subs__date { color: #c4cfdf; }

    [data-bs-theme="dark"] .admin-subs__bar { background: #29374d; }

    [data-bs-theme="dark"] .admin-subs__empty-icon {
      background: #202d40;
      color: #b5c2d5;
    }
  </style>
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('status') || request()->filled('plan_id');
  @endphp

  <div class="admin-subs">
    {{-- ================= Hero ================= --}}
    <section class="admin-subs__hero">
      <div class="admin-subs__heading">
        <span class="admin-subs__hero-icon" aria-hidden="true"><i class="bi bi-card-checklist"></i></span>
        <h1 class="admin-subs__title">{{ __('backend.subscriptions.title') }}</h1>
      </div>
      <div class="admin-subs__total">
        <i class="bi bi-people" aria-hidden="true"></i>
        {{ number_format($subscriptions->total()) }} {{ __('backend.subscriptions.title') }}
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="admin-subs__filters" aria-labelledby="subs-filter-title">
      <div class="admin-subs__filters-head">
        <h2 class="admin-subs__filters-title" id="subs-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </h2>
        @if ($hasFilters)
          <a class="admin-subs__clear" href="{{ route('admin.subscriptions.index') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="admin-subs__filter-form" action="{{ route('admin.subscriptions.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('backend.subscriptions.statuses.'.$status) }}</option>
          @endforeach
        </select>
        <select name="plan_id" class="form-select" aria-label="{{ __('backend.plans.title') }}">
          <option value="">{{ __('backend.plans.title') }}</option>
          @foreach ($plans as $plan)
            <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
          @endforeach
        </select>
        <button class="btn admin-subs__search" type="submit">
          <i class="bi bi-search" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="admin-subs__results" aria-labelledby="subs-results-title">
      <div class="admin-subs__results-head">
        <h2 class="admin-subs__results-title" id="subs-results-title">
          <i class="bi bi-list-check" aria-hidden="true"></i>
          {{ __('backend.subscriptions.title') }}
        </h2>
        <span class="admin-subs__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($subscriptions->firstItem() ?? 0),
            'last' => number_format($subscriptions->lastItem() ?? 0),
            'total' => number_format($subscriptions->total()),
          ]) }}
        </span>
      </div>

      <div class="admin-subs__table-wrap">
        <table class="table admin-subs__table">
          <thead>
            <tr>
              <th>{{ __('backend.subscriptions.patient') }}</th>
              <th>{{ __('backend.menu.plans') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.subscriptions.period') }}</th>
              <th>{{ __('backend.subscriptions.exchanges') }}</th>
              <th>{{ __('backend.subscriptions.started_at') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($subscriptions as $subscription)
              @php
                $statusClass = match ($subscription->status) {
                  'active', 'trialing' => 'success',
                  'past_due' => 'warning',
                  'cancelled', 'expired' => 'secondary',
                  default => 'secondary',
                };

                $patientUser = $subscription->patient?->user;
                $patientName = $patientUser?->name;
                $pInitials = $patientName
                  ? collect(explode(' ', trim($patientName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('')
                  : '';

                $used = (int) $subscription->exchanges_used;
                $remaining = (int) $subscription->exchanges_remaining;
                $totalExchanges = $used + $remaining;
                $pct = $totalExchanges > 0 ? min(100, (int) round(($used / $totalExchanges) * 100)) : 0;
              @endphp
              <tr>
                <td>
                  @if ($patientUser)
                    <div class="admin-subs__patient">
                      <span class="admin-subs__avatar" aria-hidden="true">{{ $pInitials !== '' ? $pInitials : '?' }}</span>
                      <span>
                        <span class="admin-subs__patient-name">{{ $patientUser->name }}</span>
                        <span class="admin-subs__patient-email">{{ $patientUser->email }}</span>
                      </span>
                    </div>
                  @else
                    <span class="admin-subs__muted">—</span>
                  @endif
                </td>
                <td><span class="admin-subs__plan-name">{{ $subscription->plan?->name ?? '—' }}</span></td>
                <td>
                  <span class="admin-subs__status admin-subs__status--{{ $statusClass }}">
                    {{ __('backend.subscriptions.statuses.'.$subscription->status) }}
                  </span>
                </td>
                <td class="admin-subs__period">
                  @if ($subscription->current_period_starts_at && $subscription->current_period_ends_at)
                    {{ $subscription->current_period_starts_at->format('Y-m-d') }}<span class="admin-subs__period-sep">–</span>{{ $subscription->current_period_ends_at->format('Y-m-d') }}
                  @else
                    <span class="admin-subs__muted">—</span>
                  @endif
                </td>
                <td>
                  <div class="admin-subs__usage">
                    <div class="admin-subs__usage-text">
                      <span>{{ $used }} / {{ $totalExchanges }}</span>
                      <span>{{ $pct }}%</span>
                    </div>
                    <div class="admin-subs__bar" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                      <div class="admin-subs__bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                  </div>
                </td>
                <td class="admin-subs__date">{{ $subscription->starts_at?->format('Y-m-d H:i') ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="admin-subs__empty">
                  <span class="admin-subs__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  {{ (request()->filled('q') || request()->filled('status')) ? __('backend.empty_filtered') : __('backend.no_records') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($subscriptions->hasPages())
        <div class="admin-subs__pagination">{{ $subscriptions->links() }}</div>
      @endif
    </section>
  </div>
@endsection
