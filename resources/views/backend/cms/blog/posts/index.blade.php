@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.posts.title'))
@section('page_title', __('backend.cms.blog.posts.title'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.menu.blog') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.cms.blog.posts.title') }}</li>
@endsection
@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.blog.posts.title') }}</h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="category" class="form-select form-select-sm">
            <option value="">{{ __('backend.cms.blog.category') }}</option>
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
        @permission('cms.blog.posts.create')
          <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.blog.posts.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.cms.blog.posts.cover') }}</th>
            <th>{{ __('backend.cms.title') }}</th>
            <th>{{ __('backend.cms.blog.category') }}</th>
            <th>{{ __('backend.cms.published_at') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($posts as $item)
            <tr>
              <td>
                @if ($item->cover_url)
                  <img src="{{ $item->cover_url }}" alt="" style="width:72px;height:48px;object-fit:cover;border-radius:4px;">
                @else
                  —
                @endif
              </td>
              <td>
                <div class="fw-semibold">{{ $item->title }}</div>
                <div class="small text-muted">{{ $item->author_name }}</div>
              </td>
              <td>{{ $item->category?->name ?? '—' }}</td>
              <td>{{ optional($item->published_at)->format('Y-m-d') ?: '—' }}</td>
              <td>
                @if ($item->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.blog.posts.edit')
                  <a href="{{ route('admin.blog.posts.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.blog.posts.delete')
                  <form action="{{ route('admin.blog.posts.destroy', $item) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
    @if ($posts->hasPages())
      <div class="card-footer">{{ $posts->links() }}</div>
    @endif
  </div>
@endsection
