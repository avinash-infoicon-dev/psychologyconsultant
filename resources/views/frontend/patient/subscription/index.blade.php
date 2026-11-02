@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.title'))
@section('page_title', __('frontend.subscription.title'))

@section('content')
  {{-- Success flash is rendered by frontend.layouts.dashboard --}}

  @if ($quotaExceeded)
    <div class="fe-alert fe-alert--warning">{{ __('frontend.subscription.quota_exceeded_banner') }}</div>
  @endif

  @php
    $balance = $exchangeBalance ?? [];
    $balanceTotal = (int) ($balance['total'] ?? ((int) ($balance['included'] ?? 0) + (int) ($balance['purchased'] ?? 0)));
    // $planModel = $subscription?->plan;
    $planModel = $subscription?->getPlanSnapshotModel() ?? $subscription?->plan;
    $quota = (int) ($planModel?->exchange_quota ?? $balanceTotal);
    $used = (int) ($subscription?->consumed_exchanges ?? ($balance['consumed'] ?? 0));
    $remaining = (int) ($balance['remaining'] ?? $subscription?->remaining_exchanges ?? 0);
    $usagePct = $quota > 0 ? min(100, (int) round(($used / $quota) * 100)) : 0;
    $endsAt = $subscription?->current_period_ends_at;
    $daysLeft = $endsAt ? (int) now()->startOfDay()->diffInDays($endsAt->copy()->startOfDay(), false) : null;
    if ($daysLeft !== null && $daysLeft < 0) {
        $daysLeft = 0;
    }
    $pricingIcons = ['price-icon1.png', 'price-icon2.png', 'price-icon4.png', 'price-icon5.png', 'price-icon3.png'];
  @endphp

  <div class="row gy-20-px mb-24 fe-subscription-kpis">
    <div class="col-xxl-3 col-sm-6">
      <div class="bg-base p-20 radius-16 h-100">
        <div class="d-flex align-items-center gap-16">
          <span class="w-56-px h-56-px bg-success-focus text-success-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
            <i class="ph ph-check-circle"></i>
          </span>
          <div>
            <span class="text-secondary-light text-sm fw-medium">{{ __('frontend.subscription.status') }}</span>
            <h2 class="h6 fw-semibold mb-0">
              @if ($subscription)
                <span class="fe-subscription-status-label is-{{ $subscription->statusTone() }}">{{ $subscription->statusLabel() }}</span>
              @else
                {{ __('frontend.subscription.none') }}
              @endif
            </h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="bg-base p-20 radius-16 h-100">
        <div class="d-flex align-items-center gap-16">
          <span class="w-56-px h-56-px bg-primary-50 text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
            <i class="ph ph-arrows-left-right"></i>
          </span>
          <div>
            <span class="text-secondary-light text-sm fw-medium">{{ __('frontend.subscription.remaining') }}</span>
            <h2 class="h6 fw-semibold mb-0">
              <span data-exchange-remaining>{{ $remaining }}</span> / <span data-exchange-total>{{ $balanceTotal ?: $quota }}</span>
            </h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="bg-base p-20 radius-16 h-100">
        <div class="d-flex align-items-center gap-16">
          <span class="w-56-px h-56-px bg-info-50 text-info-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
            <i class="ph ph-calendar-blank"></i>
          </span>
          <div>
            <span class="text-secondary-light text-sm fw-medium">{{ __('frontend.subscription.renews_on') }}</span>
            <h2 class="h6 fw-semibold mb-0">{{ $subscription?->current_period_ends_at?->format('d M Y') ?? ($balance['renewal_label'] ?? '—') }}</h2>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="bg-base p-20 radius-16 h-100">
        <div class="d-flex align-items-center gap-16">
          <span class="w-56-px h-56-px bg-warning-focus text-warning-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
            <i class="ph ph-hourglass"></i>
          </span>
          <div>
            <span class="text-secondary-light text-sm fw-medium">{{ __('frontend.subscription.period_ends') }}</span>
            <h2 class="h6 fw-semibold mb-0">{{ $daysLeft !== null ? $daysLeft : '—' }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{--<div class="row gy-24" id="current-plan">
    <div class="col-xl-5 mb-3">
      <div class="card radius-16 border-0 overflow-hidden h-100">
        @if ($subscription)
          <div class="bg-primary-600 p-24 text-white">
            @if ($subscription->hasPaymentIssue())
              <div class="bg-white bg-opacity-25 radius-8 p-12 mb-16">
                <p class="mb-8 text-white">{{ __('frontend.subscription.payment_failed_banner') }}</p>
                @if ($subscription->last_payment_error)
                  <p class="mb-8 text-white text-sm">{{ $subscription->last_payment_error }}</p>
                @endif
                @if ($activeSubscription)
                  <form action="{{ route('frontend.patient.subscription.retry-payment') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm radius-8">{{ __('frontend.subscription.retry_payment') }}</button>
                  </form>
                @endif
              </div>
            @endif
            <div class="d-flex align-items-start justify-content-between gap-12 mb-20">
              <div>
                <span class="text-white text-sm fw-medium d-block mb-8">{{ __('frontend.subscription.current_title') }}</span>
                <div class="d-flex align-items-center gap-8 flex-wrap">
                  <h2 class="h4 fw-semibold mb-0 text-white">{{ $planModel?->name }}</h2>
                  @if ($planModel?->is_recommended)
                    <span class="bg-white bg-opacity-25 text-white text-xs fw-medium px-8 py-2 radius-4">{{ __('frontend.subscription.recommended') }}</span>
                  @endif
                </div>
                @if ($planModel?->motto)
                  <span class="text-white text-sm d-block mt-6">{{ $planModel->motto }}</span>
                @endif
              </div>
              <span class="w-56-px h-56-px bg-white text-primary-600 rounded-circle d-flex align-items-center justify-content-center text-2xl flex-shrink-0">
                <i class="ph ph-crown"></i>
              </span>
            </div>
            <h3 class="h3 fw-bold mb-4 text-white">{{ $planModel?->displayPrice() }} {{ $planModel?->currency }}</h3>
            <span class="text-white text-sm">
              @if ($planModel?->billing_interval)
                {{ __('frontend.subscription.intervals.'.$planModel->billing_interval) }}
              @endif
              · {{ __('frontend.subscription.auto_renew') }}
              {{ $subscription->auto_renew ? __('frontend.subscription.yes') : __('frontend.subscription.no') }}
            </span>
          </div>
          <div class="card-body p-24">
            @if ($planModel?->description)
              <p class="text-secondary-light mb-20">{{ $planModel->description }}</p>
            @endif
            <div class="mb-20">
              <div class="d-flex align-items-center justify-content-between mb-8">
                <span class="text-sm fw-medium text-primary-light">{{ __('frontend.subscription.consumed') }}</span>
                <span class="text-sm fw-semibold text-primary-600">{{ $used }} / {{ $quota }}</span>
              </div>
              <div class="progress progress-sm radius-8 overflow-hidden">
                <div class="progress-bar bg-primary-600" role="progressbar" style="width: {{ $usagePct }}%" aria-valuenow="{{ $usagePct }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
            <div class="mb-24">
              <span class="text-sm fw-medium text-primary-light d-block mb-10">{{ __('frontend.subscription.purchased') }}: <strong>{{ (int) ($subscription->purchased_exchanges ?? 0) }}</strong></span>
              <div class="d-flex align-items-center gap-8 flex-wrap">
                @forelse (($planModel?->channels ?? []) as $channel)
                  <span class="bg-primary-50 text-primary-600 border border-primary-200 px-12 py-6 radius-4 fw-medium text-sm">{{ __('frontend.subscription.channels.'.$channel) }}</span>
                @empty
                  <span class="text-secondary-light">—</span>
                @endforelse
              </div>
            </div>
            @if ($subscription->pendingPlan)
              <p class="text-secondary-light mb-16">{{ __('frontend.subscription.pending_plan', ['plan' => $subscription->pendingPlan->name]) }}</p>
            @endif
            <div class="d-flex align-items-center gap-12 flex-wrap">
              @if ($activeSubscription)
                <a href="#choose-plans" class="btn btn-primary text-sm px-20 py-10 radius-8">
                  {{ ($changePlans ?? collect())->isNotEmpty() ? __('frontend.subscription.change_plan_title') : __('frontend.subscription.addons_title') }}
                </a>
              @endif
            </div>
          </div>
        @else
          <div class="card-body p-24">
            <h2 class="h6 fw-semibold mb-12">{{ __('frontend.subscription.current_title') }}</h2>
            <p class="text-secondary-light mb-8">{{ __('frontend.subscription.none') }}</p>
            <p class="text-secondary-light mb-20">{{ __('frontend.subscription.none_help') }}</p>
            <a href="#choose-plans" class="btn btn-primary text-sm px-20 py-10 radius-8">{{ __('frontend.subscription.plans_title') }}</a>
          </div>
        @endif
      </div>
    </div>

    <div class="col-xl-7 mb-3">
      <div class="card radius-16 border-0 h-100">
        <div class="card-body p-24">
          <h3 class="h6 fw-semibold mb-20">{{ __('frontend.subscription.plan') }}</h3>
          <div class="row gy-16">
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.plan') }}</span>
                <span class="fw-semibold text-primary-light">
                  @if ($planModel)
                    {{ __('frontend.subscription.types.'.$planModel->plan_type) }}
                  @else
                    {{ __('frontend.subscription.none') }}
                  @endif
                </span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.billing') }}</span>
                <span class="fw-semibold text-primary-light">
                  @if ($planModel?->billing_interval)
                    {{ __('frontend.subscription.intervals.'.$planModel->billing_interval) }}
                  @else
                    —
                  @endif
                </span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.starts_at') }}</span>
                <span class="fw-semibold text-primary-light">{{ $subscription?->starts_at?->format('d M Y') ?? '—' }}</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.renews_on') }}</span>
                <span class="fw-semibold text-primary-light">{{ $subscription?->current_period_ends_at?->format('d M Y') ?? '—' }}</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.response_time') }}</span>
                <span class="fw-semibold text-primary-light">{{ $planModel?->response_time_label ?: '—' }}</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="bg-neutral-50 radius-12 p-16 h-100">
                <span class="text-sm text-secondary-light d-block mb-4">{{ __('frontend.subscription.price') }}</span>
                <span class="fw-semibold text-primary-light">
                  @if ($planModel)
                    {{ $planModel->displayPrice() }} {{ $planModel->currency }}
                  @else
                    —
                  @endif
                </span>
              </div>
            </div>
          </div>

          <h3 class="h6 fw-semibold mb-16 mt-28">{{ __('frontend.subscription.included') }}</h3>
          <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-center gap-12 mb-12">
              <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
              </span>
              <span class="text-secondary-light">{{ __('frontend.subscription.exchanges_included', ['count' => $quota]) }}</span>
            </li>
            <li class="d-flex align-items-center gap-12 mb-12">
              <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
              </span>
              <span class="text-secondary-light">{{ $planModel?->response_time_label ?: __('frontend.subscription.response_na') }}</span>
            </li>
            <li class="d-flex align-items-center gap-12 mb-12">
              <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
              </span>
              <span class="text-secondary-light">
                @forelse (($planModel?->channels ?? []) as $channel)
                  {{ __('frontend.subscription.channels.'.$channel) }}@if (! $loop->last), @endif
                @empty
                  —
                @endforelse
              </span>
            </li>
            @if (is_array($planModel?->features) && count($planModel->features))
              @foreach ($planModel->features as $feature)
                <li class="d-flex align-items-center gap-12 mb-12">
                  <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                    <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
                  </span>
                  <span class="text-secondary-light">{{ $feature }}</span>
                </li>
              @endforeach
            @endif
            <li class="d-flex align-items-center gap-12">
              <span class="w-24-px h-24-px d-flex justify-content-center align-items-center bg-primary-600 rounded-circle">
                <iconify-icon icon="iconamoon:check-light" class="text-white text-lg"></iconify-icon>
              </span>
              <span class="text-secondary-light">{{ __('frontend.subscription.no_refund_note') }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>--}}

  <div class="row gy-24 mt-0">
    <div class="col-xl-7">
      <div class="card radius-16 border-0">
        <div class="card-header border-bottom bg-base py-16 px-24">
          <h3 class="h6 mb-0">{{ __('frontend.subscription.remaining_headline') }}</h3>
        </div>
        <div class="card-body p-24">
          @include('frontend.patient.partials.exchange-balance', ['exchangeBalance' => $exchangeBalance ?? [], 'compact' => true])
          <div class="table-responsive scroll-sm mt-16">
            <table class="table bordered-table sm-table mb-0">
              <tbody>
                <tr>
                  <td><span class="text-md text-secondary-light">{{ __('frontend.subscription.credit_balance') }}</span></td>
                  <td class="text-end"><strong>{{ $creditBalance }}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card radius-16 border-0">
        <div class="card-header border-bottom bg-base py-16 px-24">
          <h3 class="h6 mb-0">{{ __('frontend.subscription.billing') }}</h3>
        </div>
        <div class="card-body p-24">
          <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
              <tbody>
                <tr>
                  <td><span class="text-md text-secondary-light">{{ __('frontend.subscription.auto_renew') }}</span></td>
                  <td class="text-end"><strong>{{ $subscription?->auto_renew ? __('frontend.subscription.yes') : __('frontend.subscription.no') }}</strong></td>
                </tr>
                <tr>
                  <td><span class="text-md text-secondary-light">{{ __('frontend.subscription.status') }}</span></td>
                  <td class="text-end">
                    @if ($subscription)
                      <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-sm fe-subscription-status-label is-{{ $subscription->statusTone() }}">{{ $subscription->statusLabel() }}</span>
                    @else
                      <span class="text-secondary-light">{{ __('frontend.subscription.none') }}</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          @if ($activeSubscription)
            <div class="d-flex align-items-center gap-12 flex-wrap mt-20">
              <form action="{{ route('frontend.patient.subscription.auto-renew') }}" method="post" class="m-0">
                @csrf
                <input type="hidden" name="auto_renew" value="{{ $subscription->auto_renew ? 0 : 1 }}">
                <button type="submit" class="border border-neutral-400 text-secondary-light text-sm px-16 py-8 radius-8 bg-transparent">
                  {{ $subscription->auto_renew ? __('frontend.subscription.disable_auto_renew') : __('frontend.subscription.enable_auto_renew') }}
                </button>
              </form>
              <form action="{{ route('frontend.patient.subscription.cancel') }}" method="post" class="m-0" onsubmit="return confirm(@json(__('frontend.subscription.cancel_confirm')))">
                @csrf
                <button type="submit" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-sm px-16 py-8 radius-8 bg-transparent">{{ __('frontend.subscription.cancel') }}</button>
              </form>
            </div>
            <p class="text-secondary-light text-sm mt-16 mb-0">{{ __('frontend.subscription.no_refund_note') }}</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card h-100 p-0 radius-12 overflow-hidden mt-24" id="choose-plans">
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

  {{--<div class="row gy-24 mt-24" id="subscription-history">
    <div class="col-xl-6">
      <div class="card radius-16 border-0 h-100">
        <div class="card-header border-bottom bg-base py-16 px-24">
          <h3 class="h6 mb-0">{{ __('frontend.subscription.subscriptions_history') }}</h3>
        </div>
        <div class="card-body p-24">
          <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
              <thead>
                <tr>
                  <th>{{ __('frontend.subscription.sub_id') }}</th>
                  <th>{{ __('frontend.subscription.plan') }}</th>
                  <th>{{ __('frontend.subscription.price') }}</th>
                  <th>{{ __('frontend.subscription.starts_at') }}</th>
                  <th>{{ __('frontend.subscription.status') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($subscriptionsHistory as $subItem)
                  @php
                    $snapshotPlan = $subItem->getPlanSnapshotModel();
                  @endphp
                  <tr>
                    <td><strong class="text-primary-light">#SUB-{{ $subItem->id }}</strong></td>
                    <td>
                      <span class="fw-semibold text-primary-light d-block">{{ $snapshotPlan?->name ?? '—' }}</span>
                      @if ($snapshotPlan?->billing_interval)
                        <span class="text-xs text-secondary-light">{{ __('frontend.subscription.intervals.'.$snapshotPlan->billing_interval) }}</span>
                      @endif
                    </td>
                    <td>
                      @if ($snapshotPlan)
                        {{ $snapshotPlan->displayPrice() }} {{ $snapshotPlan->currency }}
                      @else
                        —
                      @endif
                    </td>
                    <td>{{ $subItem->starts_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                      <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-sm fe-subscription-status-label is-{{ $subItem->statusTone() }}">{{ $subItem->statusLabel() }}</span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-secondary-light py-20">{{ __('frontend.subscription.no_subscriptions_history') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card radius-16 border-0 h-100">
        <div class="card-header border-bottom bg-base py-16 px-24">
          <h3 class="h6 mb-0">{{ __('frontend.subscription.exchanges_history') }}</h3>
        </div>
        <div class="card-body p-24">
          <div class="table-responsive scroll-sm">
            <table class="table bordered-table sm-table mb-0">
              <thead>
                <tr>
                  <th>{{ __('frontend.subscription.exchange_id') }}</th>
                  <th>{{ __('frontend.subscription.date') }}</th>
                  <th>{{ __('frontend.subscription.psychologist') }}</th>
                  <th>{{ __('frontend.subscription.messages_used') }}</th>
                  <th>{{ __('frontend.subscription.status') }}</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($exchangesHistory as $excItem)
                  <tr>
                    <td><strong class="text-primary-light">#EXC-{{ $excItem->id }}</strong></td>
                    <td>{{ $excItem->created_at?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $excItem->psychologist?->user?->name ?? '—' }}</td>
                    <td>{{ (int) $excItem->patient_messages_used + (int) $excItem->psychologist_messages_used }}</td>
                    <td>
                      @if ($excItem->isCompleted())
                        <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-sm">{{ __('frontend.subscription.completed') }}</span>
                      @elseif ($excItem->isPending())
                        <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-4 radius-4 fw-medium text-sm">{{ __('frontend.subscription.pending') }}</span>
                      @else
                        <span class="bg-neutral-100 text-neutral-600 border border-neutral-300 px-12 py-4 radius-4 fw-medium text-sm">{{ $excItem->status }}</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-secondary-light py-20">{{ __('frontend.subscription.no_exchanges_history') }}</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>--}}

  @if (false)

  {{-- Success flash is rendered by frontend.layouts.dashboard --}}
  {{-- Old: duplicate session error / $errors here. Layout already shows them. --}}

  @if ($quotaExceeded)
    <div class="fe-alert fe-alert--warning">
      {{ __('frontend.subscription.quota_exceeded_banner') }}
    </div>
  @endif

  <div class="fe-cms-card fe-subscription-status">
    <h2>{{ __('frontend.subscription.current_title') }}</h2>
    @if ($subscription)
      @if ($subscription->hasPaymentIssue())
        <div class="fe-alert fe-alert--warning">
          {{ __('frontend.subscription.payment_failed_banner') }}
          @if ($subscription->last_payment_error)
            <div class="fe-muted" style="margin-top:.35rem;">{{ $subscription->last_payment_error }}</div>
          @endif
          @if ($activeSubscription)
            <form action="{{ route('frontend.patient.subscription.retry-payment') }}" method="post" style="margin-top:.75rem;">
              @csrf
              <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.subscription.retry_payment') }}</button>
            </form>
          @endif
        </div>
      @endif

      <div class="fe-subscription-status__grid">
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.plan') }}</span>
          <strong>{{ $subscription->plan?->name }}</strong>
          @if ($subscription->plan?->is_recommended)
            <span class="fe-badge">{{ __('frontend.subscription.recommended') }}</span>
          @endif
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.price') }}</span>
          <strong>
            {{ $subscription->plan?->displayPrice() }} {{ $subscription->plan?->currency }}
            @if ($subscription->plan?->billing_interval)
              / {{ __('frontend.subscription.intervals.'.$subscription->plan->billing_interval) }}
            @endif
          </strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.status') }}</span>
          {{-- Legacy always-active label:
          <strong class="fe-text-success">{{ __('frontend.subscription.status_active') }}</strong>
          --}}
          <strong class="fe-subscription-status-label is-{{ $subscription->statusTone() }}">
            {{ $subscription->statusLabel() }}
          </strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.starts_at') }}</span>
          <strong>{{ $subscription->starts_at?->format('d M Y') ?? '—' }}</strong>
        </div>
        {{-- Legacy remaining / quota only:
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.exchanges') }}</span>
          <strong>{{ $subscription->exchanges_remaining }} / {{ $subscription->plan?->exchange_quota }}</strong>
        </div>
        --}}
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.renews_on') }}</span>
          <strong>{{ $subscription->current_period_ends_at?->format('d M Y') ?? '—' }}</strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.consumed') }}</span>
          <strong>{{ (int) $subscription->consumed_exchanges }}</strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.remaining') }}</span>
          <strong>{{ (int) $subscription->remaining_exchanges }}</strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.purchased') }}</span>
          <strong>{{ (int) $subscription->purchased_exchanges }}</strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.response_time') }}</span>
          <strong>{{ $subscription->plan?->response_time_label ?: '—' }}</strong>
        </div>
        <div>
          <span class="fe-muted">{{ __('frontend.subscription.auto_renew') }}</span>
          <strong>{{ $subscription->auto_renew ? __('frontend.subscription.yes') : __('frontend.subscription.no') }}</strong>
        </div>
      </div>

      @if ($subscription->pendingPlan)
        <p class="fe-muted">{{ __('frontend.subscription.pending_plan', ['plan' => $subscription->pendingPlan->name]) }}</p>
      @endif

      @if ($activeSubscription)
        <div class="fe-subscription-actions">
          <form action="{{ route('frontend.patient.subscription.auto-renew') }}" method="post">
            @csrf
            <input type="hidden" name="auto_renew" value="{{ $subscription->auto_renew ? 0 : 1 }}">
            <button type="submit" class="fe-btn fe-btn--outline">
              {{ $subscription->auto_renew ? __('frontend.subscription.disable_auto_renew') : __('frontend.subscription.enable_auto_renew') }}
            </button>
          </form>
          <form action="{{ route('frontend.patient.subscription.cancel') }}" method="post" onsubmit="return confirm(@json(__('frontend.subscription.cancel_confirm')))">
            @csrf
            <button type="submit" class="fe-btn fe-btn--outline">{{ __('frontend.subscription.cancel') }}</button>
          </form>
        </div>
        <p class="fe-muted fe-subscription-note">{{ __('frontend.subscription.no_refund_note') }}</p>
      @endif
    @else
      <p class="fe-muted" style="margin:0;">{{ __('frontend.subscription.none') }}</p>
      <p style="margin:.5rem 0 0;">{{ __('frontend.subscription.none_help') }}</p>
    @endif

    @include('frontend.patient.partials.exchange-balance', ['exchangeBalance' => $exchangeBalance ?? []])

    <div class="fe-subscription-credits">
      <span class="fe-muted">{{ __('frontend.subscription.credit_balance') }}</span>
      <strong>{{ $creditBalance }}</strong>
    </div>
  </div>

  @if ($activeSubscription && ($changePlans ?? collect())->isNotEmpty())
    <h2 class="fe-section-title">{{ __('frontend.subscription.change_plan_title') }}</h2>
    <p class="fe-muted" style="margin-top:0;">{{ __('frontend.subscription.change_plan_help') }}</p>
    <div class="fe-plan-grid">
      @foreach ($changePlans as $plan)
        @php $direction = $subscription->changeDirection($plan); @endphp
        <article class="fe-plan-card">
          <h3>{{ $plan->name }}</h3>
          <p class="fe-plan-card__price">
            <strong>{{ $plan->displayPrice() }}</strong>
            <span>{{ $plan->currency }} / {{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</span>
          </p>
          <ul class="fe-plan-card__meta">
            <li>{{ __('frontend.subscription.exchanges_included', ['count' => $plan->exchange_quota]) }}</li>
          </ul>
          <a
            href="{{ route('frontend.patient.subscription.checkout', [
              'plan' => $plan,
              'intent' => 'change',
              'reason' => $direction,
            ]) }}"
            class="fe-btn fe-btn--primary fe-btn--block"
          >
            {{ __('frontend.subscription.'.$direction) }}
          </a>
        </article>
      @endforeach
    </div>
  @endif

  <h2 class="fe-section-title">
    {{ $activeSubscription ? __('frontend.subscription.addons_title') : __('frontend.subscription.plans_title') }}
  </h2>
  <p class="fe-muted" style="margin-top:0;">
    {{ $activeSubscription ? __('frontend.subscription.addons_subtitle') : __('frontend.subscription.plans_subtitle') }}
  </p>

  <div class="fe-plan-grid">
    @foreach ($plans as $plan)
      <article class="fe-plan-card {{ $plan->is_recommended ? 'is-recommended' : '' }}">
        @if ($plan->is_recommended)
          <div class="fe-plan-card__ribbon">{{ __('frontend.subscription.recommended') }}</div>
        @endif
        <h3>{{ $plan->name }}</h3>
        @if ($plan->motto)
          <p class="fe-plan-card__motto fe-muted">{{ $plan->motto }}</p>
        @endif
        <p class="fe-plan-card__type">{{ __('frontend.subscription.types.'.$plan->plan_type) }}</p>
        <p class="fe-plan-card__price">
          <strong>{{ $plan->displayPrice() }}</strong>
          <span>{{ $plan->currency }} / {{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</span>
        </p>
        <ul class="fe-plan-card__meta">
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
        @if (is_array($plan->features) && count($plan->features))
          <ul class="fe-plan-card__features">
            @foreach ($plan->features as $feature)
              <li>{{ $feature }}</li>
            @endforeach
          </ul>
        @endif
        <a
          href="{{ route('frontend.patient.subscription.checkout', array_filter([
              'plan' => $plan,
              'intent' => $plan->isPerExchange() ? 'addon' : 'subscribe',
              'reason' => $quotaExceeded ? 'quota' : null,
              'return' => request('return'),
          ])) }}"
          class="fe-btn fe-btn--primary fe-btn--block"
        >
          {{ $plan->billing_interval === 'per_exchange' ? __('frontend.subscription.buy') : __('frontend.subscription.subscribe') }}
        </a>
      </article>
    @endforeach
  </div>

  @endif
@endsection
