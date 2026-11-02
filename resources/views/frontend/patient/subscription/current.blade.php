@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.current_plan'))
@section('page_title', __('frontend.subscription.current_plan'))

@section('content')
  @php
    $balance = $exchangeBalance ?? [];
    $balanceTotal = (int) ($balance['total'] ?? ((int) ($balance['included'] ?? 0) + (int) ($balance['purchased'] ?? 0)));
    // $planModel = $subscription?->plan;
    $planModel = $subscription?->getPlanSnapshotModel() ?? $subscription?->plan;
    $quota = (int) ($planModel?->exchange_quota ?? $balanceTotal);
    $used = (int) ($subscription?->consumed_exchanges ?? ($balance['consumed'] ?? 0));
    $remaining = (int) ($balance['remaining'] ?? $subscription?->remaining_exchanges ?? 0);
    $usagePct = $quota > 0 ? min(100, (int) round(($used / $quota) * 100)) : 0;
  @endphp

  <div class="row gy-24">
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
                <a href="{{ route('frontend.patient.subscription.plans') }}" class="btn btn-primary text-sm px-20 py-10 radius-8">
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
            <a href="{{ route('frontend.patient.subscription.plans') }}" class="btn btn-primary text-sm px-20 py-10 radius-8">{{ __('frontend.subscription.plans_title') }}</a>
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
  </div>
@endsection
