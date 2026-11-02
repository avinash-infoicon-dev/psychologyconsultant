@extends('backend.layouts.app')

@section('title', __('backend.cms.sliders.title'))
@section('page_title', __('backend.cms.sliders.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_sliders') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.sliders.title') }}</h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('cms.sliders.create')
          <a href="{{ route('admin.cms.sliders.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.sliders.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
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
                  <img src="{{ $slider->image_url }}" alt="" style="width:72px;height:48px;object-fit:cover;border-radius:4px;">
                @else
                  —
                @endif
              </td>
              <td>
                <div class="fw-semibold">{{ $slider->title }}</div>
                @if ($slider->subtitle)
                  <div class="text-muted small">{{ $slider->subtitle }}</div>
                @endif
              </td>
              <td>
                @if ($slider->button_text)
                  <span>{{ $slider->button_text }}</span>
                  <div class="small text-muted">{{ $slider->button_url }}</div>
                @else
                  —
                @endif
              </td>
              <td>{{ $slider->sort_order }}</td>
              <td>
                @if ($slider->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.sliders.edit')
                  <a href="{{ route('admin.cms.sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.sliders.delete')
                  <form action="{{ route('admin.cms.sliders.destroy', $slider) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($sliders->hasPages())
      <div class="card-footer">{{ $sliders->links() }}</div>
    @endif
  </div>
@endsection
