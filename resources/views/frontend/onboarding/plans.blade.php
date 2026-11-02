@extends('frontend.layouts.guest')

@section('title', __('frontend.onboarding.page_title'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontendassets/css/onboarding.css') }}">
@endpush

@section('content')
  @php
    $heroBg = \App\Support\PublicMedia::url(\App\Support\PublicMedia::aboutDefault('hero-bg.webp'))
      ?: asset('frontendassets/assets/img/hero/hero-bg.webp');
  @endphp
  <div class="tp-hero-2-wrapper onboarding-hero">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle">{{ __('frontend.onboarding.plans_subtitle') }}</span>
                         <h1 class="tp-hero-2-title text-scale-anim font-bold">{{ __('frontend.onboarding.page_title') }}</h1>
                     </div>
                 </div>
             </div>
         </div>
     </div>
  </div>

  <main class="onboarding-page pb-120">
    <div class="container container-1530">
      <p class="onboarding-intro">{{ __('frontend.onboarding.intro') }}</p>

      {{-- Journey steps stay in the language files. They describe the flow and are not shown on the page.
      <ol class="onboarding-steps">
        @foreach (__('frontend.onboarding.steps') as $step)
          <li>{{ $step }}</li>
        @endforeach
      </ol>
      --}}

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
              <div class="onboarding-plan onboarding-plan--{{ $variant }}">
                @if ($plan->is_recommended)
                  <span class="onboarding-plan__badge">{{ __('frontend.onboarding.popular') }}</span>
                @endif
                <div class="onboarding-plan__head">
                  <span class="onboarding-plan__icon">
                    <img src="{{ asset('commonassets/images/pricing/'.$icon) }}" alt="">
                  </span>
                  <div>
                    <span class="onboarding-plan__type">{{ __('frontend.subscription.types.'.$plan->plan_type) }}</span>
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
  </main>
@endsection
