@extends('backend.layouts.app')

@section('title', __('backend.logs.audit_title'))
@section('page_title', __('backend.logs.audit_title'))
@section('breadcrumb')
  <li class="breadcrumb-item">{{ __('backend.menu.logs') }}</li>
  <li class="breadcrumb-item active">{{ __('backend.logs.audit') }}</li>
@endsection

@push('styles')
  <style>
    .audit-log {
      --audit-accent: #3567d6;
      --audit-ink: #17243b;
      --audit-muted: #68778d;
      --audit-line: #e8edf4;
      --audit-added: #16794c;
      --audit-added-bg: #e8f6ef;
      --audit-removed: #b42318;
      --audit-removed-bg: #fdeceb;
    }

    /* ---------- Hero ---------- */
    .audit-log__hero {
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

    .audit-log__hero::after {
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

    .audit-log__heading {
      display: flex;
      align-items: center;
      gap: 1rem;
      min-width: 0;
    }

    .audit-log__hero-icon {
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

    .audit-log__title {
      margin: 0 0 .25rem;
      color: var(--audit-ink);
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .audit-log__intro {
      margin: 0;
      color: var(--audit-muted);
      font-size: .9rem;
    }

    .audit-log__total {
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

    /* ---------- Cards ---------- */
    .audit-log__filters,
    .audit-log__results {
      overflow: hidden;
      border: 1px solid var(--audit-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
    }

    .audit-log__filters {
      margin-top: 1.15rem;
      padding: 1.1rem 1.25rem 1.2rem;
    }

    .audit-log__filters-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .85rem;
    }

    .audit-log__filters-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--audit-ink);
      font-size: .95rem;
      font-weight: 650;
    }

    .audit-log__filters-title i {
      color: var(--audit-accent);
    }

    .audit-log__clear {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      color: #65748b;
      font-size: .825rem;
      font-weight: 600;
      text-decoration: none;
    }

    .audit-log__clear:hover {
      color: var(--audit-accent);
    }

    .audit-log__filter-form {
      display: grid;
      grid-template-columns: minmax(220px, 1.5fr) minmax(160px, 1fr) minmax(140px, .6fr) minmax(160px, .8fr) auto;
      gap: .65rem;
      align-items: center;
    }

    .audit-log__filter-form .form-control,
    .audit-log__filter-form .form-select {
      min-height: 2.6rem;
      border-color: #e0e6ef;
      border-radius: .65rem;
      box-shadow: none;
    }

    .audit-log__filter-form .form-control:focus,
    .audit-log__filter-form .form-select:focus {
      border-color: #85a8f3;
      box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1);
    }

    .audit-log__search {
      display: inline-flex;
      min-height: 2.6rem;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      padding-inline: 1rem;
      border: 0;
      border-radius: .65rem;
      background: var(--audit-accent);
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
      color: #fff;
      font-size: .875rem;
      font-weight: 600;
    }

    .audit-log__search:hover {
      background: #2855bd;
      color: #fff;
    }

    /* ---------- Results ---------- */
    .audit-log__results {
      margin-top: 1.15rem;
    }

    .audit-log__results-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--audit-line);
    }

    .audit-log__results-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--audit-ink);
      font-size: .98rem;
      font-weight: 700;
    }

    .audit-log__results-title i {
      color: var(--audit-accent);
    }

    .audit-log__range {
      color: var(--audit-muted);
      font-size: .82rem;
    }

    .audit-log__table-wrap {
      overflow-x: auto;
    }

    .audit-log__table {
      min-width: 1080px;
      margin: 0;
      vertical-align: top;
    }

    .audit-log__table thead th {
      padding: .8rem 1rem;
      border-bottom: 1px solid var(--audit-line);
      background: #f8faff;
      color: #77859a;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .audit-log__table tbody td {
      padding: .9rem 1rem;
      border-color: #eef1f6;
      color: #344258;
      font-size: .84rem;
    }

    .audit-log__table tbody tr {
      transition: background-color .15s ease;
    }

    .audit-log__table tbody tr:hover {
      background: #f8faff;
    }

    .audit-log__id {
      color: #91a0b5 !important;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }

    /* ---------- User cell ---------- */
    .audit-log__user {
      display: flex;
      align-items: center;
      gap: .65rem;
      min-width: 170px;
    }

    .audit-log__avatar {
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

    .audit-log__avatar--system {
      background: #f1f3f7;
      color: #8794a7;
      font-size: .85rem;
    }

    .audit-log__user-name {
      display: block;
      color: #28364d;
      font-weight: 650;
    }

    .audit-log__user-email {
      display: block;
      margin-top: .1rem;
      color: #8a97aa;
      font-size: .75rem;
    }

    /* ---------- Event pill ---------- */
    .audit-log__event {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .3rem .6rem;
      border: 1px solid transparent;
      border-radius: 999px;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: .02em;
      text-transform: capitalize;
      white-space: nowrap;
    }

    .audit-log__event i {
      font-size: .8rem;
    }

    .audit-log__event--created {
      border-color: #bce8d2;
      background: var(--audit-added-bg);
      color: var(--audit-added);
    }

    .audit-log__event--updated {
      border-color: #cfdcfa;
      background: #eaf0fe;
      color: #2f5cc0;
    }

    .audit-log__event--deleted {
      border-color: #f3cdca;
      background: var(--audit-removed-bg);
      color: var(--audit-removed);
    }

    .audit-log__event--restored {
      border-color: #ecd9a8;
      background: #fbf3df;
      color: #8a6410;
    }

    .audit-log__event--unknown {
      border-color: #e1e9fa;
      background: #f4f7ff;
      color: #3e5f9e;
    }

    /* ---------- Record cell ---------- */
    .audit-log__record {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      min-width: 130px;
    }

    .audit-log__record-type {
      display: inline-block;
      padding: .3rem .5rem;
      border: 1px solid #e8ecf2;
      border-radius: .45rem;
      background: #fafbfc;
      color: #5f6e82;
      font-size: .75rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .audit-log__record-id {
      color: #91a0b5;
      font-size: .75rem;
      font-variant-numeric: tabular-nums;
      white-space: nowrap;
    }

    /* ---------- Changes / diff ---------- */
    .audit-log__changes {
      max-width: 360px;
    }

    .audit-log__changes > summary {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .3rem .55rem;
      border: 1px solid #e1e9fa;
      border-radius: .5rem;
      background: #f4f7ff;
      color: #3e5f9e;
      font-size: .75rem;
      font-weight: 650;
      cursor: pointer;
      list-style: none;
      user-select: none;
    }

    .audit-log__changes > summary::-webkit-details-marker {
      display: none;
    }

    .audit-log__changes > summary::marker {
      content: "";
    }

    .audit-log__changes > summary:hover {
      border-color: #c4d3f5;
      background: #eaf0fe;
    }

    .audit-log__changes[open] > summary {
      margin-bottom: .55rem;
    }

    .audit-log__changes-count {
      display: inline-grid;
      min-width: 1.15rem;
      height: 1.15rem;
      place-items: center;
      padding-inline: .3rem;
      border-radius: 999px;
      background: #dfe9ff;
      color: #2f5cc0;
      font-size: .68rem;
      font-weight: 700;
    }

    .audit-log__changes-note {
      margin: .1rem 0 .55rem;
      color: var(--audit-muted);
      font-size: .75rem;
    }

    .audit-log__diff {
      width: 100%;
      margin: 0;
      border: 1px solid var(--audit-line);
      border-radius: .6rem;
      border-collapse: separate;
      border-spacing: 0;
      font-size: .75rem;
      overflow: hidden;
    }

    .audit-log__diff th,
    .audit-log__diff td {
      padding: .45rem .55rem;
      border-bottom: 1px solid var(--audit-line);
      vertical-align: top;
      text-align: left;
    }

    .audit-log__diff tr:last-child th,
    .audit-log__diff tr:last-child td {
      border-bottom: 0;
    }

    .audit-log__diff thead th {
      background: #f8faff;
      color: #77859a;
      font-size: .66rem;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
    }

    .audit-log__diff-key {
      min-width: 90px;
      color: #344258;
      font-family: var(--bs-font-monospace, ui-monospace, SFMono-Regular, Menlo, monospace);
      font-weight: 600;
      word-break: break-word;
    }

    .audit-log__value {
      max-width: 200px;
      max-height: 150px;
      margin: 0;
      overflow: auto;
      border-radius: .4rem;
      padding: .3rem .45rem;
      background: #f8f9fc;
      color: #46566e;
      font-family: var(--bs-font-monospace, ui-monospace, SFMono-Regular, Menlo, monospace);
      font-size: .72rem;
      line-height: 1.45;
      white-space: pre-wrap;
      word-break: break-word;
    }

    .audit-log__value--old {
      background: var(--audit-removed-bg);
      color: #8f241c;
    }

    .audit-log__value--new {
      background: var(--audit-added-bg);
      color: #136c43;
    }

    .audit-log__raw {
      margin-top: .55rem;
    }

    .audit-log__raw > summary {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      color: #65748b;
      font-size: .72rem;
      font-weight: 600;
      cursor: pointer;
      list-style: none;
    }

    .audit-log__raw > summary::-webkit-details-marker { display: none; }
    .audit-log__raw > summary::marker { content: ""; }

    .audit-log__raw-block {
      margin-top: .45rem;
    }

    .audit-log__raw-label {
      display: block;
      margin-bottom: .2rem;
      color: #77859a;
      font-size: .66rem;
      font-weight: 700;
      letter-spacing: .05em;
      text-transform: uppercase;
    }

    .audit-log__raw .audit-log__value {
      max-width: none;
      max-height: 220px;
    }

    [data-bs-theme="dark"] .audit-log__raw-label {
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .audit-log__raw > summary {
      color: #93a2b8;
    }

    .audit-log__value-null {
      color: #98a3b5;
      font-style: italic;
    }

    .audit-log__value-absent {
      color: #c2cad6;
    }

    /* ---------- Meta cells ---------- */
    .audit-log__ip {
      color: #65748b;
      font-size: .76rem;
      white-space: nowrap;
    }

    .audit-log__date {
      min-width: 130px;
      color: #53637a !important;
      white-space: nowrap;
    }

    .audit-log__date time {
      display: block;
      color: inherit;
    }

    .audit-log__date-time {
      display: block;
      margin-top: .1rem;
      color: #8794a7;
      font-size: .74rem;
    }

    /* ---------- Empty ---------- */
    .audit-log__empty {
      padding: 3.5rem 1rem !important;
      text-align: center;
    }

    .audit-log__empty-icon {
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

    .audit-log__empty strong {
      display: block;
      margin-bottom: .25rem;
      color: var(--audit-ink);
      font-size: .95rem;
    }

    .audit-log__empty span {
      color: var(--audit-muted);
      font-size: .83rem;
    }

    .audit-log__muted {
      color: #a3adbd;
    }

    /* ---------- Pagination ---------- */
    .audit-log__pagination {
      padding: .9rem 1.25rem;
      border-top: 1px solid var(--audit-line);
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
      .audit-log__filter-form {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 767.98px) {
      .audit-log__hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 1.1rem;
      }

      .audit-log__hero-icon {
        width: 2.8rem;
        height: 2.8rem;
        flex-basis: 2.8rem;
      }

      .audit-log__title {
        font-size: 1.05rem;
      }

      .audit-log__total {
        padding: .5rem .65rem;
        font-size: .75rem;
      }

      .audit-log__filters {
        padding: 1rem;
      }

      .audit-log__filter-form {
        grid-template-columns: 1fr;
      }

      .audit-log__search {
        width: 100%;
      }

      .audit-log__results-head {
        align-items: flex-start;
        flex-direction: column;
        gap: .25rem;
        padding: .9rem 1rem;
      }

      .audit-log__changes {
        max-width: none;
      }
    }

    /* ---------- Dark mode ---------- */
    [data-bs-theme="dark"] .audit-log {
      --audit-ink: #e4ebf6;
      --audit-muted: #93a2b8;
      --audit-line: #29374d;
      --audit-added: #5fd39b;
      --audit-added-bg: #14331f;
      --audit-removed: #f0a098;
      --audit-removed-bg: #3a1c1a;
    }

    [data-bs-theme="dark"] .audit-log__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940);
    }

    [data-bs-theme="dark"] .audit-log__title,
    [data-bs-theme="dark"] .audit-log__filters-title,
    [data-bs-theme="dark"] .audit-log__results-title,
    [data-bs-theme="dark"] .audit-log__empty strong,
    [data-bs-theme="dark"] .audit-log__user-name {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .audit-log__total,
    [data-bs-theme="dark"] .audit-log__filters,
    [data-bs-theme="dark"] .audit-log__results {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .audit-log__total {
      color: #a9c1ff;
    }

    [data-bs-theme="dark"] .audit-log__filter-form .form-control,
    [data-bs-theme="dark"] .audit-log__filter-form .form-select {
      border-color: #334258;
      background: #111a29;
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .audit-log__table thead th {
      border-color: #29374d;
      background: #202d40;
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .audit-log__table tbody td {
      border-color: #29374d;
      color: #c4cfdf;
    }

    [data-bs-theme="dark"] .audit-log__table tbody tr:hover {
      background: #202d40;
    }

    [data-bs-theme="dark"] .audit-log__avatar {
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .audit-log__avatar--system,
    [data-bs-theme="dark"] .audit-log__empty-icon {
      background: #202d40;
      color: #b5c2d5;
    }

    [data-bs-theme="dark"] .audit-log__record-type {
      border-color: #344258;
      background: #202d40;
      color: #b5c2d5;
    }

    [data-bs-theme="dark"] .audit-log__changes > summary,
    [data-bs-theme="dark"] .audit-log__event--unknown {
      border-color: #354765;
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .audit-log__event--created {
      border-color: #1f5138;
      background: #14331f;
      color: #5fd39b;
    }

    [data-bs-theme="dark"] .audit-log__event--updated {
      border-color: #354765;
      background: #24334d;
      color: #a9c1ff;
    }

    [data-bs-theme="dark"] .audit-log__event--deleted {
      border-color: #5c2b27;
      background: #3a1c1a;
      color: #f0a098;
    }

    [data-bs-theme="dark"] .audit-log__event--restored {
      border-color: #5a4a1e;
      background: #382f16;
      color: #e3c46a;
    }

    [data-bs-theme="dark"] .audit-log__diff,
    [data-bs-theme="dark"] .audit-log__diff th,
    [data-bs-theme="dark"] .audit-log__diff td {
      border-color: #29374d;
    }

    [data-bs-theme="dark"] .audit-log__diff thead th {
      background: #202d40;
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .audit-log__diff-key {
      color: #d7e0ee;
    }

    [data-bs-theme="dark"] .audit-log__value {
      background: #111a29;
      color: #c4cfdf;
    }

    [data-bs-theme="dark"] .audit-log__value--old {
      background: var(--audit-removed-bg);
      color: #f0a098;
    }

    [data-bs-theme="dark"] .audit-log__value--new {
      background: var(--audit-added-bg);
      color: #5fd39b;
    }

    [data-bs-theme="dark"] .audit-log__changes-count {
      background: #24334d;
      color: #a9c1ff;
    }

    [data-bs-theme="dark"] .audit-log__date-time {
      color: #8794a7;
    }
  </style>
@endpush

@section('content')
  @php
    $eventMeta = [
      'created' => ['icon' => 'bi-plus-circle', 'label' => __('backend.logs.event_created')],
      'updated' => ['icon' => 'bi-pencil-square', 'label' => __('backend.logs.event_updated')],
      'deleted' => ['icon' => 'bi-trash3', 'label' => __('backend.logs.event_deleted')],
      'restored' => ['icon' => 'bi-arrow-counterclockwise', 'label' => __('backend.logs.event_restored')],
    ];

    $hasFilters = request()->filled('q')
      || request()->filled('auditable_type')
      || request()->filled('auditable_id')
      || request()->filled('event');

    // Render a stored value for display inside a diff cell.
    $formatValue = function ($value) {
      if ($value === null) {
        return null;
      }
      if (is_bool($value)) {
        return $value ? 'true' : 'false';
      }
      if (is_int($value) || is_float($value)) {
        return (string) $value;
      }
      if (is_string($value)) {
        return $value;
      }

      return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    };
  @endphp

  <div class="audit-log">
    {{-- ================= Hero ================= --}}
    <section class="audit-log__hero">
      <div class="audit-log__heading">
        <span class="audit-log__hero-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
        <div>
          <h1 class="audit-log__title">{{ __('backend.logs.audit_title') }}</h1>
          <p class="audit-log__intro">{{ __('backend.logs.audit_intro') }}</p>
        </div>
      </div>
      <div class="audit-log__total">
        <i class="bi bi-journal-check" aria-hidden="true"></i>
        {{ trans_choice('backend.logs.record_count', $logs->total(), ['count' => number_format($logs->total())]) }}
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="audit-log__filters" aria-labelledby="audit-filter-title">
      <div class="audit-log__filters-head">
        <h2 class="audit-log__filters-title" id="audit-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.logs.filter_audit') }}
        </h2>
        @if ($hasFilters)
          <a class="audit-log__clear" href="{{ route('admin.logs.audit') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="audit-log__filter-form" action="{{ route('admin.logs.audit') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.logs.search_audit') }}" aria-label="{{ __('backend.logs.search_audit') }}">
        <input type="text" name="auditable_type" value="{{ request('auditable_type') }}" class="form-control"
          placeholder="{{ __('backend.logs.auditable_type') }}" aria-label="{{ __('backend.logs.auditable_type') }}">
        <input type="number" name="auditable_id" value="{{ request('auditable_id') }}" class="form-control"
          placeholder="{{ __('backend.logs.auditable_id') }}" aria-label="{{ __('backend.logs.auditable_id') }}" min="1">
        <select name="event" class="form-select" aria-label="{{ __('backend.logs.event') }}">
          <option value="">{{ __('backend.logs.all_events') }}</option>
          @foreach (['created', 'updated', 'deleted', 'restored'] as $eventValue)
            <option value="{{ $eventValue }}" @selected(request('event') === $eventValue)>
              {{ $eventMeta[$eventValue]['label'] }}
            </option>
          @endforeach
        </select>
        <button class="btn audit-log__search" type="submit">
          <i class="bi bi-search" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="audit-log__results" aria-labelledby="audit-results-title">
      <div class="audit-log__results-head">
        <h2 class="audit-log__results-title" id="audit-results-title">
          <i class="bi bi-list-check" aria-hidden="true"></i>
          {{ __('backend.logs.recent_changes') }}
        </h2>
        <span class="audit-log__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($logs->firstItem() ?? 0),
            'last' => number_format($logs->lastItem() ?? 0),
            'total' => number_format($logs->total()),
          ]) }}
        </span>
      </div>

      <div class="audit-log__table-wrap">
        <table class="table audit-log__table">
          <thead>
            <tr>
              <th>{{ __('backend.logs.id') }}</th>
              <th>{{ __('backend.logs.user') }}</th>
              <th>{{ __('backend.logs.event') }}</th>
              <th>{{ __('backend.logs.auditable') }}</th>
              <th>{{ __('backend.logs.changes') }}</th>
              <th>{{ __('backend.logs.ip') }}</th>
              <th>{{ __('backend.logs.date') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($logs as $log)
              @php
                $event = $log->event;
                $meta = $eventMeta[$event] ?? ['icon' => 'bi-dot', 'label' => $event];

                // Build per-field diff rows.
                $old = is_array($log->old_values) ? $log->old_values : [];
                $new = is_array($log->new_values) ? $log->new_values : [];

                if ($event === 'updated') {
                  // `new_values` holds only the changed attributes.
                  $keys = collect(array_keys($new));
                } elseif ($event === 'created') {
                  $keys = collect(array_keys($new));
                } elseif ($event === 'deleted') {
                  $keys = collect(array_keys($old));
                } else {
                  $keys = collect(array_keys($old))->merge(array_keys($new));
                }

                $keys = $keys->map(fn ($key) => (string) $key)->unique()->sort(SORT_NATURAL)->values();

                $rows = $keys->map(function ($key) use ($old, $new, $event, $formatValue) {
                  $hasOld = array_key_exists($key, $old);
                  $hasNew = array_key_exists($key, $new);

                  // Drop no-op rows where nothing actually differs.
                  if ($hasOld && $hasNew && json_encode($old[$key]) === json_encode($new[$key])) {
                    return null;
                  }

                  return [
                    'key' => $key,
                    'has_old' => $hasOld,
                    'has_new' => $hasNew,
                    'old' => $formatValue($old[$key] ?? null),
                    'new' => $formatValue($new[$key] ?? null),
                  ];
                })->filter()->values();

                // User avatar initials.
                $userName = $log->user?->name;
                $initials = $userName
                  ? collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('')
                  : '';
              @endphp

              <tr>
                <td class="audit-log__id">#{{ $log->id }}</td>

                {{-- User --}}
                <td>
                  @if ($log->user)
                    <div class="audit-log__user">
                      <span class="audit-log__avatar" aria-hidden="true">{{ $initials !== '' ? $initials : '?' }}</span>
                      <span>
                        <span class="audit-log__user-name">{{ $log->user->name }}</span>
                        <span class="audit-log__user-email">{{ $log->user->email }}</span>
                      </span>
                    </div>
                  @else
                    <div class="audit-log__user">
                      <span class="audit-log__avatar audit-log__avatar--system" aria-hidden="true"><i class="bi bi-gear"></i></span>
                      <span class="audit-log__user-name">{{ __('backend.logs.system') }}</span>
                    </div>
                  @endif
                </td>

                {{-- Event --}}
                <td>
                  <span class="audit-log__event audit-log__event--{{ in_array($event, ['created', 'updated', 'deleted', 'restored'], true) ? $event : 'unknown' }}">
                    <i class="bi {{ $meta['icon'] }}" aria-hidden="true"></i>
                    {{ $meta['label'] }}
                  </span>
                </td>

                {{-- Auditable record --}}
                <td>
                  <span class="audit-log__record">
                    <span class="audit-log__record-type">{{ class_basename($log->auditable_type) }}</span>
                    <span class="audit-log__record-id">#{{ $log->auditable_id }}</span>
                  </span>
                </td>

                {{-- Changes --}}
                <td>
                  @if ($log->old_values || $log->new_values)
                    <details class="audit-log__changes">
                      <summary>
                        <i class="bi bi-plus-slash-minus" aria-hidden="true"></i>
                        {{ __('backend.logs.view_details') }}
                        @if ($rows->isNotEmpty())
                          <span class="audit-log__changes-count" title="{{ trans_choice('backend.logs.field_count', $rows->count(), ['count' => $rows->count()]) }}">{{ $rows->count() }}</span>
                        @endif
                      </summary>

                      @if ($rows->isNotEmpty())
                        <table class="audit-log__diff">
                          <thead>
                            <tr>
                              <th>{{ __('backend.logs.field') }}</th>
                              <th>{{ __('backend.logs.old_values') }}</th>
                              <th>{{ __('backend.logs.new_values') }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($rows as $row)
                              <tr>
                                <td class="audit-log__diff-key">{{ $row['key'] }}</td>
                                <td>
                                  @if (! $row['has_old'])
                                    <span class="audit-log__value-absent" title="{{ __('backend.logs.field_absent') }}">—</span>
                                  @elseif ($row['old'] === null)
                                    <span class="audit-log__value-null">null</span>
                                  @else
                                    <pre class="audit-log__value audit-log__value--old">{{ $row['old'] }}</pre>
                                  @endif
                                </td>
                                <td>
                                  @if (! $row['has_new'])
                                    <span class="audit-log__value-absent" title="{{ __('backend.logs.field_absent') }}">—</span>
                                  @elseif ($row['new'] === null)
                                    <span class="audit-log__value-null">null</span>
                                  @else
                                    <pre class="audit-log__value audit-log__value--new">{{ $row['new'] }}</pre>
                                  @endif
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      @else
                        <p class="audit-log__changes-note mb-0">{{ __('backend.logs.no_changes') }}</p>
                      @endif

                      @if ($old || $new)
                        <details class="audit-log__raw">
                          <summary>
                            <i class="bi bi-braces" aria-hidden="true"></i>
                            {{ __('backend.logs.raw_json') }}
                          </summary>
                          @if ($old)
                            <div class="audit-log__raw-block">
                              <span class="audit-log__raw-label">{{ __('backend.logs.old_values') }}</span>
                              <pre class="audit-log__value">{{ json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                          @endif
                          @if ($new)
                            <div class="audit-log__raw-block">
                              <span class="audit-log__raw-label">{{ __('backend.logs.new_values') }}</span>
                              <pre class="audit-log__value">{{ json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </div>
                          @endif
                        </details>
                      @endif
                    </details>
                  @else
                    <span class="audit-log__muted">—</span>
                  @endif
                </td>

                {{-- IP --}}
                <td><code class="audit-log__ip">{{ $log->ip_address ?: '—' }}</code></td>

                {{-- Date --}}
                <td class="audit-log__date">
                  @if ($log->created_at)
                    <time datetime="{{ $log->created_at->toIso8601String() }}" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                      {{ $log->created_at->format('d M Y') }}
                    </time>
                    <span class="audit-log__date-time">{{ $log->created_at->format('H:i') }}</span>
                  @else
                    <span class="audit-log__muted">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="audit-log__empty">
                  <span class="audit-log__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  <strong>{{ __('backend.logs.no_audit_found') }}</strong>
                  <span>{{ __('backend.logs.no_audit_hint') }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($logs->hasPages())
        <div class="audit-log__pagination">{{ $logs->links() }}</div>
      @endif
    </section>
  </div>
@endsection
