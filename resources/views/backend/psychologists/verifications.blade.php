@extends('backend.layouts.app')

@section('title', __('backend.psychologists.section_verification'))
@section('page_title', __('backend.psychologists.section_verification'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.psychologists.index') }}">{{ __('backend.menu.psychologists') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.psychologists.section_verification') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('verification_status');
  @endphp

  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-patch-check"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.psychologists.section_verification') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($psychologists->firstItem() ?? 0), 'last' => number_format($psychologists->lastItem() ?? 0), 'total' => number_format($psychologists->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        <a href="{{ route('admin.psychologists.index') }}" class="adm__btn adm__btn--subtle"><i class="bi bi-people" aria-hidden="true"></i>{{ __('backend.menu.psychologists') }}</a>
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="adm__card adm__filters" aria-labelledby="verif-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="verif-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.psychologists.verifications.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.psychologists.verifications.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="verification_status" class="form-select" aria-label="{{ __('backend.psychologists.queue_statuses') }}">
          <option value="">{{ __('backend.psychologists.queue_statuses') }}</option>
          @foreach (\App\Models\Professional::VERIFICATION_STATUSES as $status)
            <option value="{{ $status }}" @selected(request('verification_status') === $status)>
              {{ __('backend.psychologists.status_'.$status) }}
            </option>
          @endforeach
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="adm__card adm__results" aria-labelledby="verif-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="verif-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.psychologists.section_verification') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.email') }}</th>
              <th>{{ __('backend.psychologists.license') }}</th>
              <th>{{ __('backend.psychologists.verification') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($psychologists as $psychologist)
              @php
                $status = $psychologist->verification_status ?: 'draft';
                $tone = match ($status) {
                  'verified' => 'success',
                  'submitted', 'under_review' => 'warning',
                  'rejected' => 'danger',
                  'suspended' => 'dark',
                  default => 'secondary',
                };
              @endphp
              <tr>
                <td>
                  <div class="adm__person">
                    <span class="adm__avatar" aria-hidden="true">{{ collect(explode(' ', trim((string) $psychologist->user?->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: '?' }}</span>
                    <span class="adm__name">{{ $psychologist->user?->name ?: '—' }}</span>
                  </div>
                </td>
                <td>{{ $psychologist->user?->email ?: '—' }}</td>
                <td><code>{{ $psychologist->license_number ?: '—' }}</code></td>
                <td><span class="adm__status adm__status--{{ $tone }}">{{ __('backend.psychologists.status_'.$status) }}</span></td>
                <td>
                  <div class="adm__row-actions">
                    <a href="{{ route('admin.psychologists.verification.show', $psychologist) }}" class="adm__btn adm__btn--primary">{{ __('backend.psychologists.section_verification') }}</a>
                    <a href="{{ route('admin.psychologists.show', $psychologist) }}" class="adm__btn adm__btn--ghost">{{ __('backend.psychologists.view_details') }}</a>
                  </div>
                </td>
              </tr>
            @empty
              @include('backend.partials.empty-row', [
                'colspan' => 5,
                'title' => request()->filled('q') || request()->filled('verification_status')
                  ? __('backend.empty_filtered')
                  : __('backend.no_records'),
              ])
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($psychologists->hasPages())
        <div class="adm__pagination">{{ $psychologists->links() }}</div>
      @endif
    </section>
  </div>
@endsection
