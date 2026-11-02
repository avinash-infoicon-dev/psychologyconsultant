@extends('backend.layouts.app')

@section('title', __('backend.roles.title'))
@section('page_title', __('backend.roles.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.roles') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.roles.title') }}</h3>
      <div class="d-flex gap-2">
        <form method="get" class="d-flex gap-2">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('roles.create')
          <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">{{ __('backend.roles.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.slug') }}</th>
            <th>{{ __('backend.roles.users_count') }}</th>
            <th>{{ __('backend.roles.permissions_count') }}</th>
            <th>{{ __('backend.full_access') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.system') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roles as $role)
            <tr>
              <td>{{ $role->name }}</td>
              <td><code>{{ $role->slug }}</code></td>
              <td>{{ $role->users_count }}</td>
              <td>{{ $role->permissions_count }}</td>
              <td>
                @if ($role->is_full_access)
                  <span class="badge text-bg-primary">{{ __('backend.yes') }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ __('backend.no') }}</span>
                @endif
              </td>
              <td>
                @if ($role->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td>
                @if ($role->is_system)
                  <span class="badge text-bg-info">{{ __('backend.yes') }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ __('backend.no') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('roles.edit')
                  <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('roles.delete')
                  @unless ($role->is_system)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                    </form>
                  @endunless
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($roles->hasPages())
      <div class="card-footer">{{ $roles->links() }}</div>
    @endif
  </div>
@endsection
