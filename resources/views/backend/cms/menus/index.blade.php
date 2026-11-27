@extends('backend.layouts.app')

@section('title', __('backend.cms.menus.title'))
@section('page_title', __('backend.cms.menus.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_menus') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-list"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.cms.menus.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($menus->firstItem() ?? 0), 'last' => number_format($menus->lastItem() ?? 0), 'total' => number_format($menus->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('cms.menus.create')
          <a href="{{ route('admin.cms.menus.create', ['location' => $location]) }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('backend.cms.menus.create') }}
          </a>
        @endpermission
      </div>
    </section>

    <section class="adm__card adm__filters" aria-labelledby="cms-menu-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="cms-menu-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.cms.menus.index') }}">
        <select name="location" class="form-select" onchange="this.form.submit()" aria-label="{{ __('backend.cms.location') }}">
          @foreach ($locations as $key => $label)
            <option value="{{ $key }}" @selected($location === $key)>{{ __('backend.cms.locations.'.$key) }}</option>
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

    <section class="adm__card adm__results" aria-labelledby="cms-menu-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="cms-menu-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.cms.menus.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.cms.title') }}</th>
              <th>{{ __('backend.cms.type') }}</th>
              <th>{{ __('backend.cms.reference') }}</th>
              <th>{{ __('backend.cms.fallback_url') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($menus as $menu)
              <tr>
                <td>
                  <span class="adm__name">{{ $menu->title }}</span>
                  @if ($menu->children->isNotEmpty())
                    <span class="adm__sub">{{ $menu->children->count() }} {{ __('backend.cms.children') }}</span>
                  @endif
                </td>
                <td><span class="adm__status adm__status--secondary">{{ __('backend.cms.types.'.$menu->type) }}</span></td>
                <td>
                  @if ($menu->type === 'page')
                    #{{ $menu->reference_id }}
                  @elseif ($menu->type === 'category')
                    #{{ $menu->reference_id }}
                  @elseif ($menu->type === 'route')
                    <code class="adm__slug">{{ $menu->route_name }}</code>
                  @else
                    <span class="adm__muted">—</span>
                  @endif
                </td>
                <td><code class="adm__slug">{{ $menu->url ?: config('cms.default_url') }}</code></td>
                <td>
                  @if ($menu->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('cms.menus.edit')
                      <a href="{{ route('admin.cms.menus.edit', $menu) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                    @endpermission
                    @permission('cms.menus.delete')
                      <form action="{{ route('admin.cms.menus.destroy', $menu) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
      @if ($menus->hasPages())
        <div class="adm__pagination">{{ $menus->links() }}</div>
      @endif
    </section>
  </div>
@endsection
