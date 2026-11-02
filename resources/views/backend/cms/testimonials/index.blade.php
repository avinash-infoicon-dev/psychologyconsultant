@extends('backend.layouts.app')

@section('title', __('backend.cms.testimonials.title'))
@section('page_title', __('backend.cms.testimonials.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_testimonials') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.testimonials.title') }}</h3>
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
        @permission('cms.testimonials.create')
          <a href="{{ route('admin.cms.testimonials.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.testimonials.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.cms.testimonials.photo') }}</th>
            <th>{{ __('backend.cms.testimonials.author') }}</th>
            <th>{{ __('backend.cms.testimonials.quote') }}</th>
            <th>{{ __('backend.cms.sort_order') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($testimonials as $item)
            <tr>
              <td>
                @if ($item->photo_url)
                  <img src="{{ $item->photo_url }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
                @else
                  —
                @endif
              </td>
              <td>
                <div class="fw-semibold">{{ $item->author_name }}</div>
                <div class="text-muted small">{{ $item->author_role }}</div>
              </td>
              <td>{{ \Illuminate\Support\Str::limit($item->quote, 90) }}</td>
              <td>{{ $item->sort_order }}</td>
              <td>
                @if ($item->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.testimonials.edit')
                  <a href="{{ route('admin.cms.testimonials.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.testimonials.delete')
                  <form action="{{ route('admin.cms.testimonials.destroy', $item) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
    @if ($testimonials->hasPages())
      <div class="card-footer">{{ $testimonials->links() }}</div>
    @endif
  </div>
@endsection
