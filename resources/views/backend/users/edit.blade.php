@extends('backend.layouts.app')

@section('title', __('backend.users.edit'))
@section('page_title', __('backend.users.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('backend.menu.users') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.users.update', $user) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="name">{{ __('backend.name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="email">{{ __('backend.email') }}</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="phone">{{ __('backend.phone') }}</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="locale">{{ __('backend.locale') }}</label>
            <select name="locale" id="locale" class="form-select">
              @include('backend.partials.locale-options', ['selected' => old('locale', $user->locale)])
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="password">{{ __('backend.password') }}</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('backend.password_optional') }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="password_confirmation">{{ __('backend.confirm_password') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
          </div>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $user->is_active))>
          <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
        </div>
        <div class="mb-3">
          <label class="form-label" for="two_factor_policy">{{ __('backend.users.two_factor_policy') }}</label>
          <select name="two_factor_policy" id="two_factor_policy" class="form-select">
            <option value="default" @selected(old('two_factor_policy', $twoFactorPolicy) === 'default')>{{ __('backend.users.two_factor_policy_default') }}</option>
            <option value="required" @selected(old('two_factor_policy', $twoFactorPolicy) === 'required')>{{ __('backend.users.two_factor_policy_required') }}</option>
            <option value="exempt" @selected(old('two_factor_policy', $twoFactorPolicy) === 'exempt')>{{ __('backend.users.two_factor_policy_exempt') }}</option>
          </select>
          <div class="form-text">{{ __('backend.users.two_factor_policy_help') }}</div>
        </div>
        <div class="mb-3">
          <label class="form-label">{{ __('backend.assign_roles') }}</label>
          @foreach ($roles as $role)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                @checked(collect(old('roles', $user->roles->pluck('id')->all()))->contains($role->id))>
              <label class="form-check-label" for="role_{{ $role->id }}">
                {{ $role->name }}
                @if ($role->is_system)
                  <span class="badge text-bg-info">{{ __('backend.system') }}</span>
                @endif
              </label>
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
