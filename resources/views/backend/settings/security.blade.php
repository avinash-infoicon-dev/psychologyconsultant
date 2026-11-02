@extends('backend.layouts.app')

@section('title', __('backend.settings.security.title'))
@section('page_title', __('backend.settings.security.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.settings.security.title') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.settings.security.update') }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        <h5 class="mb-3">{{ __('backend.settings.security.two_factor_heading') }}</h5>
        <p class="text-muted">{{ __('backend.settings.security.two_factor_help') }}</p>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" name="two_factor_enabled" value="1" id="two_factor_enabled"
            @checked(old('two_factor_enabled', $twoFactorEnabled))>
          <label class="form-check-label" for="two_factor_enabled">
            {{ __('backend.settings.security.two_factor_enabled') }}
          </label>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ __('backend.settings.security.required_for_roles') }}</label>
          <p class="text-muted small mb-2">{{ __('backend.settings.security.required_for_roles_help') }}</p>
          @foreach ($roles as $role)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="required_for_roles[]" value="{{ $role->slug }}"
                id="role_{{ $role->slug }}"
                @checked(collect(old('required_for_roles', $requiredForRoles))->contains($role->slug))>
              <label class="form-check-label" for="role_{{ $role->slug }}">
                {{ $role->name }}
                <code class="small">{{ $role->slug }}</code>
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
