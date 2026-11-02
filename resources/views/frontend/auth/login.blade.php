@php
  $site = app(\App\Services\Cms\CmsSiteSettingService::class)->current();
  $heroBg = $site->loginHeroUrl();
@endphp
@extends('frontend.layouts.guest')

@section('title', $site->loginCopy('login_hero_title', __('frontend.nav.login')))

@section('content')
<div class="tp-hero-2-wrapper">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle">{{ $site->loginCopy('login_hero_subtitle', __('frontend.auth.login_title')) }}</span>
                         <h1 class="tp-hero-2-title font-bold">{{ $site->loginCopy('login_hero_title', __('frontend.nav.login')) }}</h1>
                         <p class="cst-hero-text text-white mb-45">{{ $site->loginCopy('login_hero_text', __('frontend.auth.login_subtitle')) }}</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
</div>

<main>
    <section class="auth-page">
        <div class="container container-1330">
            <div class="auth-shell">
                <div class="auth-visual">
                    @if ($site->loginCopy('login_kicker'))
                      <span class="auth-kicker">{{ $site->loginCopy('login_kicker') }}</span>
                    @endif
                    <h2>{{ $site->loginCopy('login_visual_title', __('frontend.auth.brand_title')) }}</h2>
                    <p>{{ $site->loginCopy('login_visual_text', __('frontend.auth.brand_text')) }}</p>
                    <ul class="auth-points">
                        <li><i class="fa-solid fa-shield-halved"></i> {{ $site->loginCopy('login_point_1', __('frontend.auth.brand_point_1')) }}</li>
                        <li><i class="fa-solid fa-comments"></i> {{ $site->loginCopy('login_point_2', __('frontend.auth.brand_point_2')) }}</li>
                        <li><i class="fa-solid fa-calendar-check"></i> {{ $site->loginCopy('login_point_3', __('frontend.auth.brand_point_3')) }}</li>
                        @if ($site->loginCopy('login_point_4'))
                          <li><i class="fa-solid fa-user-doctor"></i> {{ $site->loginCopy('login_point_4') }}</li>
                        @endif
                    </ul>
                </div>
                <div class="auth-form">
                    <div class="auth-form-head">
                        <h3>{{ $site->loginCopy('login_form_title', __('frontend.auth.login_title')) }}</h3>
                        <p>{{ $site->loginCopy('login_form_subtitle', __('frontend.auth.login_subtitle')) }}</p>
                    </div>

                    @if (session('success'))
                      <div class="fe-alert fe-alert--success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                      <div class="fe-alert fe-alert--error">
                        @foreach ($errors->all() as $error)
                          <div>{{ $error }}</div>
                        @endforeach
                      </div>
                    @endif

                    <form method="post" action="{{ route('frontend.auth.login') }}">
                        @csrf
                        <div class="auth-field">
                            <label for="email">{{ __('frontend.auth.email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required autofocus>
                        </div>
                        <div class="auth-field">
                            <label for="password">{{ __('frontend.auth.password') }}</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                        <div class="auth-row">
                            <label class="auth-check">
                                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                                {{ __('frontend.auth.remember') }}
                            </label>
                            <a class="auth-link" href="{{ route('frontend.auth.password.request') }}">{{ __('frontend.auth.forgot_password') }}</a>
                            {{-- Design placeholder (no public reset route yet)
                            <a class="auth-link" href="#">Forgot Password?</a>
                            --}}
                        </div>
                        <button class="auth-submit" type="submit">
                            {{ __('frontend.auth.login_button') }}
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                    <div class="auth-footer">
                        {{ __('frontend.auth.no_account') }}
                        <a href="{{ route('frontend.auth.register') }}">{{ __('frontend.nav.register') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

{{--
Previous login layout (kept for reference):

@extends('frontend.layouts.guest', ['hideHeader' => true, 'hideFooter' => true])

@section('title', __('frontend.auth.login_title'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontendassets/css/auth.css') }}">
@endpush

@section('content')
  <div class="fe-auth-page">
    @include('frontend.partials.auth-brand-panel')

    <div class="fe-auth-panel">
      <div class="fe-auth-box">
        @include('frontend.partials.locale-switch')

        <h2>{{ __('frontend.auth.login_title') }}</h2>
        <p class="fe-subtitle">{{ __('frontend.auth.login_subtitle') }}</p>

        @if (session('success'))
          <div class="fe-alert fe-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
          <div class="fe-alert fe-alert--error">
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form method="post" action="{{ route('frontend.auth.login') }}">
          @csrf
          <div class="fe-form-group">
            <label for="email">{{ __('frontend.auth.email') }}</label>
            <input type="email" name="email" id="email" class="fe-input" value="{{ old('email') }}" required autofocus>
          </div>
          <div class="fe-form-group">
            <label for="password">{{ __('frontend.auth.password') }}</label>
            <input type="password" name="password" id="password" class="fe-input" required>
          </div>
          <label class="fe-check">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            <span>{{ __('frontend.auth.remember') }}</span>
          </label>
          <button type="submit" class="fe-btn fe-btn--primary fe-btn--block" style="margin-top:1rem;">
            {{ __('frontend.auth.login_button') }}
          </button>
        </form>

        <p class="fe-auth-footer">
          {{ __('frontend.auth.no_account') }}
          <a href="{{ route('frontend.auth.register') }}">{{ __('frontend.nav.register') }}</a>
        </p>
      </div>
    </div>
  </div>
@endsection
--}}
