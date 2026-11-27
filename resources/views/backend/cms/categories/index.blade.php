@extends('backend.layouts.app')

@section('title', __('backend.cms.categories.title'))
@section('page_title', __('backend.cms.categories.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_categories') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php($hasFilters = request()->filled('q') || request()->filled('status'))

  <div class="adm">
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-folder2"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.cms.categories.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($categories->firstItem() ?? 0), 'last' => number_format($categories->lastItem() ?? 0), 'total' => number_format($categories->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('cms.categories.create')
          <a href="{{ route('admin.cms.categories.create') }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('backend.cms.categories.create') }}
          </a>
        @endpermission
      </div>
    </section>

    <section class="adm__card adm__filters" aria-labelledby="cms-cat-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="cms-cat-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.cms.categories.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.cms.categories.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    <section class="adm__card adm__results" aria-labelledby="cms-cat-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="cms-cat-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.cms.categories.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.slug') }}</th>
              <th>{{ __('backend.cms.pages_count') }}</th>
              <th>{{ __('backend.cms.sort_order') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($categories as $category)
              <tr>
                <td><span class="adm__name">{{ $category->name }}</span></td>
                <td><code class="adm__slug">{{ $category->slug }}</code></td>
                <td class="adm__num">{{ $category->pages_count }}</td>
                <td class="adm__num">{{ $category->sort_order }}</td>
                <td>
                  @if ($category->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('cms.categories.edit')
                      <a href="{{ route('admin.cms.categories.edit', $category) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                    @endpermission
                    @permission('cms.categories.delete')
                      <form action="{{ route('admin.cms.categories.destroy', $category) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
      @if ($categories->hasPages())
        <div class="adm__pagination">{{ $categories->links() }}</div>
      @endif
    </section>
  </div>
@endsection
