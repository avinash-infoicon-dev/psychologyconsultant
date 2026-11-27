@extends('backend.layouts.app')

@section('title', __('backend.settings.security.title'))
@section('page_title', __('backend.settings.security.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.settings.security.title') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.settings.security.title') }}</h1>
        </div>
      </div>
    </section>

    {{-- ================= Form ================= --}}
    <div class="adm__card adm__section">
      <form action="{{ route('admin.settings.security.update') }}" method="post">
        @csrf
        @method('PUT')
        <div class="adm__card-head">
          <h2 class="adm__card-title"><i class="bi bi-key" aria-hidden="true"></i>{{ __('backend.settings.security.two_factor_heading') }}</h2>
        </div>
        <div class="adm__card-body adm-form">
          <p class="adm__subtitle mb-3">{{ __('backend.settings.security.two_factor_help') }}</p>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="two_factor_enabled" value="1" id="two_factor_enabled"
              @checked(old('two_factor_enabled', $twoFactorEnabled))>
            <label class="form-check-label" for="two_factor_enabled">
              {{ __('backend.settings.security.two_factor_enabled') }}
            </label>
          </div>

          <div class="mb-0">
            <label class="form-label d-block">{{ __('backend.settings.security.required_for_roles') }}</label>
            <p class="form-text mb-2">{{ __('backend.settings.security.required_for_roles_help') }}</p>
            <div class="adm__checks">
              @foreach ($roles as $role)
                <div class="form-check adm__check">
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
        </div>
        <div class="adm__card-foot">
          <button type="submit" class="adm__btn adm__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
