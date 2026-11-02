@extends('backend.layouts.app')

@section('title', __('backend.permissions.title'))
@section('page_title', __('backend.permissions.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.permissions') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="card-title mb-0">{{ __('backend.permissions.title') }}</h3>
      @permission('permissions.assign')
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">{{ __('backend.permissions.create') }}</a>
      @endpermission
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.slug') }}</th>
            <th>{{ __('backend.group') }}</th>
            <th>{{ __('backend.description') }}</th>
            <th>{{ __('backend.permissions.roles_count') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($permissions as $permission)
            <tr>
              <td>{{ $permission->name }}</td>
              <td><code>{{ $permission->slug }}</code></td>
              <td><span class="badge text-bg-light">{{ $permission->group ?: '—' }}</span></td>
              <td>{{ $permission->description ?: '—' }}</td>
              <td>{{ $permission->roles_count }}</td>
              <td class="text-nowrap">
                @permission('permissions.assign')
                  <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                  <form action="{{ route('admin.permissions.destroy', $permission) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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
    @if ($permissions->hasPages())
      <div class="card-footer">{{ $permissions->links() }}</div>
    @endif
  </div>
@endsection
