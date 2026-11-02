@php
  $site = app(\App\Services\Cms\CmsSiteSettingService::class)->current();
  $heroBg = $site->loginHeroUrl();
@endphp
@extends('frontend.layouts.guest')

@section('title', __('frontend.auth.forgot_title'))

@section('content')
<div class="tp-hero-2-wrapper">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle">{{ $site->loginCopy('login_hero_subtitle', __('frontend.auth.forgot_title')) }}</span>
                         <h1 class="tp-hero-2-title font-bold">{{ __('frontend.auth.forgot_title') }}</h1>
                         <p class="cst-hero-text text-white mb-45">{{ __('frontend.auth.forgot_subtitle') }}</p>
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
                        <h3>{{ __('frontend.auth.forgot_title') }}</h3>
                        <p>{{ __('frontend.auth.forgot_subtitle') }}</p>
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

                    <form method="post" action="{{ route('frontend.auth.password.email') }}">
                        @csrf
                        <div class="auth-field">
                            <label for="email">{{ __('frontend.auth.email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required autofocus>
                        </div>
                        <button class="auth-submit" type="submit">
                            {{ __('frontend.auth.forgot_button') }}
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="auth-footer">
                        <a href="{{ route('frontend.auth.login') }}">{{ __('frontend.nav.login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
