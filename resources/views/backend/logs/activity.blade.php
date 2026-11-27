@extends('backend.layouts.app')

@section('title', __('backend.logs.activity_title'))
@section('page_title', __('backend.logs.activity_title'))
@section('breadcrumb')
  <li class="breadcrumb-item">{{ __('backend.menu.logs') }}</li>
  <li class="breadcrumb-item active">{{ __('backend.logs.activity') }}</li>
@endsection

@push('styles')
  <style>
    .activity-log {
      --activity-accent: #3567d6;
      --activity-ink: #17243b;
      --activity-muted: #68778d;
      --activity-line: #e8edf4;
    }

    .activity-log__hero {
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

    .activity-log__hero::after {
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

    .activity-log__heading {
      display: flex;
      align-items: center;
      gap: 1rem;
      min-width: 0;
    }

    .activity-log__hero-icon {
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

    .activity-log__title {
      margin: 0 0 .25rem;
      color: var(--activity-ink);
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .activity-log__intro {
      margin: 0;
      color: var(--activity-muted);
      font-size: .9rem;
    }

    .activity-log__total {
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

    .activity-log__filters,
    .activity-log__results {
      overflow: hidden;
      border: 1px solid var(--activity-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
    }

    .activity-log__filters {
      margin-top: 1.15rem;
      padding: 1.1rem 1.25rem 1.2rem;
    }

    .activity-log__filters-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .85rem;
    }

    .activity-log__filters-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--activity-ink);
      font-size: .95rem;
      font-weight: 650;
    }

    .activity-log__filters-title i {
      color: var(--activity-accent);
    }

    .activity-log__clear {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      color: #65748b;
      font-size: .825rem;
      font-weight: 600;
      text-decoration: none;
    }

    .activity-log__clear:hover {
      color: var(--activity-accent);
    }

    .activity-log__filter-form {
      display: grid;
      grid-template-columns: minmax(240px, 1.5fr) minmax(180px, 1fr) minmax(150px, .75fr) auto;
      gap: .65rem;
      align-items: center;
    }

    .activity-log__filter-form .form-control {
      min-height: 2.6rem;
      border-color: #e0e6ef;
      border-radius: .65rem;
      box-shadow: none;
    }

    .activity-log__filter-form .form-control:focus {
      border-color: #85a8f3;
      box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1);
    }

    .activity-log__search {
      display: inline-flex;
      min-height: 2.6rem;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      padding-inline: 1rem;
      border: 0;
      border-radius: .65rem;
      background: var(--activity-accent);
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
      color: #fff;
      font-size: .875rem;
      font-weight: 600;
    }

    .activity-log__search:hover {
      background: #2855bd;
      color: #fff;
    }

    .activity-log__results {
      margin-top: 1.15rem;
    }

    .activity-log__results-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--activity-line);
    }

    .activity-log__results-title {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin: 0;
      color: var(--activity-ink);
      font-size: .98rem;
      font-weight: 700;
    }

    .activity-log__results-title i {
      color: var(--activity-accent);
    }

    .activity-log__range {
      color: var(--activity-muted);
      font-size: .82rem;
    }

    .activity-log__table-wrap {
      overflow-x: auto;
    }

    .activity-log__table {
      min-width: 900px;
      margin: 0;
      vertical-align: middle;
    }

    .activity-log__table thead th {
      padding: .8rem 1rem;
      border-bottom: 1px solid var(--activity-line);
      background: #f8faff;
      color: #77859a;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .07em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .activity-log__table tbody td {
      padding: .9rem 1rem;
      border-color: #eef1f6;
      color: #344258;
      font-size: .84rem;
    }

    .activity-log__table tbody tr {
      transition: background-color .15s ease;
    }

    .activity-log__table tbody tr:hover {
      background: #f8faff;
    }

    .activity-log__id {
      color: #91a0b5 !important;
      font-variant-numeric: tabular-nums;
    }

    .activity-log__user {
      display: flex;
      align-items: center;
      gap: .65rem;
      min-width: 155px;
    }

    .activity-log__avatar {
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

    .activity-log__user-name {
      display: block;
      color: #28364d;
      font-weight: 650;
    }

    .activity-log__user-email {
      display: block;
      margin-top: .1rem;
      color: #8a97aa;
      font-size: .75rem;
    }

    .activity-log__action {
      display: inline-flex;
      max-width: 220px;
      padding: .33rem .55rem;
      overflow: hidden;
      border: 1px solid #e1e9fa;
      border-radius: .5rem;
      background: #f4f7ff;
      color: #3e5f9e;
      font-size: .75rem;
      font-weight: 650;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .activity-log__subject {
      display: inline-block;
      padding: .32rem .5rem;
      border: 1px solid #e8ecf2;
      border-radius: .45rem;
      background: #fafbfc;
      color: #5f6e82;
      font-size: .75rem;
      white-space: nowrap;
    }

    .activity-log__description {
      max-width: 260px;
      color: #64738a !important;
      line-height: 1.45;
    }

    .activity-log__ip {
      color: #65748b;
      font-size: .76rem;
      white-space: nowrap;
    }

    .activity-log__date {
      min-width: 130px;
      color: #53637a !important;
      white-space: nowrap;
    }

    .activity-log__date time {
      display: block;
      color: #8794a7;
      font-size: .74rem;
    }

    .activity-log__empty {
      padding: 3.5rem 1rem !important;
      text-align: center;
    }

    .activity-log__empty-icon {
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

    .activity-log__empty strong {
      display: block;
      margin-bottom: .25rem;
      color: var(--activity-ink);
      font-size: .95rem;
    }

    .activity-log__empty span {
      color: var(--activity-muted);
      font-size: .83rem;
    }

    .activity-log__pagination {
      padding: .9rem 1.25rem;
      border-top: 1px solid var(--activity-line);
    }

    @media (max-width: 767.98px) {
      .activity-log__hero {
        align-items: flex-start;
        padding: 1.1rem;
      }

      .activity-log__hero-icon {
        width: 2.8rem;
        height: 2.8rem;
        flex-basis: 2.8rem;
      }

      .activity-log__title {
        font-size: 1.05rem;
      }

      .activity-log__total {
        padding: .5rem .65rem;
        font-size: .75rem;
      }

      .activity-log__filters {
        padding: 1rem;
      }

      .activity-log__filter-form {
        grid-template-columns: 1fr;
      }

      .activity-log__search {
        width: 100%;
      }

      .activity-log__results-head {
        align-items: flex-start;
        flex-direction: column;
        gap: .25rem;
        padding: .9rem 1rem;
      }
    }

    [data-bs-theme="dark"] .activity-log__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940);
    }

    [data-bs-theme="dark"] .activity-log__title,
    [data-bs-theme="dark"] .activity-log__filters-title,
    [data-bs-theme="dark"] .activity-log__results-title,
    [data-bs-theme="dark"] .activity-log__empty strong,
    [data-bs-theme="dark"] .activity-log__user-name {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .activity-log__total,
    [data-bs-theme="dark"] .activity-log__filters,
    [data-bs-theme="dark"] .activity-log__results {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .activity-log__total {
      color: #a9c1ff;
    }

    [data-bs-theme="dark"] .activity-log__filter-form .form-control {
      border-color: #334258;
      background: #111a29;
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .activity-log__table thead th {
      border-color: #29374d;
      background: #202d40;
      color: #a5b2c5;
    }

    [data-bs-theme="dark"] .activity-log__table tbody td {
      border-color: #29374d;
      color: #c4cfdf;
    }

    [data-bs-theme="dark"] .activity-log__table tbody tr:hover {
      background: #202d40;
    }

    [data-bs-theme="dark"] .activity-log__action {
      border-color: #354765;
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .activity-log__subject,
    [data-bs-theme="dark"] .activity-log__empty-icon {
      border-color: #344258;
      background: #202d40;
      color: #b5c2d5;
    }
  </style>
@endpush

@section('content')
  <div class="activity-log">
    <section class="activity-log__hero">
      <div class="activity-log__heading">
        <span class="activity-log__hero-icon" aria-hidden="true"><i class="bi bi-activity"></i></span>
        <div>
          <h1 class="activity-log__title">{{ __('backend.logs.activity_title') }}</h1>
          <p class="activity-log__intro">{{ __('backend.logs.activity_intro') }}</p>
        </div>
      </div>
      <div class="activity-log__total">
        <i class="bi bi-journal-check" aria-hidden="true"></i>
        {{ trans_choice('backend.logs.record_count', $logs->total(), ['count' => number_format($logs->total())]) }}
      </div>
    </section>

    <section class="activity-log__filters" aria-labelledby="activity-filter-title">
      <div class="activity-log__filters-head">
        <h2 class="activity-log__filters-title" id="activity-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.logs.filter_activity') }}
        </h2>
        @if (request()->filled('q') || request()->filled('action') || request()->filled('user_id'))
          <a class="activity-log__clear" href="{{ route('admin.logs.activity') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="activity-log__filter-form">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.logs.search_activity') }}" aria-label="{{ __('backend.logs.search_activity') }}">
        <input type="text" name="action" value="{{ request('action') }}" class="form-control"
          placeholder="{{ __('backend.logs.action') }}" aria-label="{{ __('backend.logs.action') }}">
        <input type="number" name="user_id" value="{{ request('user_id') }}" class="form-control"
          placeholder="{{ __('backend.logs.user_id') }}" aria-label="{{ __('backend.logs.user_id') }}" min="1">
        <button class="btn activity-log__search" type="submit">
          <i class="bi bi-search" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </button>
      </form>
    </section>

    <section class="activity-log__results" aria-labelledby="activity-results-title">
      <div class="activity-log__results-head">
        <h2 class="activity-log__results-title" id="activity-results-title">
          <i class="bi bi-clock-history" aria-hidden="true"></i>
          {{ __('backend.logs.recent_activity') }}
        </h2>
        <span class="activity-log__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($logs->firstItem() ?? 0),
            'last' => number_format($logs->lastItem() ?? 0),
            'total' => number_format($logs->total()),
          ]) }}
        </span>
      </div>

      <div class="activity-log__table-wrap">
        <table class="table activity-log__table">
          <thead>
            <tr>
              <th>{{ __('backend.logs.id') }}</th>
              <th>{{ __('backend.logs.user') }}</th>
              <th>{{ __('backend.logs.action') }}</th>
              <th>{{ __('backend.logs.subject') }}</th>
              <th>{{ __('backend.logs.description') }}</th>
              <th>{{ __('backend.logs.ip') }}</th>
              <th>{{ __('backend.logs.date') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($logs as $log)
              @php
                $userName = $log->user?->name;
                $initials = $userName
                  ? collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('')
                  : '—';
              @endphp
              <tr>
                <td class="activity-log__id">#{{ $log->id }}</td>
                <td>
                  @if ($log->user)
                    <div class="activity-log__user">
                      <span class="activity-log__avatar" aria-hidden="true">{{ $initials }}</span>
                      <span>
                        <span class="activity-log__user-name">{{ $log->user->name }}</span>
                        <span class="activity-log__user-email">{{ $log->user->email }}</span>
                      </span>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td><span class="activity-log__action" title="{{ $log->action }}">{{ $log->action }}</span></td>
                <td>
                  @if ($log->subject_type)
                    <span class="activity-log__subject">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="activity-log__description">{{ $log->description ?: '—' }}</td>
                <td><code class="activity-log__ip">{{ $log->ip_address ?: '—' }}</code></td>
                <td class="activity-log__date">
                  @if ($log->created_at)
                    <time datetime="{{ $log->created_at->toIso8601String() }}">{{ $log->created_at->format('d M Y') }}</time>
                    {{ $log->created_at->format('H:i') }}
                  @else
                    —
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="activity-log__empty">
                  <span class="activity-log__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  <strong>{{ __('backend.logs.no_activity_found') }}</strong>
                  <span>{{ __('backend.logs.no_activity_hint') }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($logs->hasPages())
        <div class="activity-log__pagination">{{ $logs->links() }}</div>
      @endif
    </section>
  </div>
@endsection
