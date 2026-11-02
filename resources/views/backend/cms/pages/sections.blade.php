@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.sections_heading', ['page' => $page->title]))
@section('page_title', __('backend.cms.pages.sections_heading', ['page' => $page->title]))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.edit', $page) }}">{{ $page->title }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.cms.pages.sections') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.pages.page_sections') }}</h3>
      <div class="d-flex gap-2">
        {{-- Add section is not used: sections map existing page content.
        <a href="#" class="btn btn-primary btn-sm">{{ __('backend.cms.pages.add_section') }}</a>
        --}}
        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('backend.cms.pages.back_to_page') }}</a>
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover align-middle mb-0 cms-admin-table">
        <thead>
          <tr>
            <th>{{ __('backend.cms.pages.col_order') }}</th>
            <th>{{ __('backend.cms.pages.col_type') }}</th>
            <th>{{ __('backend.cms.title') }}</th>
            <th>{{ __('backend.cms.pages.col_items') }}</th>
            <th>{{ __('backend.cms.pages.col_status') }}</th>
            <th>{{ __('backend.cms.pages.col_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($sections as $section)
            <tr>
              <td>{{ $section['order'] }}</td>
              <td>{{ $section['type'] }}</td>
              <td>{{ $section['title'] ?: '—' }}</td>
              <td>{{ $section['items'] }}</td>
              <td>
                @if ($section['is_active'])
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @if ($section['edit_url'])
                  @permission('cms.pages.edit')
                    <a href="{{ $section['edit_url'] }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                  @endpermission
                @endif
                @if ($section['items_url'])
                  <a href="{{ $section['items_url'] }}" class="btn btn-sm btn-outline-primary">{{ __('backend.cms.pages.items') }}</a>
                @endif
                {{-- Virtual sections are not deleted:
                <button type="button" class="btn btn-sm btn-outline-danger" disabled>{{ __('backend.delete') }}</button>
                --}}
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
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
