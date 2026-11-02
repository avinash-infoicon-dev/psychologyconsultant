@extends('frontend.layouts.dashboard')

@section('title', __('profile_api.web.title'))
@section('page_title', __('profile_api.web.title'))

@section('content')
  <div class="fe-cms-card">
    <form action="{{ route('frontend.patient.profile.update') }}" method="post">
      @csrf
      @method('PUT')

      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="name">{{ __('profile_api.validation.attributes.name') }}</label>
          <input type="text" name="name" id="name" class="fe-input" value="{{ old('name', $patient->user->name) }}" required>
        </div>
        <div class="fe-form-group">
          <label for="email">{{ __('auth_api.validation.attributes.email') }}</label>
          <input type="email" id="email" class="fe-input" value="{{ $patient->user->email }}" disabled>
        </div>
        <div class="fe-form-group">
          <label for="phone">{{ __('profile_api.validation.attributes.phone') }}</label>
          <input type="text" name="phone" id="phone" class="fe-input" value="{{ old('phone', $patient->user->phone) }}">
        </div>
        <div class="fe-form-group">
          <label for="locale">{{ __('profile_api.validation.attributes.locale') }}</label>
          <select name="locale" id="locale" class="fe-select">
            @include('frontend.partials.locale-options', ['selected' => old('locale', $patient->user->locale)])
          </select>
        </div>
        <div class="fe-form-group">
          <label for="date_of_birth">{{ __('profile_api.validation.attributes.date_of_birth') }}</label>
          <input type="date" name="date_of_birth" id="date_of_birth" class="fe-input"
            value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}">
        </div>
        <div class="fe-form-group">
          <label for="gender">{{ __('profile_api.validation.attributes.gender') }}</label>
          <input type="text" name="gender" id="gender" class="fe-input" value="{{ old('gender', $patient->gender) }}">
        </div>
        <div class="fe-form-group">
          <label for="address">{{ __('profile_api.validation.attributes.address') }}</label>
          <input type="text" name="address" id="address" class="fe-input" value="{{ old('address', $patient->address) }}">
        </div>
        <div class="fe-form-group">
          <label for="city">{{ __('profile_api.validation.attributes.city') }}</label>
          <input type="text" name="city" id="city" class="fe-input" value="{{ old('city', $patient->city) }}">
        </div>
        <div class="fe-form-group">
          <label for="country">{{ __('profile_api.validation.attributes.country') }}</label>
          <input type="text" name="country" id="country" class="fe-input" maxlength="2"
            value="{{ old('country', $patient->country) }}" placeholder="CH">
        </div>
        <div class="fe-form-group">
          <label for="emergency_contact_name">{{ __('profile_api.validation.attributes.emergency_contact_name') }}</label>
          <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="fe-input"
            value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
        </div>
        <div class="fe-form-group">
          <label for="emergency_contact_phone">{{ __('profile_api.validation.attributes.emergency_contact_phone') }}</label>
          <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="fe-input"
            value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
        </div>
      </div>

      <div class="fe-form-actions">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('profile_api.web.save') }}</button>
      </div>
    </form>
  </div>
  {{-- Preferences moved to admin Settings → Notifications. --}}
  {{-- Restored: users can set types the admin has not applied globally. --}}
  {{--@include('frontend.partials.notification-preferences', ['prefFormId' => 'fe-notif-pref-patient'])--}}
@endsection
