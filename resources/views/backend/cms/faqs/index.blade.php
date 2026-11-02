@extends('backend.layouts.app')

@section('title', __('backend.cms.faqs.title'))
@section('page_title', __('backend.cms.faqs.title'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.cms.faqs.questions') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.faqs.title') }}</h3>
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
        @permission('cms.faqs.create')
          <a href="{{ route('admin.cms.faqs.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.faqs.create') }}</a>
        @endpermission
        @if ($faqPage ?? null)
          <a href="{{ route('admin.cms.pages.sections', $faqPage) }}" class="btn btn-outline-secondary btn-sm">{{ __('backend.cms.pages.back_to_page') }}</a>
        @endif
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.cms.faqs.question') }}</th>
            <th>{{ __('backend.cms.faqs.answer') }}</th>
            <th>{{ __('backend.cms.sort_order') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($faqs as $item)
            <tr>
              <td class="fw-semibold">{{ $item->question }}</td>
              <td>{{ \Illuminate\Support\Str::limit($item->answer, 90) }}</td>
              <td>{{ $item->sort_order }}</td>
              <td>
                @if ($item->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.faqs.edit')
                  <a href="{{ route('admin.cms.faqs.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.faqs.delete')
                  <form action="{{ route('admin.cms.faqs.destroy', $item) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($faqs->hasPages())
      <div class="card-footer">{{ $faqs->links() }}</div>
    @endif
  </div>
@endsection
