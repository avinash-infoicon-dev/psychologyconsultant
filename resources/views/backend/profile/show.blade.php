@extends('backend.layouts.app')

@section('title', __('backend.profile.title'))
@section('page_title', __('backend.profile.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.profile.title') }}</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card card-primary card-outline h-100">
        <div class="card-header">
          <h3 class="card-title mb-0">{{ __('backend.profile.details') }}</h3>
        </div>
        <form action="{{ route('admin.profile.update') }}" method="post">
          @csrf
          @method('PUT')
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label" for="name">{{ __('backend.name') }}</label>
              <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="email">{{ __('backend.email') }}</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" disabled required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="phone">{{ __('backend.phone') }}</label>
              <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="mb-3">
              <label class="form-label" for="locale">{{ __('backend.locale') }}</label>
              <select name="locale" id="locale" class="form-select">
                @include('backend.partials.locale-options', ['selected' => old('locale', $user->locale)])
              </select>
            </div>
            <div class="mb-0">
              <label class="form-label">{{ __('backend.menu.roles') }}</label>
              <div>
                @forelse ($user->roles as $role)
                  <span class="badge text-bg-secondary me-1">{{ $role->name }}</span>
                @empty
                  —
                @endforelse
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-5 mb-4">
      <div class="card card-warning card-outline h-100">
        <div class="card-header">
          <h3 class="card-title mb-0">{{ __('backend.profile.change_password') }}</h3>
        </div>
        <form action="{{ route('admin.profile.password') }}" method="post">
          @csrf
          @method('PUT')
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label" for="current_password">{{ __('backend.profile.current_password') }}</label>
              <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password">
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">{{ __('backend.profile.new_password') }}</label>
              <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password">
            </div>
            <div class="mb-0">
              <label class="form-label" for="password_confirmation">{{ __('backend.confirm_password') }}</label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-warning">{{ __('backend.profile.update_password') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
