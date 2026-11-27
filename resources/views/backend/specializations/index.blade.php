@extends('backend.layouts.app')

@section('title', __('backend.specializations.title'))
@section('page_title', __('backend.specializations.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.specializations') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('status');
  @endphp

  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-diagram-3"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.specializations.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($specializations->firstItem() ?? 0), 'last' => number_format($specializations->lastItem() ?? 0), 'total' => number_format($specializations->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('specializations.create')
          <a href="{{ route('admin.specializations.create') }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            {{ __('backend.specializations.create') }}
          </a>
        @endpermission
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="adm__card adm__filters" aria-labelledby="spec-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="spec-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.specializations.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.specializations.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          <option value="archived" @selected(request('status') === 'archived')>{{ __('backend.specializations.archived_badge') }}</option>
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="adm__card adm__results" aria-labelledby="spec-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="spec-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.specializations.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.slug') }}</th>
              <th>{{ __('backend.description') }}</th>
              <th>{{ __('backend.specializations.professionals_count') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($specializations as $specialization)
              <tr>
                <td><span class="adm__name">{{ $specialization->name }}</span></td>
                <td><code class="adm__slug">{{ $specialization->slug }}</code></td>
                <td>{{ \Illuminate\Support\Str::limit($specialization->description, 80) ?: '—' }}</td>
                <td class="adm__num">{{ $specialization->professionals_count }}</td>
                <td>
                  @if ($specialization->isArchived())
                    <span class="adm__status adm__status--secondary">{{ __('backend.specializations.archived_badge') }}</span>
                  @elseif ($specialization->status)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('specializations.edit')
                      <a href="{{ route('admin.specializations.edit', $specialization) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                      @unless ($specialization->isArchived())
                        <form action="{{ route('admin.specializations.toggle-status', $specialization) }}" method="post" class="d-inline">
                          @csrf
                          @method('PATCH')
                          @if ($specialization->status)
                            <button type="submit" class="adm__btn adm__btn--warn">{{ __('backend.specializations.deactivate') }}</button>
                          @else
                            <button type="submit" class="adm__btn adm__btn--success">{{ __('backend.specializations.activate') }}</button>
                          @endif
                        </form>
                      @endunless
                    @endpermission
                    @permission('specializations.delete')
                      {{--
                      <form action="{{ route('admin.specializations.destroy', $specialization) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="adm__btn adm__btn--danger">{{ __('backend.delete') }}</button>
                      </form>
                      --}}
                      @unless ($specialization->isArchived())
                        <form action="{{ route('admin.specializations.archive', $specialization) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.specializations.confirm_archive')))">
                          @csrf
                          <button type="submit" class="adm__btn adm__btn--subtle">{{ __('backend.specializations.archive') }}</button>
                        </form>
                      @endunless
                    @endpermission
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="adm__empty">
                  <span class="adm__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  <span class="adm__empty-title">{{ __('backend.no_records') }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($specializations->hasPages())
        <div class="adm__pagination">{{ $specializations->links() }}</div>
      @endif
    </section>

    {{-- ================= Archive ================= --}}
    @if (! request()->filled('status'))
      <section class="adm__card adm__results adm__section" aria-labelledby="spec-archive-title">
        <div class="adm__results-head">
          <h2 class="adm__card-title" id="spec-archive-title"><i class="bi bi-archive" aria-hidden="true"></i>{{ __('backend.specializations.archive_section') }}</h2>
        </div>
        <div class="adm__table-wrap">
          <table class="table adm__table">
            <thead>
              <tr>
                <th>{{ __('backend.name') }}</th>
                <th>{{ __('backend.slug') }}</th>
                <th>{{ __('backend.specializations.professionals_count') }}</th>
                <th>{{ __('backend.status') }}</th>
                <th>{{ __('backend.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse (($archivedSpecializations ?? []) as $specialization)
                <tr>
                  <td><span class="adm__name">{{ $specialization->name }}</span></td>
                  <td><code class="adm__slug">{{ $specialization->slug }}</code></td>
                  <td class="adm__num">{{ $specialization->professionals_count }}</td>
                  <td><span class="adm__status adm__status--secondary">{{ __('backend.specializations.archived_badge') }}</span></td>
                  <td>
                    @permission('specializations.edit')
                      <a href="{{ route('admin.specializations.edit', $specialization) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                    @endpermission
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="adm__empty">{{ __('backend.specializations.archive_empty') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    @endif
  </div>
@endsection
