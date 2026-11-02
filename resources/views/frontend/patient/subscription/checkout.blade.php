@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.checkout_title'))
@section('page_title', __('frontend.subscription.checkout_title'))

@section('content')
  @if ($errors->any())
    <div class="fe-alert fe-alert--error">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @php($intent = $intent ?? 'subscribe')
  <div class="fe-cms-card fe-checkout">
    {{-- Dummy payment badge (commented out, not deleted) --}}
    {{-- <div class="fe-checkout__badge">{{ __('frontend.subscription.dummy_badge') }}</div> --}}
    <h2>{{ $plan->name }}</h2>
    <p class="fe-muted">{{ $plan->description }}</p>

    <div class="fe-checkout__summary">
      <div>
        <span class="fe-muted">{{ __('frontend.subscription.price') }}</span>
        <strong>{{ $plan->displayPrice() }} {{ $plan->currency }}</strong>
      </div>
      <div>
        <span class="fe-muted">{{ __('frontend.subscription.billing') }}</span>
        <strong>{{ __('frontend.subscription.intervals.'.$plan->billing_interval) }}</strong>
      </div>
      <div>
        <span class="fe-muted">{{ __('frontend.subscription.exchanges') }}</span>
        <strong>{{ $plan->exchange_quota }}</strong>
      </div>
      <div>
        <span class="fe-muted">{{ __('frontend.subscription.response_time') }}</span>
        <strong>{{ $plan->response_time_label ?: '—' }}</strong>
      </div>
    </div>

    @if ($reason === 'quota')
      <div class="fe-alert fe-alert--warning" style="margin-top:1rem;">
        {{ __('frontend.subscription.quota_exceeded_banner') }}
      </div>
    @endif
    @if (in_array($intent, ['change', 'upgrade', 'downgrade'], true) || in_array($reason, ['change', 'upgrade', 'downgrade'], true))
      <div class="fe-alert fe-alert--warning" style="margin-top:1rem;">
        {{ __('frontend.subscription.change_no_refund') }}
      </div>
    @endif

    {{-- Dummy note (commented out, not deleted) --}}
    {{-- <p class="fe-checkout__note">{{ __('frontend.subscription.dummy_note') }}</p> --}}

    <form action="{{ route('frontend.patient.subscription.pay', $plan) }}" method="post" class="fe-checkout__form">
      @csrf
      <input type="hidden" name="intent" value="{{ $intent }}">
      @if ($returnTo)
        <input type="hidden" name="return" value="{{ $returnTo }}">
      @endif

      <div class="fe-payment-methods" style="margin: 1.5rem 0;">
        <h4 style="margin-bottom: 1rem; font-weight: 600;">{{ __('frontend.subscription.select_payment_method') }}</h4>

        <div class="fe-payment-options" style="display: flex; flex-direction: column; gap: 0.75rem;">
          <label class="fe-payment-option" style="display: flex; align-items: center; padding: 1rem; border: 1px solid var(--fe-border-color, #e2e8f0); border-radius: 8px; cursor: pointer; background: var(--fe-card-bg, #fff);">
            <input type="radio" name="payment_method" value="stripe" checked style="margin-right: 12px; width: 18px; height: 18px; accent-color: var(--fe-primary-color, #4f46e5);">
            <div style="display: flex; flex-direction: column;">
              <span style="font-weight: 600; color: var(--fe-text-color, #1e293b);">{{ __('frontend.subscription.stripe_payment') }}</span>
              <span class="fe-muted" style="font-size: 0.85rem; color: #64748b;">{{ __('frontend.subscription.stripe_payment_desc') }}</span>
            </div>
          </label>

          <label class="fe-payment-option" style="display: flex; align-items: center; padding: 1rem; border: 1px solid var(--fe-border-color, #e2e8f0); border-radius: 8px; cursor: pointer; background: var(--fe-card-bg, #fff);">
            <input type="radio" name="payment_method" value="twint" style="margin-right: 12px; width: 18px; height: 18px; accent-color: var(--fe-primary-color, #4f46e5);">
            <div style="display: flex; flex-direction: column;">
              <span style="font-weight: 600; color: var(--fe-text-color, #1e293b);">{{ __('frontend.subscription.twint_payment') }}</span>
              <span class="fe-muted" style="font-size: 0.85rem; color: #64748b;">{{ __('frontend.subscription.twint_payment_desc') }}</span>
            </div>
          </label>

          {{-- Dummy payment option radio button (commented out, not deleted) --}}
          {{--
          <label class="fe-payment-option" style="display: flex; align-items: center; padding: 1rem; border: 1px solid var(--fe-border-color, #e2e8f0); border-radius: 8px; cursor: pointer; background: var(--fe-card-bg, #fff);">
            <input type="radio" name="payment_method" value="dummy" style="margin-right: 12px; width: 18px; height: 18px;">
            <div style="display: flex; flex-direction: column;">
              <span style="font-weight: 600; color: var(--fe-text-color, #1e293b);">{{ __('frontend.subscription.dummy_payment') }}</span>
              <span class="fe-muted" style="font-size: 0.85rem; color: #64748b;">{{ __('frontend.subscription.dummy_payment_desc') }}</span>
            </div>
          </label>
          --}}
        </div>
      </div>

      {{-- Dummy payment original form submit action (commented out, not deleted) --}}
      {{--
      <form action="{{ route('frontend.patient.subscription.pay', $plan) }}" method="post" class="fe-form-actions">
        @csrf
        <input type="hidden" name="intent" value="{{ $intent }}">
        @if ($returnTo)
          <input type="hidden" name="return" value="{{ $returnTo }}">
        @endif
        <a href="{{ route('frontend.patient.subscription.index') }}" class="fe-btn fe-btn--outline">{{ __('frontend.subscription.back') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">
          {{ __('frontend.subscription.pay_dummy', ['amount' => $plan->displayPrice().' '.$plan->currency]) }}
        </button>
      </form>
      --}}

      <div class="fe-form-actions" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <a href="{{ route('frontend.patient.subscription.index') }}" class="fe-btn fe-btn--outline">{{ __('frontend.subscription.back') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">
          {{ __('frontend.subscription.pay_now', ['amount' => $plan->displayPrice().' '.$plan->currency]) }}
        </button>
      </div>
    </form>
  </div>
@endsection
