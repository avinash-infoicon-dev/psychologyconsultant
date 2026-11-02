<div class="row">
  @php
    $planModel = $plan ?? null;
    $locales = $locales ?? \App\Support\Locales::codes();
    $selectedChannels = old('channels', $planModel?->channels ?? ['text', 'dictation']);
    $selectedChannels = is_array($selectedChannels) ? $selectedChannels : [];
    $hasTranslations = (bool) $planModel?->translations?->isNotEmpty();
  @endphp
  {{-- Legacy single-language name (replaced by locale tabs):
  <div class="col-md-6 mb-3">
    <label class="form-label" for="name">{{ __('backend.name') }}</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $planModel?->name) }}" required>
  </div>
  --}}
  <div class="col-md-6 mb-3">
    <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $planModel?->slug) }}">
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="plan_type">{{ __('backend.plans.plan_type') }}</label>
    <select name="plan_type" id="plan_type" class="form-select" required>
      @foreach (['subscription', 'on_demand', 'addon'] as $type)
        <option value="{{ $type }}" @selected(old('plan_type', $planModel?->plan_type ?? 'subscription') === $type)>
          {{ __('backend.plans.types.'.$type) }}
        </option>
      @endforeach
    </select>
    <div class="form-text">{{ __('backend.plans.plan_type_help') }}</div>
  </div>
  {{-- Legacy single-language description / motto / features / response label:
  <div class="col-12 mb-3">
    <label class="form-label" for="description">{{ __('backend.description') }}</label>
    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $planModel?->description) }}</textarea>
  </div>
  <div class="col-12 mb-3">
    <label class="form-label" for="motto">{{ __('backend.plans.motto') }}</label>
    <input type="text" name="motto" id="motto" class="form-control" maxlength="255"
      value="{{ old('motto', $planModel?->motto) }}"
      placeholder="{{ __('backend.plans.motto_placeholder') }}">
  </div>
  --}}
  @if (! $planModel)
  <div class="col-md-4 mb-3">
    <label class="form-label" for="price_amount">{{ __('backend.plans.price') }}</label>
    <input type="number" step="0.01" min="0" name="price_amount" id="price_amount" class="form-control"
      value="{{ old('price_amount', $planModel ? $planModel->displayPrice() : '') }}" required>
    <div class="form-text">{{ __('backend.plans.price_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="currency">{{ __('backend.plans.currency') }}</label>
    <input type="text" name="currency" id="currency" class="form-control" maxlength="3"
      value="{{ old('currency', $planModel?->currency ?? 'CHF') }}" required>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="billing_interval">{{ __('backend.plans.billing_interval') }}</label>
    {{-- Legacy: always listed monthly, quarterly, yearly, per_exchange together
    <select name="billing_interval" id="billing_interval" class="form-select" required>
      @foreach (['monthly', 'quarterly', 'yearly', 'per_exchange'] as $interval)
        <option value="{{ $interval }}">...</option>
      @endforeach
    </select>
    --}}
    <select name="billing_interval" id="billing_interval" class="form-select" required data-plan-billing-interval>
      @foreach ([
          'monthly' => 'subscription',
          'quarterly' => 'subscription',
          'yearly' => 'subscription',
          'per_exchange' => 'per_exchange',
      ] as $interval => $group)
        <option
          value="{{ $interval }}"
          data-interval-group="{{ $group }}"
          @selected(old('billing_interval', $planModel?->billing_interval ?? 'monthly') === $interval)
        >
          {{ __('backend.plans.intervals.'.$interval) }}
        </option>
      @endforeach
    </select>
    <div class="form-text">{{ __('backend.plans.billing_interval_help') }}</div>
  </div>
  @else
  {{-- Edit: price, currency and billing frequency are locked (Stripe prices cannot be changed).
  <div class="col-md-4 mb-3">
    <label class="form-label" for="price_amount">{{ __('backend.plans.price') }}</label>
    <input type="number" step="0.01" min="0" name="price_amount" id="price_amount" class="form-control"
      value="{{ old('price_amount', $planModel ? $planModel->displayPrice() : '') }}" required>
    <div class="form-text">{{ __('backend.plans.price_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="currency">{{ __('backend.plans.currency') }}</label>
    <input type="text" name="currency" id="currency" class="form-control" maxlength="3"
      value="{{ old('currency', $planModel?->currency ?? 'CHF') }}" required>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="billing_interval">{{ __('backend.plans.billing_interval') }}</label>
    <select name="billing_interval" id="billing_interval" class="form-select" required data-plan-billing-interval>
      ...
    </select>
  </div>
  --}}
  <input type="hidden" name="price_amount" value="{{ old('price_amount', $planModel->displayPrice()) }}">
  <input type="hidden" name="currency" value="{{ old('currency', $planModel->currency) }}">
  <input type="hidden" name="billing_interval" value="{{ old('billing_interval', $planModel->billing_interval) }}">
  @endif
  <div class="col-md-4 mb-3">
    <label class="form-label" for="exchange_quota">{{ __('backend.plans.exchange_quota') }}</label>
    <input type="number" min="1" name="exchange_quota" id="exchange_quota" class="form-control"
      value="{{ old('exchange_quota', $planModel?->exchange_quota ?? 1) }}" required>
    <div class="form-text">{{ __('backend.plans.exchange_quota_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="max_text_chars">{{ __('backend.plans.max_text_chars') }}</label>
    <input type="number" min="0" name="max_text_chars" id="max_text_chars" class="form-control"
      value="{{ old('max_text_chars', $planModel?->max_text_chars) }}">
    <div class="form-text">{{ __('backend.plans.max_text_chars_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="max_audio_seconds">{{ __('backend.plans.max_audio_seconds') }}</label>
    <input type="number" min="0" name="max_audio_seconds" id="max_audio_seconds" class="form-control"
      value="{{ old('max_audio_seconds', $planModel?->max_audio_seconds) }}">
    <div class="form-text">{{ __('backend.plans.max_audio_seconds_help') }}</div>
  </div>
  @php
    $planForLimits = $planModel;
    $patientMsgSource = \App\Support\MessagingLimits::messagesPerExchangeSource($planForLimits, \App\Support\MessagingLimits::ROLE_PATIENT);
    $psychMsgSource = \App\Support\MessagingLimits::messagesPerExchangeSource($planForLimits, \App\Support\MessagingLimits::ROLE_PSYCHOLOGIST);
    $patientMsgEffective = \App\Support\MessagingLimits::patientMessagesPerExchange($planForLimits);
    $psychMsgEffective = \App\Support\MessagingLimits::psychologistMessagesPerExchange($planForLimits);
  @endphp
  {{--<div class="col-md-4 mb-3">
    <label class="form-label" for="patient_messages_per_exchange">{{ __('backend.plans.patient_messages_per_exchange') }}</label>
    <input type="number" min="0" name="patient_messages_per_exchange" id="patient_messages_per_exchange" class="form-control"
      value="{{ old('patient_messages_per_exchange', $planModel?->patient_messages_per_exchange) }}">
    <div class="form-text">{{ __('backend.plans.patient_messages_per_exchange_help') }}</div>
    <div class="form-text">
      {{ __('backend.plans.effective_label') }}:
      {{ $patientMsgEffective === null ? __('backend.plans.unlimited') : $patientMsgEffective }}
      ({{ __('backend.plans.limit_source.'.$patientMsgSource) }})
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="psychologist_messages_per_exchange">{{ __('backend.plans.psychologist_messages_per_exchange') }}</label>
    <input type="number" min="0" name="psychologist_messages_per_exchange" id="psychologist_messages_per_exchange" class="form-control"
      value="{{ old('psychologist_messages_per_exchange', $planModel?->psychologist_messages_per_exchange) }}">
    <div class="form-text">{{ __('backend.plans.psychologist_messages_per_exchange_help') }}</div>
    <div class="form-text">
      {{ __('backend.plans.effective_label') }}:
      {{ $psychMsgEffective === null ? __('backend.plans.unlimited') : $psychMsgEffective }}
      ({{ __('backend.plans.limit_source.'.$psychMsgSource) }})
    </div>
  </div>--}}
  <div class="col-md-4 mb-3">
    <label class="form-label" for="response_time_hours">{{ __('backend.plans.response_time_hours') }}</label>
    <input type="number" min="1" max="720" name="response_time_hours" id="response_time_hours" class="form-control"
      value="{{ old('response_time_hours', $planModel?->response_time_hours) }}">
    <div class="form-text">{{ __('backend.plans.response_time_hours_help') }}</div>
  </div>
  {{-- Legacy single-language response label:
  <div class="col-md-4 mb-3">
    <label class="form-label" for="response_time_label">{{ __('backend.plans.response_time_label') }}</label>
    <input type="text" name="response_time_label" id="response_time_label" class="form-control"
      value="{{ old('response_time_label', $planModel?->response_time_label) }}"
      placeholder="{{ __('backend.plans.response_time_label_placeholder') }}">
  </div>
  --}}
  <div class="col-md-4 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.plans.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control"
      value="{{ old('sort_order', $planModel?->sort_order ?? 0) }}">
  </div>
  @if ($planModel)
  <div class="col-md-4 mb-3">
    <label class="form-label" for="plan_id">{{ __('backend.plans.stripe_plan_id') }}</label>
    <input type="text" id="plan_id" class="form-control" value="{{ $planModel->plan_id }}" readonly>
    <input type="hidden" name="plan_id" value="{{ $planModel->plan_id }}">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="stripe_price_id">{{ __('backend.plans.stripe_price_id') }}</label>
    <input type="text" id="stripe_price_id" class="form-control" value="{{ $planModel->stripe_price_id }}" readonly>
    <input type="hidden" name="stripe_price_id" value="{{ $planModel->stripe_price_id }}">
  </div>
  @else
  {{-- Created automatically in Stripe on save:
  <div class="col-md-4 mb-3">
    <label class="form-label" for="stripe_price_id">{{ __('backend.plans.stripe_price_id') }}</label>
    <input type="text" name="stripe_price_id" id="stripe_price_id" class="form-control"
      value="{{ old('stripe_price_id', $planModel?->stripe_price_id) }}">
  </div>
  --}}
  @endif
  <div class="col-12 mb-3">
    <label class="form-label d-block">{{ __('backend.plans.channels') }}</label>
    @foreach (['text', 'dictation', 'audio'] as $channel)
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" name="channels[]" value="{{ $channel }}" id="channel_{{ $channel }}"
          @checked(in_array($channel, $selectedChannels, true))>
        <label class="form-check-label" for="channel_{{ $channel }}">{{ __('backend.plans.channel_options.'.$channel) }}</label>
      </div>
    @endforeach
    <div class="form-text">{{ __('backend.plans.channels_help') }}</div>
  </div>
  {{-- Legacy single-language features:
  <div class="col-12 mb-3">
    <label class="form-label" for="features_text">{{ __('backend.plans.features') }}</label>
    <textarea name="features_text" id="features_text" class="form-control" rows="4">{{ old('features_text', $planModel && is_array($planModel->features) ? implode("\n", $planModel->features) : '') }}</textarea>
    <div class="form-text">{{ __('backend.plans.features_help') }}</div>
  </div>
  --}}
  <div class="col-md-4 mb-3">
    <label class="form-label" for="status">{{ __('backend.status') }}</label>
    <select name="status" id="status" class="form-select" required>
      @foreach (['draft', 'active', 'archived'] as $status)
        <option value="{{ $status }}" @selected(old('status', $planModel?->status ?? 'draft') === $status)>
          {{ __('backend.plans.statuses.'.$status) }}
        </option>
      @endforeach
    </select>
  </div>
</div>

<div class="mb-3">
  <label class="form-label">{{ __('backend.plans.content_translations') }}</label>
  <div class="form-text mb-2">{{ __('backend.plans.content_translations_help') }}</div>
  <ul class="nav nav-tabs" role="tablist">
    @foreach ($locales as $i => $locale)
      <li class="nav-item" role="presentation">
        <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#plan-locale-{{ $locale }}" type="button" role="tab">
          {{ \App\Support\Locales::label($locale) }}
        </button>
      </li>
    @endforeach
  </ul>
  <div class="tab-content border border-top-0 p-3">
    @foreach ($locales as $i => $locale)
      @php
        $tr = $planModel?->translations?->firstWhere('locale', $locale);
        $fallbackName = $hasTranslations ? $tr?->name : $planModel?->getRawOriginal('name');
        $fallbackMotto = $hasTranslations ? $tr?->motto : $planModel?->getRawOriginal('motto');
        $fallbackDescription = $hasTranslations ? $tr?->description : $planModel?->getRawOriginal('description');
        $fallbackResponseLabel = $hasTranslations ? $tr?->response_time_label : $planModel?->getRawOriginal('response_time_label');
        $fallbackFeaturesText = is_array($tr?->features)
          ? implode("\n", $tr->features)
          : (! $hasTranslations && is_array($planModel?->features) ? implode("\n", $planModel->features) : '');
      @endphp
      <div class="tab-pane fade @if($i === 0) show active @endif" id="plan-locale-{{ $locale }}" role="tabpanel">
        <div class="mb-3">
          <label class="form-label" for="name_{{ $locale }}">{{ __('backend.name') }}</label>
          <input type="text" name="translations[{{ $locale }}][name]" id="name_{{ $locale }}" class="form-control"
            value="{{ old("translations.$locale.name", $fallbackName) }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="motto_{{ $locale }}">{{ __('backend.plans.motto') }}</label>
          <input type="text" name="translations[{{ $locale }}][motto]" id="motto_{{ $locale }}" class="form-control" maxlength="255"
            value="{{ old("translations.$locale.motto", $fallbackMotto) }}"
            placeholder="{{ __('backend.plans.motto_placeholder') }}">
        </div>
        <div class="mb-3">
          <label class="form-label" for="description_{{ $locale }}">{{ __('backend.description') }}</label>
          <textarea name="translations[{{ $locale }}][description]" id="description_{{ $locale }}" class="form-control" rows="3">{{ old("translations.$locale.description", $fallbackDescription) }}</textarea>
        </div>
        <div class="mb-3">
          <label class="form-label" for="response_time_label_{{ $locale }}">{{ __('backend.plans.response_time_label') }}</label>
          <input type="text" name="translations[{{ $locale }}][response_time_label]" id="response_time_label_{{ $locale }}" class="form-control"
            value="{{ old("translations.$locale.response_time_label", $fallbackResponseLabel) }}"
            placeholder="{{ __('backend.plans.response_time_label_placeholder') }}">
        </div>
        <div class="mb-0">
          <label class="form-label" for="features_text_{{ $locale }}">{{ __('backend.plans.features') }}</label>
          <textarea name="translations[{{ $locale }}][features_text]" id="features_text_{{ $locale }}" class="form-control" rows="4">{{ old("translations.$locale.features_text", $fallbackFeaturesText) }}</textarea>
          <div class="form-text">{{ __('backend.plans.features_help') }}</div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="form-check mb-2">
  <input class="form-check-input" type="checkbox" name="is_recommended" value="1" id="is_recommended"
    @checked(old('is_recommended', $planModel?->is_recommended ?? false))>
  <label class="form-check-label" for="is_recommended">{{ __('backend.plans.is_recommended') }}</label>
</div>
{{-- Legacy active checkbox (status dropdown replaces this):
<div class="form-check mb-0">
  <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
    @checked(old('is_active', $planModel?->is_active ?? true))>
  <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
</div>
--}}

@once
  @push('scripts')
    <script>
      (function () {
        const planType = document.getElementById('plan_type');
        const billing = document.getElementById('billing_interval');
        if (!planType || !billing || billing.tagName !== 'SELECT') {
          return;
        }

        const syncBillingIntervalOptions = () => {
          const isSubscription = planType.value === 'subscription';
          const wantedGroup = isSubscription ? 'subscription' : 'per_exchange';
          let firstVisible = null;
          let selectedStillVisible = false;

          Array.from(billing.options).forEach((option) => {
            const match = option.dataset.intervalGroup === wantedGroup;
            option.hidden = !match;
            option.disabled = !match;
            if (match && !firstVisible) {
              firstVisible = option;
            }
            if (match && option.selected) {
              selectedStillVisible = true;
            }
          });

          if (!selectedStillVisible && firstVisible) {
            billing.value = firstVisible.value;
          }
        };

        planType.addEventListener('change', syncBillingIntervalOptions);
        syncBillingIntervalOptions();
      })();
    </script>
  @endpush
@endonce
