@php
  $headerMenus = app(\App\Services\Cms\CmsMenuService::class)->forLocation('header');
  $site = app(\App\Services\Cms\CmsSiteSettingService::class)->current();
  $siteBrand = $site->brandName();
  $siteTagline = $site->taglineText();
  $siteLogo = $site->logoUrl();
  $siteLogoDark = $site->logoUrl('dark');
@endphp

{{-- Design chrome + header (from projectinfo/frontend_design/online-psychology/lib/header.php) --}}
<!-- Begin magic cursor -->
<div id="magic-cursor">
    <div id="ball"></div>
</div>
<!-- End magic cursor -->

<!-- preloader -->
<div id="preloader">
    <div class="preloader">
        <span></span>
        <span></span>
    </div>
</div>
<!-- preloader end  -->

<!-- back to top start -->
<div class="back-to-top-wrapper">
    <button id="back_to_top" type="button" class="back-to-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>
</div>
<!-- back to top end -->

<!-- tp-offcanvus-area-start -->
<div class="tp-offcanvas-area">
    <div class="tp-offcanvas-wrapper @@class">
        <div class="tp-offcanvas-top d-flex align-items-center justify-content-between">
            <div class="tp-offcanvas-logo">
                <a href="{{ route('frontend.home') }}">
                    <img class="logo-1" src="{{ $siteLogo }}" alt="{{ $siteBrand }}">
                    {{-- <img class="logo-1" src="{{ asset('frontendassets/assets/img/logo.webp') }}" alt="{{ __('frontend.brand') }}"> --}}
                </a>
            </div>
            <div class="tp-offcanvas-close">
                <button class="tp-offcanvas-close-btn" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="tp-offcanvas-main">
            {{-- Cloned from desktop nav by assets/js/main.js --}}
            <div class="tp-offcanvas-menu d-xl-none">
                <nav></nav>
            </div>
            <div class="tp-offcanvas-content d-none d-xl-block">
                <h3 class="tp-offcanvas-title">{{ $siteBrand }}</h3>
                <p>{{ $siteTagline }}</p>
            </div>
            <div class="tp-offcanvas-contact">
                <h3 class="tp-offcanvas-title sm">{{ $site->offcanvas_info_title ?: 'Information' }}</h3>
                <ul>
                    @if ($site->phone)
                      <li><a href="{{ $site->phoneHref() }}">{{ $site->phone }}</a></li>
                    @endif
                    @if ($site->email)
                      <li><a href="{{ $site->emailHref() }}">{{ $site->email }}</a></li>
                    @endif
                    @if ($site->availability)
                      <li><a href="#">{{ $site->availability }}</a></li>
                    @endif
                    {{-- Previous hardcoded contact:
                    <li><a href="tel:+18005551234">+1 (800) 555-1234</a></li>
                    <li><a href="mailto:support@mindlawconnect.com">support@mindlawconnect.com</a></li>
                    <li><a href="#">Available 24/7 Online Worldwide</a></li>
                    --}}
                    @auth
                      @if (auth()->user()->isPatient())
                        <li><a href="{{ route('frontend.patient.dashboard') }}">{{ __('frontend.nav.dashboard') }}</a></li>
                      @elseif (auth()->user()->isPsychologist())
                        <li><a href="{{ route('frontend.psychologist.dashboard') }}">{{ __('frontend.nav.dashboard') }}</a></li>
                      @endif
                      <li>
                        <form action="{{ route('frontend.auth.logout') }}" method="post">
                          @csrf
                          @method('DELETE')
                          <button type="submit">{{ __('frontend.nav.logout') }}</button>
                        </form>
                      </li>
                    @else
                      <li><a href="{{ route('frontend.onboarding.plans') }}">{{ __('frontend.nav.subscription_plan') }}</a></li>
                      <li><a href="{{ route('frontend.auth.login') }}">{{ __('frontend.nav.login') }}</a></li>
                      <li><a href="{{ route('frontend.auth.register') }}">{{ __('frontend.nav.register') }}</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="body-overlay"></div>
<!-- tp-offcanvus-area-end -->

<!-- search area start -->
<div class="tp-search-area p-relative">
    <div class="tp-search-close">
        <button class="tp-search-close-btn" type="button">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<div class="body-overlay"></div>
<!-- search area end -->

<header>
    <div id="header-sticky"
        class="tp-header-area tp-header-13-ptb sticky-white-bg tp-header-blur header-transparent">
        <div class="container container-1750">
            <div class="row align-items-center">
                <div class="col-xl-3 col-lg-5 col-5">
                    <div class="tp-header-logo">
                        <a href="{{ route('frontend.home') }}">
                            <img class="logo-white img-fluid" src="{{ $siteLogo }}" alt="{{ $siteBrand }}">
                            <img class="logo-black img-fluid d-none" src="{{ $siteLogoDark }}" alt="{{ $siteBrand }}">
                            {{-- <img class="logo-white img-fluid" src="{{ asset('frontendassets/assets/img/logo.webp') }}" alt="{{ __('frontend.brand') }}">
                            <img class="logo-black img-fluid d-none" src="{{ asset('frontendassets/assets/img/logo-dark.webp') }}" alt="{{ __('frontend.brand') }}"> --}}
                        </a>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-7 col-7">
                    <div
                        class="tp-header-box d-flex align-items-center justify-content-end justify-content-xl-between">
                        {{-- tp-header-13-menu adds margin-left:270px on wide screens, which shoves the nav into the hero and wraps "Dashboard" after login. Keep the class commented. --}}
                        <div
                            class="tp-header-menu cst-menu tp-header-dropdown dropdown-white-bg d-none d-xl-flex">
                            {{-- class="tp-header-menu tp-header-13-menu cst-menu tp-header-dropdown dropdown-white-bg d-none d-xl-flex" --}}
                            <nav class="tp-mobile-menu-active">
                                <ul>
                                    @foreach ($headerMenus as $item)
                                      @if (! empty($item['children']))
                                        <li class="has-dropdown @if(url()->current() === url($item['href'])) active @endif">
                                          <a href="{{ $item['href'] }}" target="{{ $item['target'] }}">
                                            @if ($item['icon'])<i class="{{ $item['icon'] }}"></i>@endif
                                            {{ $item['title'] }}
                                          </a>
                                          <ul class="tp-submenu submenu">
                                            @foreach ($item['children'] as $child)
                                              <li>
                                                <a href="{{ $child['href'] }}" target="{{ $child['target'] }}">
                                                  @if ($child['icon'])<i class="{{ $child['icon'] }}"></i>@endif
                                                  {{ $child['title'] }}
                                                </a>
                                              </li>
                                            @endforeach
                                          </ul>
                                        </li>
                                      @else
                                        <li class="@if(url()->current() === url($item['href'])) active @endif">
                                          <a href="{{ $item['href'] }}" target="{{ $item['target'] }}">
                                            @if ($item['icon'])<i class="{{ $item['icon'] }}"></i>@endif
                                            {{ $item['title'] }}
                                          </a>
                                        </li>
                                      @endif
                                    @endforeach

                                    <li class="@if(request()->routeIs('frontend.onboarding.plans', 'frontend.patient.subscription.*')) active @endif">
                                      <a href="{{ auth()->user()?->isPatient() ? route('frontend.patient.subscription.index') : route('frontend.onboarding.plans') }}">
                                        {{ __('frontend.nav.subscription_plan') }}
                                      </a>
                                    </li>

                                    @auth
                                      @if (auth()->user()->isPatient())
                                        <li class="@if(request()->routeIs('frontend.patient.dashboard')) active @endif">
                                          <a href="{{ route('frontend.patient.dashboard') }}">{{ __('frontend.nav.dashboard') }}</a>
                                        </li>
                                      @elseif (auth()->user()->isPsychologist())
                                        <li class="@if(request()->routeIs('frontend.psychologist.dashboard')) active @endif">
                                          <a href="{{ route('frontend.psychologist.dashboard') }}">{{ __('frontend.nav.dashboard') }}</a>
                                        </li>
                                      @endif
                                    @endauth
                                </ul>
                            </nav>
                        </div>
                        <div class="tp-header-right cr-header d-flex align-items-center justify-content-end">
                            @include('frontend.partials.header-message-bell')
                            {{-- @include('frontend.partials.theme-toggle') --}}
                            @include('frontend.partials.language-dropdown')

                            @auth
                              <div class="cr-header-login d-none d-lg-block">
                                <form action="{{ route('frontend.auth.logout') }}" method="post">
                                  @csrf
                                  @method('DELETE')
                                  {{-- Design uses an <a>; keep logout as a matching button --}}
                                  <button type="submit">{{ __('frontend.nav.logout') }}</button>
                                </form>
                              </div>
                            @else
                              <div class="cr-header-login d-none d-lg-block">
                                  <a href="{{ route('frontend.auth.login') }}">{{ __('frontend.nav.login') }}</a>
                              </div>
                              <div class="tp-header-btn-box d-none d-md-block ml-15">
                                  <a href="{{ route('frontend.auth.register') }}" class="tp-btn-white-border tp-btn-light-bg">{{ __('frontend.nav.register') }}</a>
                              </div>
                            @endauth
                            <div class="tp-header-bar ml-20 d-xl-none">
                                <button class="tp-offcanvas-open-btn" type="button">
                                    <i></i><i></i><i></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@if (false)
{{-- Previous functional header (kept for reference) --}}
<header class="fe-header">
  <div class="fe-container fe-header__inner">
    <a href="{{ route('frontend.home') }}" class="fe-brand">
      <span class="fe-brand__icon"><i class="bi bi-heart-pulse-fill"></i></span>
      {{ __('frontend.brand') }}
    </a>

    <button class="fe-mobile-toggle" type="button" data-fe-mobile-toggle aria-label="Menu">
      <i class="bi bi-list"></i>
    </button>

    <nav class="fe-nav" data-fe-nav>
      @foreach ($headerMenus as $item)
        @if (! empty($item['children']))
          <div class="fe-nav__dropdown">
            <a href="{{ $item['href'] }}" target="{{ $item['target'] }}" class="fe-nav__link">
              @if ($item['icon'])<i class="{{ $item['icon'] }}"></i>@endif
              {{ $item['title'] }}
              <i class="bi bi-chevron-down fe-nav__chevron"></i>
            </a>
            <div class="fe-nav__submenu">
              @foreach ($item['children'] as $child)
                <a href="{{ $child['href'] }}" target="{{ $child['target'] }}">
                  @if ($child['icon'])<i class="{{ $child['icon'] }}"></i>@endif
                  {{ $child['title'] }}
                </a>
              @endforeach
            </div>
          </div>
        @else
          <a
            href="{{ $item['href'] }}"
            target="{{ $item['target'] }}"
            class="fe-nav__link @if(url()->current() === url($item['href'])) active @endif"
          >
            @if ($item['icon'])<i class="{{ $item['icon'] }}"></i>@endif
            {{ $item['title'] }}
          </a>
        @endif
      @endforeach

      @auth
        @if (auth()->user()->isPatient())
          <a href="{{ route('frontend.patient.dashboard') }}" class="fe-nav__link @if(request()->routeIs('frontend.patient.dashboard')) active @endif">
            {{ __('frontend.nav.dashboard') }}
          </a>
        @elseif (auth()->user()->isPsychologist())
          <a href="{{ route('frontend.psychologist.dashboard') }}" class="fe-nav__link @if(request()->routeIs('frontend.psychologist.dashboard')) active @endif">
            {{ __('frontend.nav.dashboard') }}
          </a>
        @endif
      @endauth
    </nav>

    <div class="fe-header__actions">
      @include('frontend.partials.header-message-bell')
      {{--@include('frontend.partials.theme-toggle')--}}
      @include('frontend.partials.language-dropdown')

      @auth
        <form action="{{ route('frontend.auth.logout') }}" method="post" class="fe-header__logout">
          @csrf
          @method('DELETE')
          {{-- Legacy full outline button size:
          <button type="submit" class="fe-btn fe-btn--outline">{{ __('frontend.nav.logout') }}</button>
          --}}
          <button type="submit" class="fe-btn fe-btn--outline fe-btn--header">{{ __('frontend.nav.logout') }}</button>
        </form>
      @else
        <a href="{{ route('frontend.auth.login') }}" class="fe-btn fe-btn--ghost fe-btn--header">{{ __('frontend.nav.login') }}</a>
        <a href="{{ route('frontend.auth.register') }}" class="fe-btn fe-btn--primary fe-btn--header">{{ __('frontend.nav.register') }}</a>
      @endauth
    </div>
  </div>
</header>
@endif
