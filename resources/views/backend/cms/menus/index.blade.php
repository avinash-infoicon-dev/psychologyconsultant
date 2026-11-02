@extends('backend.layouts.app')

@section('title', __('backend.cms.menus.title'))
@section('page_title', __('backend.cms.menus.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_menus') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.menus.title') }}</h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2">
          <select name="location" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach ($locations as $key => $label)
              <option value="{{ $key }}" @selected($location === $key)>{{ __('backend.cms.locations.'.$key) }}</option>
            @endforeach
          </select>
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('cms.menus.create')
          <a href="{{ route('admin.cms.menus.create', ['location' => $location]) }}" class="btn btn-primary btn-sm">{{ __('backend.cms.menus.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
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
                {{ $menu->title }}
                @if ($menu->children->isNotEmpty())
                  <div class="small text-muted">{{ $menu->children->count() }} {{ __('backend.cms.children') }}</div>
                @endif
              </td>
              <td><span class="badge text-bg-secondary">{{ __('backend.cms.types.'.$menu->type) }}</span></td>
              <td>
                @if ($menu->type === 'page')
                  #{{ $menu->reference_id }}
                @elseif ($menu->type === 'category')
                  #{{ $menu->reference_id }}
                @elseif ($menu->type === 'route')
                  <code class="small">{{ $menu->route_name }}</code>
                @else
                  —
                @endif
              </td>
              <td><code class="small">{{ $menu->url ?: config('cms.default_url') }}</code></td>
              <td>
                @if ($menu->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.menus.edit')
                  <a href="{{ route('admin.cms.menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.menus.delete')
                  <form action="{{ route('admin.cms.menus.destroy', $menu) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
    @if ($menus->hasPages())
      <div class="card-footer">{{ $menus->links() }}</div>
    @endif
  </div>
@endsection
