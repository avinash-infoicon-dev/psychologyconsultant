@extends('backend.layouts.app')

@section('title', __('backend.cms.sliders.title'))
@section('page_title', __('backend.cms.sliders.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_sliders') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php($hasFilters = request()->filled('q') || request()->filled('status'))

  <div class="adm">
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-images"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.cms.sliders.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($sliders->firstItem() ?? 0), 'last' => number_format($sliders->lastItem() ?? 0), 'total' => number_format($sliders->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('cms.sliders.create')
          <a href="{{ route('admin.cms.sliders.create') }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ __('backend.cms.sliders.create') }}
          </a>
        @endpermission
      </div>
    </section>

    <section class="adm__card adm__filters" aria-labelledby="cms-slider-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="cms-slider-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.cms.sliders.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.cms.sliders.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    <section class="adm__card adm__results" aria-labelledby="cms-slider-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="cms-slider-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.cms.sliders.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.cms.sliders.image') }}</th>
              <th>{{ __('backend.cms.title') }}</th>
              <th>{{ __('backend.cms.sliders.button') }}</th>
              <th>{{ __('backend.cms.sort_order') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($sliders as $slider)
              <tr>
                <td>
                  @if ($slider->image_url)
                    <img src="{{ $slider->image_url }}" alt="" class="adm__thumb">
                  @else
                    <span class="adm__muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="adm__name">{{ $slider->title }}</span>
                  @if ($slider->subtitle)
                    <span class="adm__sub">{{ $slider->subtitle }}</span>
                  @endif
                  @if ($slider->para_text)
                    <span class="adm__sub">{{ \Illuminate\Support\Str::limit($slider->para_text, 80) }}</span>
                  @endif
                </td>
                <td>
                  @if ($slider->button_text)
                    <span class="adm__name">{{ $slider->button_text }}</span>
                    <span class="adm__sub">{{ $slider->button_url }}</span>
                  @else
                    <span class="adm__muted">—</span>
                  @endif
                </td>
                <td class="adm__num">{{ $slider->sort_order }}</td>
                <td>
                  @if ($slider->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('cms.sliders.edit')
                      <a href="{{ route('admin.cms.sliders.edit', $slider) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                    @endpermission
                    @permission('cms.sliders.delete')
                      <form action="{{ route('admin.cms.sliders.destroy', $slider) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
      @if ($sliders->hasPages())
        <div class="adm__pagination">{{ $sliders->links() }}</div>
      @endif
    </section>
  </div>
@endsection
