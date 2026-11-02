@extends('frontend.layouts.guest')

@section('title', __('frontend.brand'))

@section('content')
 {{--<section class="fe-hero">
    <div class="fe-container fe-hero__grid">
      <div>
        <span class="fe-hero__badge"><i class="bi bi-shield-check"></i> {{ __('frontend.home.hero_badge') }}</span>
        <h1>{{ __('frontend.home.hero_title') }}</h1>
        <p>{{ __('frontend.home.hero_text') }}</p>
        <div class="fe-hero__actions">
          <a href="{{ route('frontend.auth.register') }}" class="fe-btn fe-btn--primary">{{ __('frontend.home.hero_cta_primary') }}</a>
          <a href="{{ route('frontend.auth.login') }}" class="fe-btn fe-btn--outline">{{ __('frontend.home.hero_cta_secondary') }}</a>
        </div>
      </div>
      <div class="fe-hero__card">
        <div class="fe-stat-grid">
          <div class="fe-stat"><strong>2.4k+</strong><span>{{ __('frontend.home.stat_patients') }}</span></div>
          <div class="fe-stat"><strong>180+</strong><span>{{ __('frontend.home.stat_psychologists') }}</span></div>
          <div class="fe-stat"><strong>12k+</strong><span>{{ __('frontend.home.stat_sessions') }}</span></div>
          <div class="fe-stat"><strong>98%</strong><span>{{ __('frontend.home.stat_satisfaction') }}</span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="fe-section" id="services">
    <div class="fe-container">
      <div class="fe-section__head">
        <h2>{{ __('frontend.home.services_title') }}</h2>
        <p>{{ __('frontend.home.services_subtitle') }}</p>
      </div>
      <div class="fe-cards">
        <article class="fe-card">
          <div class="fe-card__icon"><i class="bi bi-person-heart"></i></div>
          <h3>{{ __('frontend.home.service_1_title') }}</h3>
          <p>{{ __('frontend.home.service_1_text') }}</p>
        </article>
        <article class="fe-card">
          <div class="fe-card__icon"><i class="bi bi-chat-heart"></i></div>
          <h3>{{ __('frontend.home.service_2_title') }}</h3>
          <p>{{ __('frontend.home.service_2_text') }}</p>
        </article>
        <article class="fe-card">
          <div class="fe-card__icon"><i class="bi bi-credit-card-2-front"></i></div>
          <h3>{{ __('frontend.home.service_3_title') }}</h3>
          <p>{{ __('frontend.home.service_3_text') }}</p>
        </article>
      </div>
    </div>
  </section>

  <section class="fe-container">
    <div class="fe-cta">
      <h2>{{ __('frontend.home.cta_title') }}</h2>
      <p>{{ __('frontend.home.cta_text') }}</p>
      <a href="{{ route('frontend.auth.register') }}" class="fe-btn fe-btn--outline">{{ __('frontend.home.cta_button') }}</a>
    </div>
  </section>--}}

  <main>
  @php($homeSliders = $homeSliders ?? collect())
  @if ($homeSliders->isNotEmpty())
  <section class="tp-showcase-slider">
        <div class="tp-showcase-slider-main p-relative">
            <div class="swiper tp-showcase-slider-active">
                <div class="swiper-wrapper">
                    @foreach ($homeSliders as $slider)
                      @continue(! $slider->image_url)
                      <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ $slider->image_url }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        @if ($slider->subtitle)
                                          <h6 class="tp-showcase-slider-subtitle">{{ $slider->subtitle }}</h6>
                                        @endif
                                        @if ($slider->title)
                                          <h1 class="tp-showcase-slider-title"><a
                                                href="{{ $slider->button_url ?: '#contact-form' }}"><span>{{ $slider->title }}</span></a>
                                          </h1>
                                        @endif
                                        @if ($slider->button_text)
                                          @include('frontend.partials.home-slider-cta', [
                                            #'href' => $slider->button_url,
                                            'href' => route('frontend.meet-psychologists'),
                                            'label' => $slider->button_text,
                                            'filterId' => 'homeSliderFilter'.$slider->id,
                                          ])
                                        @endif 
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    @endforeach
                </div>
                <div class="tp-showcase-slider-button-wrap">
                    <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide">
                        <div><span>{{ __('frontend.home.next_slide') }}</span></div>
                        <div><i class="fas fa-chevron-right"></i></div>
                    </div>
                    <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide">
                        <div><i class="fas fa-chevron-left"></i></div>
                        <div><span>{{ __('frontend.home.prev_slide') }}</span></div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <!-- slider area end  -->
  @endif

  @if (false)
{{-- Previous hardcoded home slider (kept for reference) --}}
  <section class="tp-showcase-slider">
        <div class="tp-showcase-slider-main p-relative">
            <div class="swiper tp-showcase-slider-active">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-1.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        <h6 class="tp-showcase-slider-subtitle">{{ __('frontend.home.slider_1_subtitle') }}</h6>
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_1_title') }}</span></a>
                                        </h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter3">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter3)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.book_consultation') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-2.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        {{--<h6 class="tp-showcase-slider-subtitle">Legal Support</h6>--}}
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_2_title') }}</span></a></h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter4">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter4)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.book_consultation') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-3.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        <h6 class="tp-showcase-slider-subtitle">{{ __('frontend.home.slider_3_subtitle') }}</h6>
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_3_title') }}</span></a>
                                        </h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter5">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter5)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.get_started') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-4.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        <h6 class="tp-showcase-slider-subtitle">{{ __('frontend.home.slider_4_subtitle') }}</h6>
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_4_title') }}</span></a>
                                        </h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter9">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter9)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.book_consultation') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-5.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        <h6 class="tp-showcase-slider-subtitle">{{ __('frontend.home.slider_5_subtitle') }}</h6>
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_5_title') }}</span></a>
                                        </h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter6">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter6)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.learn_more') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="tp-showcase-slider-bg"
                            data-background="{{ asset('frontendassets/assets/img/hero/slider-6.webp') }}"
                            data-swiper-parallax="1152">
                            <div class="container container-1300">
                                <div class="row">
                                    <div class="tp-showcase-slider-content z-index-2">
                                        <h6 class="tp-showcase-slider-subtitle">{{ __('frontend.home.slider_6_subtitle') }}</h6>
                                        <h1 class="tp-showcase-slider-title"><a
                                                href="#contact-form"><span>{{ __('frontend.home.slider_6_title') }}</span></a>
                                        </h1>
                                        <a href="#contact-form"
                                            class="tp-btn-black btn-green-light-bg">
                                            <span class="tp-btn-black-filter-blur">
                                                <svg width="0" height="0">
                                                    <defs>
                                                        <filter id="buttonFilter7">
                                                            <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                                                                result="blur"></feGaussianBlur>
                                                            <feColorMatrix in="blur"
                                                                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                                                            </feColorMatrix>
                                                            <feComposite in="SourceGraphic" in2="buttonFilter3"
                                                                operator="atop"></feComposite>
                                                            <feBlend in="SourceGraphic" in2="buttonFilter3"></feBlend>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </span>
                                            <span class="tp-btn-black-filter d-inline-flex align-items-center"
                                                style="filter: url(#buttonFilter7)">
                                                <span class="tp-btn-black-text">{{ __('frontend.home.book_consultation') }}</span>
                                                <span class="tp-btn-black-circle">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tp-showcase-slider-button-wrap">
                    <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide">
                        <div><span>{{ __('frontend.home.next_slide') }}</span></div>
                        <div><i class="fas fa-chevron-right"></i></div>
                    </div>
                    <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide">
                        <div><i class="fas fa-chevron-left"></i></div>
                        <div><span>{{ __('frontend.home.prev_slide') }}</span></div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <!-- slider area end  -->
  @endif



    <!-- about area start -->
    <div class="cst-about-ptb p-relative pt-170 pb-120">
        <div class="cst-about-shape d-none d-xxl-block" data-speed=".9">
            <img src="{{ asset('frontendassets/assets/img/about/about-shape-1.webp') }}" alt="">
        </div>
        <div class="container container-1530">
            <div class="row">
                <div class="col-xxl-2 d-none d-xxl-block"></div>
                <div class="col-xxl-6 col-lg-6">
                    <div class="cst-about-heading mb-40">
                        <h3 class="cst-section-title fs-32 mb-25 tp_fade_anim" data-delay=".3">
                            {!! __('frontend.home.about_title') !!}
                        </h3>
                        <div class="cst-about-text tp_fade_anim" data-delay=".5">
                            <p class="color-g mb-45">
                                {{ __('frontend.home.about_desc_1') }}
                            </p>
                        </div>
                        <div class="cst-about-list d-block mb-50 tp_fade_anim" data-delay=".7">
                            <ul>
                                <li>
                                    <span><i class="fa-solid fa-check"></i></span>
                                    {{ __('frontend.home.about_list_1') }}
                                </li>

                                <li>
                                    <span><i class="fa-solid fa-check"></i></span>
                                    {{ __('frontend.home.about_list_2') }}
                                </li>

                                <li>
                                    <span><i class="fa-solid fa-check"></i></span>
                                    {{ __('frontend.home.about_list_3') }}
                                </li>

                                <li>
                                    <span><i class="fa-solid fa-check"></i></span>
                                    {{ __('frontend.home.about_list_4') }}
                                </li>
                            </ul>
                        </div>
                        <div class="cst-about-btn tp_fade_anim" data-delay=".7" data-fade-from="top" data-ease="bounce">
                            <a class="cst-btn" href="{{ route('frontend.auth.login') }}">
                                <span>
                                    <span class="text-1">
                                        {{ __('frontend.home.learn_more') }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                    <span class="text-2">
                                        {{ __('frontend.home.learn_more') }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-lg-6">
                    <div class="cst-about-right">
                        <div class="cst-about-text tp_fade_anim" data-delay=".5">
                            <p class="color-g mb-45">
                                {{ __('frontend.home.about_desc_2') }}
                            </p>
                        </div>
                        <div class="cst-about-thumb fix">
                            <div class="tp_img_reveal">
                                <img src="{{ asset('frontendassets/assets/img/about/thumb-1.webp') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about area end -->

    <!-- hero area start -->
    {{--<div class="cst-hero-ptb crp-hero-bg pt-90" data-background="{{ asset('frontendassets/assets/img/hero/hero-thumb-1.webp') }}">
        <div class="container container-1530">
            <div class="row align-items-center">
                <div class="col-xl-6 order-xl-1 order-2">
                    <div class="cst-hero-thumb-wrapper z-index-1">
                        <div class="cst-hero-thumb">
                            <img src="{{ asset('frontendassets/assets/img/hero/hero-thumb-2.webp') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 order-xl-2 order-1">
                    <div class="cst-hero-wrapper p-relative d-md-flex pb-50">
                        <div class="shape-1 tp_fade_anim" data-delay=".3" data-fade-from="top" data-ease="bounce">
                            <img src="{{ asset('frontendassets/assets/img/hero/hero-shape-3.webp') }}" alt="">
                        </div>
                        <div class="cst-hero-left tp_fade_anim" data-delay=".3" data-fade-from="left">
                            <div class="cst-hero-subtitle">
                                SECURE ONLINE CONSULTATION PLATFORM
                            </div>
                        </div>
                        <div class="cst-hero-right">
                            <div class="cst-hero-heading tp_fade_anim" data-delay=".3">
                                <h3 class="cst-hero-title cst-section-title color-white fs-62 mb-45">Your Trusted
                                    Platform for Online Psychology Consultation</h3>
                            </div>
                            <div class="cst-hero-btn-box">
                                <div class="cst-hero-btn tp_fade_anim" data-delay=".5">
                                    <a class="cst-btn" href="#">
                                        <span>
                                            <span class="text-1">
                                                {{ __('frontend.home.get_started') }}
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </span>
                                            <span class="text-2">
                                                {{ __('frontend.home.get_started') }}
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <div class="cst-hero-btn tp_fade_anim" data-delay=".7">
                                    <a class="cst-btn transparent" href="#">
                                        <span>
                                            <span class="text-1">Book a Consultation</span>
                                            <span class="text-2">Book a Consultation</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cst-hero-slider-wrapper">
            <div class="swiper tp-brand-active">
                <div class="swiper-wrapper slide-transtion">

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">{{ __('frontend.home.service_item_1_title') }}</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Consultation</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Secure Messaging</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Voice-to-Text Support</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Audio Consultation</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Secure Document Sharing</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Appointment Management</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Subscription Plans</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">Private & Encrypted Communication</h3>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="cst-hero-item">
                            <h3 class="cst-hero-item-title">{{ __('frontend.home.feature_2_title') }}</h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>--}}
    <!-- hero area end -->


    @include('frontend.partials.feature-area', ['homePage' => $homePage ?? null])
    @if (false)
    {{-- Previous hardcoded feature + video area (kept for reference) --}}
    <!-- feature area start -->
    <div class="cst-feature-ptb pb-120" data-bg-color="#F3F1F2">
        <div class="container container-1530">
            <div class="cst-feature-top">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="cst-feature-item mb-30 tp_fade_anim" data-delay=".3">
                            <div class="cst-feature-item-icon">
                                <span data-bg-color="secondary">
                                    <i class="fa-solid fa-user-secret"></i>
                                </span>
                            </div>
                            <div class="cst-feature-item-content">
                                <h5 class="cst-feature-item-title">{{ __('frontend.home.feature_1_title') }}</h5>

                                <p class="color-g mb-0">
                                    {{ __('frontend.home.feature_1_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="cst-feature-item mb-30 tp_fade_anim" data-delay=".5">
                            <div class="cst-feature-item-icon">
                                <span data-bg-color="black">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </span>
                            </div>
                            <div class="cst-feature-item-content">
                                <h5 class="cst-feature-item-title">{{ __('frontend.home.feature_2_title') }}</h5>

                                <p class="color-g mb-0">
                                    {{ __('frontend.home.feature_2_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="cst-feature-item mb-30 tp_fade_anim" data-delay=".7">
                            <div class="cst-feature-item-icon">
                                <span data-bg-color="primary">
                                    <i class="fa-solid fa-comments"></i>
                                </span>
                            </div>
                            <div class="cst-feature-item-content">
                                <h5 class="cst-feature-item-title">{{ __('frontend.home.feature_3_title') }}</h5>

                                <p class="color-g mb-0">
                                    {{ __('frontend.home.feature_3_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cst-feature-bottom pt-40">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h4 class="cst-feature-text text-center tp_fade_anim" data-delay=".9">
                            {!! __('frontend.home.feature_bottom_text') !!}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- feature area end -->


    <!-- video area start -->
    <div class="cst-video-ptb p-relative pb-160">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-video-thumb text-center">
                        <img class="tp-gsap-image w-100" src="{{ asset('frontendassets/assets/img/video/video-thumb-1.webp') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- video area end -->
    @endif


        @include('frontend.partials.service-area', ['servicesPage' => $servicesPage ?? null])
    @if (false)
    {{-- Previous hardcoded service area (kept for reference) --}}
<!-- service area start -->
    <div class="cst-service-ptb pb-120">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-service-heading text-center mb-60">
                        <span class="cst-section-subtitle mb-15 tp_fade_anim" data-delay=".3">
                            {{ __('frontend.home.service_subtitle') }}
                        </span>
                        <h4 class="cst-section-title tp_fade_anim" data-delay=".5">
                            {!! __('frontend.home.service_title') !!}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="cst-service-item mb-30 tp_fade_anim" data-delay=".3" data-bg-color="secondary">
                        <div class="cst-service-item-content">
                            <div class="cst-service-item-icon">
                                <span class="mb-25">
                                    <i class="fa-solid fa-brain"></i>
                                </span>
                            </div>
                            <h4 class="cst-service-item-title">
                                <a class="tp-line-black" href="#">{{ __('frontend.home.service_item_1_title') }}</a>
                            </h4>
                            <p>{{ __('frontend.home.service_item_1_desc') }}</p>
                        </div>
                        <div class="cst-service-item-thumb">
                            <img src="{{ asset('frontendassets/assets/img/blog/blog-1.webp') }}" alt="">
                        </div>
                    </div>
                </div>
                {{--<div class="col-xl-4 col-md-6">
                    <div class="cst-service-item mb-30 tp_fade_anim" data-delay=".5" data-bg-color="primary">
                        <div class="cst-service-item-content">
                            <div class="cst-service-item-icon">
                                <span class="mb-25">
                                    <i class="fa-solid fa-scale-balanced"></i>
                                </span>
                            </div>
                            <h4 class="cst-service-item-title">
                                <a class="tp-line-black" href="#">Legal Consultation</a>
                            </h4>
                            <p>Receive expert legal advice from experienced professionals for personal, family, and
                                business legal matters.</p>
                        </div>
                        <div class="cst-service-item-thumb">
                            <img src="{{ asset('frontendassets/assets/img/blog/blog-2.webp') }}" alt="">
                        </div>
                    </div>
                </div>--}}
                <div class="col-xl-4 col-md-6">
                    <div class="cst-service-item mb-30 tp_fade_anim" data-delay=".3" data-bg-color="primary">
                        <div class="cst-service-item-content">
                            <div class="cst-service-item-icon">
                                <span class="mb-25">
                                    <i class="fa-solid fa-brain"></i>
                                </span>
                            </div>
                            <h4 class="cst-service-item-title">
                                <a class="tp-line-black" href="#">{{ __('frontend.home.service_item_3_title') }}</a>
                            </h4>
                            <p>{{ __('frontend.home.service_item_3_desc') }}</p>
                        </div>
                        <div class="cst-service-item-thumb">
                            <img src="{{ asset('frontendassets/assets/img/blog/blog-1.webp') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="cst-service-item mb-30 tp_fade_anim" data-delay=".7" data-bg-color="secondary">
                        <div class="cst-service-item-content">
                            <div class="cst-service-item-icon">
                                <span class="mb-25">
                                    <i class="fa-solid fa-comments"></i>
                                </span>
                            </div>
                            <h4 class="cst-service-item-title">
                                <a class="tp-line-black" href="#">{{ __('frontend.home.service_item_2_title') }}</a>
                            </h4>
                            <p>{{ __('frontend.home.service_item_2_desc') }}</p>
                        </div>
                        <div class="cst-service-item-thumb">
                            <img src="{{ asset('frontendassets/assets/img/blog/blog-3.webp') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- service area end -->
    @endif


    <!-- portfolio area start -->
   {{--<div class="cst-portfolio-ptb cst-portfolio-bg pt-120 tp-panel-pin-area" data-bg-color="black">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-6">
                    <div class="cst-portfolio-right tp-panel-pin mb-50">
                        <div class="cst-portfolio-heading z-index-1">
                            <span class="cst-section-subtitle color-white mb-15">
                                Secure. Private. Professional.
                            </span>
                            <h4 class="cst-section-title color-white mb-40">
                                How Our Online <br>
                                Consultation Platform <br>
                                Works
                            </h4>
                            <div class="cst-portfolio-btn tp_fade_anim" data-delay=".5" data-fade-from="top"
                                data-ease="bounce">
                                <a class="cst-btn white-t" href="#">
                                    <span>
                                        <span class="text-1">
                                            Start Your Consultation
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span>

                                        <span class="text-2">
                                            Start Your Consultation
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="cst-portfolio-thumb z-index-1 pt-200 tp_fade_anim" data-delay=".7">
                            <img src="{{ asset('frontendassets/assets/img/portfolio/thumb-1.webp') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cst-portfolio-wrapper">
                        <div class="cst-portfolio-item tp-panel-pin mb-50" data-end="bottom 107%">
                            <span class="cst-portfolio-item-num">
                                01
                            </span>
                            <div class="cst-portfolio-item-thumb">
                                <a href="#">
                                    <img src="{{ asset('frontendassets/assets/img/portfolio/thumb-2.webp') }}" alt="">
                                </a>
                            </div>
                            <h4 class="cst-portfolio-item-title">
                                <a href="#">
                                    Create Your Secure <br>
                                    Account
                                </a>
                            </h4>

                            <div class="cst-portfolio-item-categories">
                                <span>Sign Up</span>
                                <span>Secure Access</span>
                            </div>
                        </div>
                        <div class="cst-portfolio-item tp-panel-pin mb-50" data-end="bottom 107%">
                            <span class="cst-portfolio-item-num">
                                02
                            </span>
                            <div class="cst-portfolio-item-thumb">
                                <a href="#">
                                    <img src="{{ asset('frontendassets/assets/img/portfolio/thumb-3.webp') }}" alt="">
                                </a>
                            </div>
                            <h4 class="cst-portfolio-item-title">
                                <a href="#">
                                    Choose Psychology Consultation <br>
                                </a>
                            </h4>

                            <div class="cst-portfolio-item-categories">
                                <span>{{ __('frontend.home.slider_5_title') }}</span>
                            </div>
                        </div>
                        <div class="cst-portfolio-item tp-panel-pin mb-50" data-end="bottom 107%">
                            <span class="cst-portfolio-item-num">
                                03
                            </span>
                            <div class="cst-portfolio-item-thumb">
                                <a href="#">
                                    <img src="{{ asset('frontendassets/assets/img/portfolio/thumb-4.webp') }}" alt="">
                                </a>
                            </div>
                            <h4 class="cst-portfolio-item-title">
                                <a href="#">
                                    Chat, Voice & Secure <br>
                                    Document Sharing
                                </a>
                            </h4>

                            <div class="cst-portfolio-item-categories">
                                <span>Messaging</span>
                                <span>Voice Support</span>
                            </div>
                        </div>
                        <div class="cst-portfolio-item tp-panel-pin mb-50" data-end="bottom 107%">
                            <span class="cst-portfolio-item-num">
                                04
                            </span>
                            <div class="cst-portfolio-item-thumb">
                                <a href="#">
                                    <img src="{{ asset('frontendassets/assets/img/portfolio/thumb-5.webp') }}" alt="">
                                </a>
                            </div>
                            <h4 class="cst-portfolio-item-title">
                                <a href="#">
                                    Receive Expert Advice <br>
                                    Anytime, Anywhere
                                </a>
                            </h4>

                            <div class="cst-portfolio-item-categories">
                                <span>Professional Care</span>
                                <span>Private Consultation</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}
    <!-- portfolio area end -->


    @include('frontend.partials.sections.team', [
      'teamMembers' => $teamMembers ?? collect(),
      'psychologists' => $psychologists ?? collect(),
      'teamSubtitle' => ($aboutPage ?? null)?->layout('team_subtitle') ?: __('frontend.home.team_subtitle'),
      'teamTitle' => ($aboutPage ?? null)?->layout('team_title') ?: __('frontend.home.team_title'),
      'teamButtonText' => ($aboutPage ?? null)?->layout('team_button_text') ?: __('frontend.home.team_subtitle'),
    ])
  @if (false)
    <!-- team area start -->
    <div class="cst-team-ptb pt-140 pb-80">
        <div class="container container-1530">
            <div class="row align-items-end">
                <div class="col-lg-6">
                    <div class="cst-team-heading mb-50">
                        <span class="cst-section-subtitle mb-15 tp_fade_anim" data-delay=".3">
                            {{ __('frontend.home.team_subtitle') }}
                        </span>
                        <h4 class="cst-section-title tp_fade_anim" data-delay=".5">
                            {!! __('frontend.home.team_title') !!}
                        </h4>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cst-team-btn text-lg-end mb-50 tp_fade_anim" data-delay=".7" data-fade-from="top"
                        data-ease="bounce">
                        <a class="cst-btn" href="#">
                            <span>
                                <span class="text-1">
                                    {{ __('frontend.home.team_subtitle') }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <span class="text-2">
                                    {{ __('frontend.home.team_subtitle') }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="cst-team-box">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".3">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="#">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ asset('frontendassets/assets/img/team/thumb-1.webp') }}" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="#">
                                        Dr. Emma Wilson
                                    </a>
                                </h4>

                                {!! __('frontend.home.team_role_1') !!}
                                <div class="dgm-team-social">
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-twitter"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-linkedin-in"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".4">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="#">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ asset('frontendassets/assets/img/team/thumb-2.webp') }}" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="#">
                                        James Carter
                                    </a>
                                </h4>

                                {!! __('frontend.home.team_role_2') !!}
                                <div class="dgm-team-social">
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-twitter"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-linkedin-in"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".5">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="#">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ asset('frontendassets/assets/img/team/thumb-3.webp') }}" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="#">
                                        Michael Anderson
                                    </a>
                                </h4>

                                {!! __('frontend.home.team_role_3') !!}
                                <div class="dgm-team-social">
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-twitter"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-linkedin-in"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".6">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="#">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ asset('frontendassets/assets/img/team/thumb-4.webp') }}" alt="">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="#">
                                        Sarah Thompson
                                    </a>
                                </h4>

                                {!! __('frontend.home.team_role_4') !!}
                                <div class="dgm-team-social">
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-facebook-f"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-twitter"></i>
                                        </span>
                                    </a>
                                    <a href="#">
                                        <span>
                                            <i class="fa-brands fa-linkedin-in"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- team area end -->
  @endif



    <!-- brand area start -->
    {{--<div class="dgm-brand-area fix">
        <div class="dgm-brand-wrapper cst-border-b">
            <div class="swiper-container dgm-brand-active">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-1.webp') }}" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-2.webp') }}" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-3.webp') }}" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-4.webp') }}" alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-5.webp') }} " alt="">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="dgm-brand-item">
                            <img src="{{ asset('frontendassets/assets/img/brand/brand-6.webp') }} " alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}
    <!-- brand area end -->


    @include('frontend.partials.sections.testimonials', [
      'testimonials' => $testimonials ?? collect(),
      'heading' => ($aboutPage ?? null)?->layout('testimonials_title') ?: __('frontend.home.testimonial_title'),
      'ratingText' => ($aboutPage ?? null)?->layout('testimonials_rating') ?: __('frontend.home.testimonial_subtitle'),
    ])
  @if (false)
    <!-- testimonial area start -->
    <div class="cst-testimonial-ptb pt-110 pb-100" data-bg-color="#F3F1F2">
        <div class="container container-1530">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="cst-testimonial-heading mb-40">
                        <h3 class="cst-section-title">{{ __('frontend.home.testimonial_title') }}</h3>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="tp-testimonial-ratting-box d-flex justify-content-start justify-content-lg-end mb-40">
                        <div class="tp-testimonial-ratting d-inline-flex align-items-center">
                            <span class="mr-15 d-inline-block">
                                <i class="fa-solid fa-award"></i>
                            </span>
                            <div class="tp-testimonial-ratting-info">
                                <div class="tp-testimonial-ratting-icon">
                                    <span>4.9/5</span>
                                    <i>
                                        <i class="fa-solid fa-star"></i>
                                    </i>
                                    <i>
                                        <i class="fa-solid fa-star"></i>
                                    </i>
                                    <i>
                                        <i class="fa-solid fa-star"></i>
                                    </i>
                                    <i>
                                        <i class="fa-solid fa-star"></i>
                                    </i>
                                    <i>
                                        <i class="fa-solid fa-star"></i>
                                    </i>
                                </div>
                                <p>{{ __('frontend.home.testimonial_subtitle') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-testimonial-slider-wrapper">
                        <div class="cst-testimonial-slider swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="cst-testimonial-item">
                                        <div class="cst-testimonial-item-shape">
                                            <i class="fa-solid fa-quote-right"></i>
                                        </div>
                                        <div class="cst-testimonial-item-top">
                                            <div class="cst-testimonial-item-icon">
                                                <span>
                                                    <i class="fa-solid fa-quote-left"></i>
                                                </span>
                                            </div>
                                            <div class="cst-testimonial-item-text">
                                                <p>
                                                    {{ __('frontend.home.testimonial_1_desc') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="cst-testimonial-item-user">
                                            <div class="cst-testimonial-item-user-thumb">
                                                <img src="{{ asset('frontendassets/assets/img/testimonial/user-1.webp') }}" alt="">
                                            </div>
                                            <div class="cst-testimonial-item-user-content">
                                                <h4>Sarah Thompson</h4>

                                                <span>{{ __('frontend.home.testimonial_role_1') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cst-testimonial-item">
                                        <div class="cst-testimonial-item-shape">
                                            <i class="fa-solid fa-quote-right"></i>
                                        </div>
                                        <div class="cst-testimonial-item-top">
                                            <div class="cst-testimonial-item-icon">
                                                <span>
                                                    <i class="fa-solid fa-quote-left"></i>
                                                </span>
                                            </div>
                                            <div class="cst-testimonial-item-text">
                                                <p>
                                                    {{ __('frontend.home.testimonial_2_desc') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="cst-testimonial-item-user">
                                            <div class="cst-testimonial-item-user-thumb">
                                                <img src="{{ asset('frontendassets/assets/img/testimonial/user-2.webp') }}" alt="">
                                            </div>
                                            <div class="cst-testimonial-item-user-content">
                                                <h4>Michael Johnson</h4>

                                                <span>{{ __('frontend.home.testimonial_role_2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cst-testimonial-item">
                                        <div class="cst-testimonial-item-shape">
                                            <i class="fa-solid fa-quote-right"></i>
                                        </div>
                                        <div class="cst-testimonial-item-top">
                                            <div class="cst-testimonial-item-icon">
                                                <span>
                                                    <i class="fa-solid fa-quote-left"></i>
                                                </span>
                                            </div>
                                            <div class="cst-testimonial-item-text">
                                                <p>
                                                    {{ __('frontend.home.testimonial_3_desc') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="cst-testimonial-item-user">
                                            <div class="cst-testimonial-item-user-thumb">
                                                <img src="{{ asset('frontendassets/assets/img/testimonial/user-3.webp') }}" alt="">
                                            </div>
                                            <div class="cst-testimonial-item-user-content">
                                                <h4>Emily Carter</h4>

                                                <span>{{ __('frontend.home.testimonial_role_3') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cst-testimonial-item">
                                        <div class="cst-testimonial-item-shape">
                                            <i class="fa-solid fa-quote-right"></i>
                                        </div>
                                        <div class="cst-testimonial-item-top">
                                            <div class="cst-testimonial-item-icon">
                                                <span>
                                                    <i class="fa-solid fa-quote-left"></i>
                                                </span>
                                            </div>
                                            <div class="cst-testimonial-item-text">
                                                <p>
                                                    {{ __('frontend.home.testimonial_4_desc') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="cst-testimonial-item-user">
                                            <div class="cst-testimonial-item-user-thumb">
                                                <img src="{{ asset('frontendassets/assets/img/testimonial/user-4.webp') }}" alt="">
                                            </div>
                                            <div class="cst-testimonial-item-user-content">
                                                <h4>David Williams</h4>

                                                <span>{{ __('frontend.home.testimonial_role_4') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dgm-testimonial-dot pt-55"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- testimonial area end -->
  @endif



    @include('frontend.partials.sections.faq', [
      'faqs' => $faqs ?? collect(),
      'faqSubtitle' => ($faqPage ?? null)?->layout('faq_subtitle') ?: __('frontend.home.faq_subtitle'),
      'faqTitle' => ($faqPage ?? null)?->layout('faq_title') ?: __('frontend.home.faq_title'),
      'faqButtonText' => ($faqPage ?? null)?->layout('faq_button_text') ?: __('frontend.home.faq_btn'),
      'faqButtonUrl' => ($faqPage ?? null)?->layout('faq_button_url') ?: route('frontend.faq'),
      'accordionId' => 'homeFaqAccordion',
    ])
  @if (false)
    <!-- faq area start -->
    <div class="cst-faq-ptb p-relative pt-140 pb-120">
        <div class="container container-1530">
            <div class="row">

                <div class="col-lg-5">
                    <div class="cst-faq-heading p-relative mb-50">
                        <span class="cst-section-subtitle mb-10 tp_fade_anim" data-delay=".3">{{ __('frontend.home.faq_subtitle') }}</span>
                        <h4 class="cst-section-title mb-50 tp_fade_anim" data-delay=".5">{!! __('frontend.home.faq_title') !!}</h4>
                        <div class="cst-faq-btn tp_fade_anim" data-delay=".7">
                            <a class="cst-btn" href="#">
                                <span>
                                    <span class="text-1">
                                        {{ __('frontend.home.faq_btn') }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                    <span class="text-2">
                                        {{ __('frontend.home.faq_btn') }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="cst-faq-wrap pl-70">
                        <div class="ai-faq-accordion-wrap tp_fade_anim" data-delay=".3" data-fade-from="right" data-ease="bounce">

                            <div class="accordion" id="accordionExample">
                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne">
                                            {{ __('frontend.home.faq_q1') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                            {{ __('frontend.home.faq_q2') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            {{ __('frontend.home.faq_q3') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour">
                                            {{ __('frontend.home.faq_q4') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive">
                                            {{ __('frontend.home.faq_q5') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseFive" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-items">
                                    <h2 class="accordion-header">
                                        <button class="accordion-buttons collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                            aria-expanded="false" aria-controls="collapseSix">
                                            {{ __('frontend.home.faq_q6') }}
                                            <span class="accordion-icon"></span>
                                        </button>
                                    </h2>
                                    <div id="collapseSix" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <p>
                                                {{ __('frontend.home.faq_ans') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- faq area end -->
  @endif


    <?php
    /*
    <!-- cta area start -->
    <div class="cst-cta-ptb">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-cta-wrapper">
                        <div class="cst-cta-thumb">
                            <img src="{{ asset('frontendassets/assets/img/thumb-1.webp') }}" alt="">
                        </div>
                        <div class="cst-cta-content">
                            <span class="cst-section-subtitle mb-10">
                                {{ __('frontend.home.cta_subtitle') }}
                            </span>
                            <h4 class="cst-section-title mb-15">
                                {{ __('frontend.home.cta_title') }}
                            </h4>
                            <p class="cst-cta-content-text">
                                {{ __('frontend.home.cta_desc') }}
                            </p>
                            <div class="cst-cta-input-box">
                                <form id="contact-form" action="/" method="POST">
                                    <div class="row gx-12">
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_name') }}</label>
                                                <input name="name" type="text" placeholder="{{ __('frontend.home.form_name_placeholder') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_email') }}</label>
                                                <input name="email" type="email" placeholder="clikson@gmail.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_phone') }}</label>
                                                <input name="subject" type="text" placeholder="+225 636 956">
                                            </div>
                                        </div>
                                        {{--<div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>Consultation Type</label>
                                                <select name="consultation_type">
                                                    <option value="">Select Consultation</option>
                                                    <option>{{ __('frontend.home.service_item_1_title') }}</option>
                                                    <!-- <option>Legal Consultation</option> -->
                                                </select>
                                            </div>
                                        </div>--}}
                                        <div class="col-md-12">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>Your message</label>
                                                <textarea name="message"
                                                    placeholder="Briefly describe how we can assist you..."></textarea>
                                            </div>
                                            <div class="tp-contact-form-btn">
                                                <button type="submit">
                                                    <span>
                                                        <span class="text-1">{{ __('frontend.home.book_consultation') }}</span>
                                                        <span class="text-2">{{ __('frontend.home.book_consultation') }}</span>
                                                    </span>
                                                </button>
                                                <!-- <p class="ajax-response mt-5"></p> -->
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- cta area end -->
     */
?>
</main>
@endsection
