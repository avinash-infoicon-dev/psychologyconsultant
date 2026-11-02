@extends('backend.layouts.app')

@section('title', __('backend.roles.edit'))
@section('page_title', __('backend.roles.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('backend.menu.roles') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.roles.update', $role) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="name">{{ __('backend.name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $role->name) }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $role->slug) }}" required
              @disabled($role->is_system)>
            @if ($role->is_system)
              <input type="hidden" name="slug" value="{{ $role->slug }}">
            @endif
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="description">{{ __('backend.description') }}</label>
          <textarea name="description" id="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
        </div>
        <div class="d-flex flex-wrap gap-4 mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_full_access" value="1" id="is_full_access" @checked(old('is_full_access', $role->is_full_access))>
            <label class="form-check-label" for="is_full_access">{{ __('backend.full_access') }}</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $role->is_active))>
            <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_system" value="1" id="is_system"
              @checked(old('is_system', $role->is_system)) @disabled($role->is_system)>
            <label class="form-check-label" for="is_system">{{ __('backend.system') }}</label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('backend.assign_permissions') }}</label>
          @foreach ($permissions as $group => $items)
            <div class="mb-2">
              <strong class="text-uppercase small">{{ $group ?: __('backend.group') }}</strong>
              @foreach ($items as $permission)
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                    @checked(collect(old('permissions', $role->permissions->pluck('id')->all()))->contains($permission->id))>
                  <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }} <code class="ms-1">{{ $permission->slug }}</code></label>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
