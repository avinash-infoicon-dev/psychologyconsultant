@php
  $homePage = $homePage ?? null;
  $featureBg = $homePage?->layout('feature_bg_color') ?: '#F3F1F2';
  $videoImg = $homePage?->layoutUrl('video_image')
      ?: asset('frontendassets/assets/img/video/video-thumb-1.webp');
  $defaultIcons = [
    1 => 'fa-solid fa-user-secret',
    2 => 'fa-solid fa-user-doctor',
    3 => 'fa-solid fa-comments',
  ];
  $defaultBgs = [
    1 => 'secondary',
    2 => 'black',
    3 => 'primary',
  ];
  $defaultDelays = [
    1 => '.3',
    2 => '.5',
    3 => '.7',
  ];
  $features = [];
  foreach ([1, 2, 3] as $n) {
      $features[] = [
          'title' => $homePage?->layout('feature_'.$n.'_title') ?: __('frontend.home.feature_'.$n.'_title'),
          'desc' => $homePage?->layout('feature_'.$n.'_desc') ?: __('frontend.home.feature_'.$n.'_desc'),
          'icon' => $homePage?->layout('feature_'.$n.'_icon') ?: $defaultIcons[$n],
          'bg' => $homePage?->layout('feature_'.$n.'_bg') ?: $defaultBgs[$n],
          'delay' => $defaultDelays[$n],
      ];
  }
@endphp
<!-- feature area start -->
    <div class="cst-feature-ptb pb-120" data-bg-color="{{ $featureBg }}">
        <div class="container container-1530">
            <div class="cst-feature-top">
                <div class="row">
                    @foreach ($features as $feature)
                    <div class="col-lg-4 col-md-6">
                        <div class="cst-feature-item mb-30 tp_fade_anim" data-delay="{{ $feature['delay'] }}">
                            <div class="cst-feature-item-icon">
                                <span data-bg-color="{{ $feature['bg'] }}">
                                    <i class="{{ $feature['icon'] }}"></i>
                                </span>
                            </div>
                            <div class="cst-feature-item-content">
                                <h5 class="cst-feature-item-title">{{ $feature['title'] }}</h5>

                                <p class="color-g mb-0">
                                    {{ $feature['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="cst-feature-bottom pt-40">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h4 class="cst-feature-text text-center tp_fade_anim" data-delay=".9">
                            {!! $homePage?->layout('feature_bottom_text') ?: __('frontend.home.feature_bottom_text') !!}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- feature area end -->

    @push('styles')
      <link rel="stylesheet" href="{{ asset('frontendassets/css/onboarding.css') }}">
    @endpush

    @php
      $plans = $plans ?? collect();
    @endphp
    <section class="onboarding-page home-plans pb-120">
    <div class="container container-1530">
      <p class="onboarding-intro">{{ __('frontend.onboarding.intro') }}</p>

      @if (session('onboarding_notice') || $errors->any())
        <p class="onboarding-notice">{{ session('onboarding_notice') ?: $errors->first() }}</p>
      @endif

      <div class="text-center onboarding-heading">
        <h2>{{ __('frontend.onboarding.plans_title') }}</h2>
        <p>{{ __('frontend.onboarding.plans_subtitle') }}</p>
      </div>

      @if ($plans->isEmpty())
        <p class="text-center">{{ __('frontend.onboarding.empty') }}</p>
      @else
        <div class="row gy-4 justify-content-center">
          @foreach ($plans as $plan)
            @php
              $variant = $loop->index % 3;
              $icons = ['price-icon1.png', 'price-icon2.png', 'price-icon3.png'];
              $icon = $icons[$variant];
            @endphp
            <div class="col-xxl-4 col-md-6">
              <div class="onboarding-plan onboarding-plan--1">
                @if ($plan->is_recommended)
                  <span class="onboarding-plan__badge">{{ __('frontend.onboarding.popular') }}</span>
                @endif
                <div class="onboarding-plan__head">
                  <span class="onboarding-plan__icon">
                    <img src="{{ asset('commonassets/images/pricing/'.$icon) }}" alt="">
                  </span>
                  <div>
                    <!-- <span class="onboarding-plan__type">{{ __('frontend.subscription.types.'.$plan->plan_type) }}</span> -->
                    <h3>{{ $plan->name }}</h3>
                  </div>
                </div>
                <p class="onboarding-plan__text">{{ $plan->motto ?: $plan->description }}</p>
                <p class="onboarding-plan__price">
                  {{ $plan->displayPrice() }} {{ $plan->currency }}
                  <span>/ {{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</span>
                </p>
                <span class="onboarding-plan__included">{{ __('frontend.onboarding.included') }}</span>
                <ul>
                  <li>{{ __('frontend.subscription.exchanges_included', ['count' => $plan->exchange_quota]) }}</li>
                  <li>{{ $plan->response_time_label ?: __('frontend.subscription.response_na') }}</li>
                  <li>
                    @forelse (($plan->channels ?? []) as $channel)
                      {{ __('frontend.subscription.channels.'.$channel) }}@if (! $loop->last), @endif
                    @empty
                      —
                    @endforelse
                  </li>
                </ul>
                <a class="onboarding-plan__btn" href="{{ route('frontend.onboarding.account', $plan) }}">
                  {{ __('frontend.onboarding.get_started') }}
                </a>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
    </section>

    <!-- video area start -->
    <div class="cst-video-ptb p-relative pb-120 d-none">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-video-thumb text-center">
                        <img class="tp-gsap-image w-100" src="{{ $videoImg }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- video area end -->
