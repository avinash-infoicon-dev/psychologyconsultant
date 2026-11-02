@extends('frontend.layouts.guest')

@section('title', __('frontend.onboarding.account_title'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontendassets/css/onboarding.css') }}">
@endpush

@section('content')
  <div class="tp-hero-2-wrapper">
    <div class="tp-banner-area include-bg pt-180 pb-80">
      <div class="container">
        <div class="tp-hero-2-content text-center mb-25">
          <h1 class="tp-hero-2-title font-bold">{{ __('frontend.onboarding.account_title') }}</h1>
          <p class="cst-hero-text text-white mb-0">{{ __('frontend.onboarding.account_subtitle', ['plan' => $plan->name]) }}</p>
        </div>
      </div>
    </div>
  </div>

  <main class="onboarding-page pb-120">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="onboarding-account">
            @if ($errors->any())
              <p class="onboarding-error">{{ $errors->first() }}</p>
            @endif
            <form method="POST" action="{{ route('frontend.onboarding.store', $plan) }}">
              @csrf
              <div class="row gx-12">
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.last_name') }}</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.first_name') }}</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.password') }}</label>
                    <input type="password" name="password" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.onboarding.password_confirmation') }}</label>
                    <input type="password" name="password_confirmation" required>
                  </div>
                </div>
                {{-- Existing patient registration also requires date of birth and country. --}}
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.auth.date_of_birth') }}</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="tp-contact-form-input mb-20">
                    <label>{{ __('frontend.auth.country') }}</label>
                    @php
                      $selectedCountry = old('country', $allowedCountries->has('CH') ? 'CH' : $allowedCountries->keys()->first());
                    @endphp
                    <select name="country" required>
                      @foreach ($allowedCountries as $code => $name)
                        <option value="{{ $code }}" @selected($selectedCountry === $code)>{{ $name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-12 mb-15">
                  <label class="onboarding-check">
                    <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms')) required>
                    <span>{{ __('frontend.auth.accept_terms') }}</span>
                  </label>
                </div>
                <div class="col-12 mb-20">
                  <label class="onboarding-check">
                    <input type="checkbox" name="accept_privacy_policy" value="1" @checked(old('accept_privacy_policy')) required>
                    <span>{{ __('frontend.auth.accept_privacy') }}</span>
                  </label>
                </div>
                <div class="col-12">
                  <button type="submit" class="onboarding-plan__btn">{{ __('frontend.onboarding.continue_payment') }}</button>
                  <p class="onboarding-login">
                    {{ __('frontend.onboarding.already_account') }}
                    <a href="{{ route('frontend.auth.login') }}">{{ __('frontend.onboarding.login') }}</a>
                  </p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
