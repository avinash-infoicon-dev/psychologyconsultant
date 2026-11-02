@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.all_plans'))
@section('page_title', __('frontend.subscription.all_plans'))

@section('content')
  @if ($quotaExceeded)
    <div class="fe-alert fe-alert--warning">{{ __('frontend.subscription.quota_exceeded_banner') }}</div>
  @endif

  @php
    $planModel = $subscription?->getPlanSnapshotModel() ?? $subscription?->plan;
    $pricingIcons = ['price-icon1.png', 'price-icon2.png', 'price-icon4.png', 'price-icon5.png', 'price-icon3.png'];
  @endphp

  <div class="card h-100 p-0 radius-12 overflow-hidden" id="choose-plans">
    <div class="card-header border-bottom bg-base py-16 px-24">
      <h2 class="h6 mb-0 text-lg">
        @if ($activeSubscription && ($changePlans ?? collect())->isNotEmpty())
          {{ __('frontend.subscription.change_plan_title') }}
        @elseif ($activeSubscription)
          {{ __('frontend.subscription.addons_title') }}
        @else
          {{ __('frontend.subscription.plans_title') }}
        @endif
      </h2>
    </div>
    <div class="card-body p-40">
      <div class="row justify-content-center">
        <div class="col-xxl-10">
          @if ($activeSubscription)
            <div class="text-center">
              <h2 class="h4 mb-16">{{ __('frontend.subscription.change_plan_title') }}</h2>
              <p class="mb-0 text-lg text-secondary-light">{{ __('frontend.subscription.change_plan_help') }}</p>
            </div>

            <div class="row gy-20-px mt-40">
              @if ($planModel)
                <div class="col-xxl-4 col-sm-6">
                  <div class="pricing-plan featured-item position-relative radius-24 overflow-hidden border bg-primary-600 text-white z-1">
                    <img src="{{ asset('commonassets/images/pricing/pricing-shape.png') }}" alt="" class="position-absolute end-0 top-10 z-n1">
                    <span class="bg-white bg-opacity-25 text-white radius-24 py-8 px-24 text-sm position-absolute end-0 top-0 z-1 rounded-start-top-0 rounded-end-bottom-0">{{ $subscription->statusLabel() }}</span>
                    <div class="d-flex align-items-center gap-16">
                      <span class="w-72-px h-72-px d-flex justify-content-center align-items-center radius-16 bg-base">
                        <img src="{{ asset('commonassets/images/pricing/price-icon2.png') }}" alt="">
                      </span>
                      <div>
                        <span class="fw-medium text-md text-white">{{ __('frontend.subscription.types.'.$planModel->plan_type) }}</span>
                        <h2 class="h6 mb-0 text-white">{{ $planModel->name }}</h2>
                      </div>
                    </div>
                    <p class="mt-16 mb-0 text-white mb-28">{{ $planModel->motto ?: $planModel->description }}</p>
                    <h2 class="h3 mb-24 text-white">{{ $planModel->displayPrice() }} {{ $planModel->currency }} <span class="fw-medium text-md text-white">/ {{ __('frontend.subscription.intervals.'.$planModel->billing_interval) }}</span></h2>
                    <span class="mb-20 fw-medium">{{ __('frontend.subscription.included') }}</span>
                    <ul>
                      <li class="d-flex align-items-center gap-16 mb-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-white rounded-circle text-primary-600">
                          <iconify-icon icon="iconamoon:check-light" class="text-lg"></iconify-icon>
                        </span>
                        <span class="text-white text-lg">{{ __('frontend.subscription.exchanges_included', ['count' => $planModel->exchange_quota]) }}</span>
                      </li>
                      <li class="d-flex align-items-center gap-16 mb-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-white rounded-circle text-primary-600">
                          <iconify-icon icon="iconamoon:check-light" class="text-lg"></iconify-icon>
                        </span>
                        <span class="text-white text-lg">{{ $planModel->response_time_label ?: __('frontend.subscription.response_na') }}</span>
                      </li>
                      <li class="d-flex align-items-center gap-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-white rounded-circle text-primary-600">
                          <iconify-icon icon="iconamoon:check-light" class="text-lg"></iconify-icon>
                        </span>
                        <span class="text-white text-lg">
                          @forelse (($planModel->channels ?? []) as $channel)
                            {{ __('frontend.subscription.channels.'.$channel) }}@if (! $loop->last), @endif
                          @empty
                            —
                          @endforelse
                        </span>
                      </li>
                    </ul>
                    <button type="button" disabled class="bg-white text-primary-600 text-center border border-white text-sm btn-sm px-12 py-10 w-100 radius-8 mt-28">{{ $subscription->statusLabel() }}</button>
                  </div>
                </div>
              @endif

              @foreach ($changePlans ?? collect() as $plan)
                @php
                  $direction = $subscription->changeDirection($plan);
                  $icon = $pricingIcons[$loop->index % count($pricingIcons)];
                @endphp
                <div class="col-xxl-4 col-sm-6">
                  <div class="pricing-plan position-relative radius-24 overflow-hidden border bg-base">
                    <div class="d-flex align-items-center gap-16">
                      <span class="w-72-px h-72-px d-flex justify-content-center align-items-center radius-16 bg-primary-50">
                        <img src="{{ asset('commonassets/images/pricing/'.$icon) }}" alt="">
                      </span>
                      <div>
                        <span class="fw-medium text-md text-secondary-light">{{ __('frontend.subscription.types.'.$plan->plan_type) }}</span>
                        <h2 class="h6 mb-0">{{ $plan->name }}</h2>
                      </div>
                    </div>
                    <p class="mt-16 mb-0 text-secondary-light mb-28">{{ $plan->motto ?: $plan->description }}</p>
                    <h2 class="h3 mb-24">{{ $plan->displayPrice() }} {{ $plan->currency }} <span class="fw-medium text-md text-secondary-light">/ {{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</span></h2>
                    <span class="mb-20 fw-medium">{{ __('frontend.subscription.included') }}</span>
                    <ul>
                      <li class="d-flex align-items-center gap-16 mb-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                          <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                        </span>
                        <span class="text-secondary-light text-lg">{{ __('frontend.subscription.exchanges_included', ['count' => $plan->exchange_quota]) }}</span>
                      </li>
                      <li class="d-flex align-items-center gap-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                          <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                        </span>
                        <span class="text-secondary-light text-lg">{{ $plan->response_time_label ?: __('frontend.subscription.response_na') }}</span>
                      </li>
                    </ul>
                    <a href="{{ route('frontend.patient.subscription.checkout', ['plan' => $plan, 'intent' => 'change', 'reason' => $direction]) }}"
                      class="bg-primary-600 bg-hover-primary-700 text-white text-center border border-primary-600 text-sm btn-sm px-12 py-10 w-100 radius-8 mt-28 d-inline-block text-decoration-none">
                      {{ __('frontend.subscription.'.$direction) }}
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center">
              <h2 class="h4 mb-16">{{ __('frontend.subscription.plans_title') }}</h2>
              <p class="mb-0 text-lg text-secondary-light">{{ __('frontend.subscription.plans_subtitle') }}</p>
            </div>
          @endif

          @if ($plans->isNotEmpty())
            @if ($activeSubscription)
              <div class="text-center mt-40">
                <h2 class="h4 mb-16">{{ __('frontend.subscription.addons_title') }}</h2>
                <p class="mb-0 text-lg text-secondary-light">{{ __('frontend.subscription.addons_subtitle') }}</p>
              </div>
            @endif
            <div class="row gy-20-px mt-40">
              @foreach ($plans as $plan)
                @php $icon = $pricingIcons[$loop->index % count($pricingIcons)]; @endphp
                <div class="col-xxl-4 col-sm-6">
                  <div class="pricing-plan position-relative radius-24 overflow-hidden border bg-base {{ $plan->is_recommended ? 'border-primary-600' : '' }}">
                    @if ($plan->is_recommended)
                      <span class="bg-primary-600 text-white radius-24 py-8 px-24 text-sm position-absolute end-0 top-0 z-1">{{ __('frontend.subscription.recommended') }}</span>
                    @endif
                    <div class="d-flex align-items-center gap-16">
                      <span class="w-72-px h-72-px d-flex justify-content-center align-items-center radius-16 bg-primary-50">
                        <img src="{{ asset('commonassets/images/pricing/'.$icon) }}" alt="">
                      </span>
                      <div>
                        <span class="fw-medium text-md text-secondary-light">{{ __('frontend.subscription.types.'.$plan->plan_type) }}</span>
                        <h2 class="h6 mb-0">{{ $plan->name }}</h2>
                      </div>
                    </div>
                    <p class="mt-16 mb-0 text-secondary-light mb-28">{{ $plan->motto ?: $plan->description }}</p>
                    <h2 class="h3 mb-24">{{ $plan->displayPrice() }} {{ $plan->currency }} <span class="fw-medium text-md text-secondary-light">/ {{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</span></h2>
                    <span class="mb-20 fw-medium">{{ __('frontend.subscription.included') }}</span>
                    <ul>
                      <li class="d-flex align-items-center gap-16 mb-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                          <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                        </span>
                        <span class="text-secondary-light text-lg">{{ __('frontend.subscription.exchanges_included', ['count' => $plan->exchange_quota]) }}</span>
                      </li>
                      <li class="d-flex align-items-center gap-16 mb-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                          <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                        </span>
                        <span class="text-secondary-light text-lg">{{ $plan->response_time_label ?: __('frontend.subscription.response_na') }}</span>
                      </li>
                      <li class="d-flex align-items-center gap-16">
                        <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                          <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                        </span>
                        <span class="text-secondary-light text-lg">
                          @forelse (($plan->channels ?? []) as $channel)
                            {{ __('frontend.subscription.channels.'.$channel) }}@if (! $loop->last), @endif
                          @empty
                            —
                          @endforelse
                        </span>
                      </li>
                    </ul>
                    @if (is_array($plan->features) && count($plan->features))
                      <ul class="mt-16">
                        @foreach ($plan->features as $feature)
                          <li class="d-flex align-items-center gap-16 {{ $loop->last ? '' : 'mb-16' }}">
                            <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                              <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                            </span>
                            <span class="text-secondary-light text-lg">{{ $feature }}</span>
                          </li>
                        @endforeach
                      </ul>
                    @endif
                    <a href="{{ route('frontend.patient.subscription.checkout', array_filter(['plan' => $plan, 'intent' => $plan->isPerExchange() ? 'addon' : 'subscribe', 'reason' => $quotaExceeded ? 'quota' : null, 'return' => request('return')])) }}"
                      class="bg-primary-600 bg-hover-primary-700 text-white text-center border border-primary-600 text-sm btn-sm px-12 py-10 w-100 radius-8 mt-28 d-inline-block text-decoration-none">
                      {{ $plan->billing_interval === 'per_exchange' ? __('frontend.subscription.buy') : __('frontend.subscription.subscribe') }}
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
