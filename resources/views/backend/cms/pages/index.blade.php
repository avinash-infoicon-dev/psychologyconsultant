@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.title'))
@section('page_title', __('backend.cms.pages.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_pages') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.pages.all') }}</h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="category" class="form-select form-select-sm">
            <option value="">{{ __('backend.cms.category') }}</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" @selected((string) request('category') === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('cms.pages.create')
          <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.pages.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover align-middle mb-0 cms-admin-table">
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
                <span class="fw-semibold">{{ $page->title }}</span>
                @if ($page->isHomeTemplate())
                  <span class="badge text-bg-success ms-1">{{ __('backend.cms.templates.home') }}</span>
                @endif
              </td>
              <td>
                <a href="{{ url($page->publicPath()) }}" target="_blank" rel="noopener" class="text-decoration-none">{{ $page->publicPath() }}</a>
              </td>
              <td>{{ __('backend.cms.templates.'.$page->template) }}</td>
              <td>{{ $sectionCounts[$page->id] ?? 0 }}</td>
              <td>
                @if ($page->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.pages.edit')
                  <a href="{{ route('admin.cms.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                <a href="{{ route('admin.cms.pages.sections', $page) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.cms.pages.sections') }}</a>
                @permission('cms.pages.delete')
                  <form action="{{ route('admin.cms.pages.destroy', $page) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
    @if ($pages->hasPages())
      <div class="card-footer">{{ $pages->links() }}</div>
    @endif
  </div>
@endsection

@push('styles')
<style>
  .cms-admin-table thead th {
    font-size: .75rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--bs-secondary-color);
  }
</style>
@endpush
