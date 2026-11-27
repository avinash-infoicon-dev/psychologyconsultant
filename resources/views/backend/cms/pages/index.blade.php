@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.title'))
@section('page_title', __('backend.cms.pages.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_pages') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('category') || request()->filled('status');
  @endphp

  <div class="adm">
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.cms.pages.all') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($pages->firstItem() ?? 0), 'last' => number_format($pages->lastItem() ?? 0), 'total' => number_format($pages->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('cms.pages.create')
          <a href="{{ route('admin.cms.pages.create') }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('backend.cms.pages.create') }}
          </a>
        @endpermission
      </div>
    </section>

    <section class="adm__card adm__filters" aria-labelledby="cms-page-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="cms-page-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.cms.pages.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.cms.pages.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="category" class="form-select" aria-label="{{ __('backend.cms.category') }}">
          <option value="">{{ __('backend.cms.category') }}</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string) request('category') === (string) $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    <section class="adm__card adm__results" aria-labelledby="cms-page-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="cms-page-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.cms.pages.all') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.cms.pages.col_title') }}</th>
              <th>{{ __('backend.cms.pages.col_slug') }}</th>
              <th>{{ __('backend.cms.pages.col_template') }}</th>
              <th>{{ __('backend.cms.pages.col_sections') }}</th>
              <th>{{ __('backend.cms.pages.col_status') }}</th>
              <th>{{ __('backend.cms.pages.col_actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($pages as $page)
              <tr>
                <td>
                  <span class="adm__name">{{ $page->title }}</span>
                  @if ($page->isHomeTemplate())
                    <span class="adm__status adm__status--success ms-1">{{ __('backend.cms.templates.home') }}</span>
                  @endif
                </td>
                <td>
                  <a href="{{ url($page->publicPath()) }}" target="_blank" rel="noopener" class="adm__sub text-decoration-none">{{ $page->publicPath() }}</a>
                </td>
                <td>{{ __('backend.cms.templates.'.$page->template) }}</td>
                <td class="adm__num">{{ $sectionCounts[$page->id] ?? 0 }}</td>
                <td>
                  @if ($page->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('cms.pages.edit')
                      <a href="{{ route('admin.cms.pages.edit', $page) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                    @endpermission
                    <a href="{{ route('admin.cms.pages.sections', $page) }}" class="adm__btn adm__btn--subtle">{{ __('backend.cms.pages.sections') }}</a>
                    @permission('cms.pages.delete')
                      <form action="{{ route('admin.cms.pages.destroy', $page) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="adm__btn adm__btn--danger">{{ __('backend.delete') }}</button>
                      </form>
                    @endpermission
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="adm__empty"><span class="adm__empty-title">{{ __('backend.no_records') }}</span></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($pages->hasPages())
        <div class="adm__pagination">{{ $pages->links() }}</div>
      @endif
    </section>
  </div>
@endsection
