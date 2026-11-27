@extends('backend.layouts.app')

@section('title', __('backend.patients.title'))
@section('page_title', __('backend.patients.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.patients') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-person-hearts"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.patients.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($patients->firstItem() ?? 0), 'last' => number_format($patients->lastItem() ?? 0), 'total' => number_format($patients->total())]) }}</p>
        </div>
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="adm__card adm__filters" aria-labelledby="patients-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="patients-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if (request()->filled('q'))
          <a class="adm__clear" href="{{ route('admin.patients.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.patients.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="adm__card adm__results" aria-labelledby="patients-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="patients-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.patients.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.email') }}</th>
              <th>{{ __('backend.phone') }}</th>
              <th>{{ __('backend.patients.psychologist') }}</th>
              <th>{{ __('backend.patients.location') }}</th>
              <th>{{ __('backend.status') }}</th>
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
                <td>{{ $patient->user?->phone ?: '—' }}</td>
                <td>{{ $patient->psychologist?->user?->name ?: ($patient->psychologist?->name ?? '—') }}</td>
                <td>{{ implode(', ', array_filter([$patient->city, $patient->country])) ?: '—' }}</td>
                <td>
                  @if ($patient->user?->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--secondary">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.patients.show', $patient) }}" class="adm__btn adm__btn--ghost">{{ __('backend.patients.view_details') }}</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="adm__empty">
                  <span class="adm__empty-icon" aria-hidden="true"><i class="bi bi-person-x"></i></span>
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
