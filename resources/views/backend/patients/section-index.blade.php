@extends('backend.layouts.app')

@section('title', $pageTitle)
@section('page_title', $pageTitle)
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item active">{{ $pageTitle }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
        <div>
          <h1 class="adm__title">{{ $pageTitle }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($patients->firstItem() ?? 0), 'last' => number_format($patients->lastItem() ?? 0), 'total' => number_format($patients->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        <a href="{{ route('admin.patients.index') }}" class="adm__btn adm__btn--subtle"><i class="bi bi-people" aria-hidden="true"></i>{{ __('backend.menu.patients') }}</a>
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="adm__card adm__filters" aria-labelledby="patients-section-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="patients-section-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if (request()->filled('q'))
          <a class="adm__clear" href="{{ url()->current() }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ url()->current() }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="adm__card adm__results" aria-labelledby="patients-section-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="patients-section-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ $pageTitle }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.email') }}</th>
              <th>{{ __('backend.patients.psychologist') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($patients as $patient)
              <tr>
                <td>
                  <div class="adm__person">
                    <span class="adm__avatar" aria-hidden="true">{{ collect(explode(' ', trim((string) $patient->user?->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: '?' }}</span>
                    <span class="adm__name">{{ $patient->user?->name ?: '—' }}</span>
                  </div>
                </td>
                <td>{{ $patient->user?->email ?: '—' }}</td>
                <td>{{ $patient->psychologist?->user?->name ?: ($patient->psychologist?->name ?? '—') }}</td>
                <td>
                  <div class="adm__row-actions">
                    <a href="{{ route($actionRoute, $patient) }}" class="adm__btn adm__btn--primary">{{ $actionLabel }}</a>
                    <a href="{{ route('admin.patients.show', $patient) }}" class="adm__btn adm__btn--ghost">{{ __('backend.patients.view_details') }}</a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="adm__empty">
                  <span class="adm__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  <span class="adm__empty-title">{{ request()->filled('q') ? __('backend.empty_filtered') : __('backend.no_records') }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($patients->hasPages())
        <div class="adm__pagination">{{ $patients->links() }}</div>
      @endif
    </section>
  </div>
@endsection
