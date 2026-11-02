@php
  $balance = $exchangeBalance ?? [
      'included' => 0,
      'consumed' => 0,
      'purchased' => 0,
      'remaining' => 0,
      'total' => 0,
      'renewal_date' => null,
      'renewal_label' => null,
      'has_subscription' => false,
  ];
  $total = (int) ($balance['total'] ?? ((int) $balance['included'] + (int) $balance['purchased']));
@endphp

<section
  class="fe-exchange-balance {{ ($compact ?? false) ? 'is-compact' : '' }} {{ (int) $balance['remaining'] <= 2 ? 'is-low' : '' }}"
  data-exchange-balance
>
  <p class="fe-exchange-balance__headline">
    <strong data-exchange-remaining>{{ (int) $balance['remaining'] }}</strong>
    /
    <span data-exchange-total>{{ $total }}</span>
    {{ __('frontend.subscription.remaining_headline') }}
  </p>
  <ul class="fe-exchange-balance__breakdown">
    <li>
      <span>{{ __('frontend.subscription.included') }}</span>
      <strong data-exchange-included-value>{{ (int) $balance['included'] }}</strong>
    </li>
    <li>
      <span>{{ __('frontend.subscription.purchased') }}</span>
      <strong data-exchange-purchased>+{{ (int) $balance['purchased'] }}</strong>
    </li>
    <li>
      <span>{{ __('frontend.subscription.consumed') }}</span>
      <strong data-exchange-consumed>-{{ (int) $balance['consumed'] }}</strong>
    </li>
  </ul>
  <p class="fe-exchange-balance__renewal" data-exchange-renewal-wrap @if (! ($balance['renewal_label'] ?? null)) hidden @endif>
    {{ __('frontend.subscription.renews_on') }}
    <time data-exchange-renewal datetime="{{ $balance['renewal_date'] }}">{{ $balance['renewal_label'] }}</time>
  </p>
  <p class="fe-exchange-balance__hint" data-exchange-low-hint @if ((int) $balance['remaining'] > 2) hidden @endif>
    {{ __('frontend.subscription.low_balance_hint') }}
  </p>
</section>
