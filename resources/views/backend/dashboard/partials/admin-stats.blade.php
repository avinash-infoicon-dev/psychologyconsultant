<h5 class="mb-3">{{ __('backend.dashboard_pages.section_users') }}</h5>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-primary">
      <div class="inner">
        <h3>{{ $stats['patients']['total'] }}</h3>
        <p>{{ __('backend.dashboard_pages.patients_total') }}</p>
      </div>
      <div class="icon"><i class="bi bi-people"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.patients_new_month') }}: <strong>{{ $stats['patients']['new_this_month'] }}</strong>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-success">
      <div class="inner">
        {{-- Legacy: total professionals as the headline.
        <h3>{{ $stats['psychologists']['total'] }}</h3>
        <p>{{ __('backend.dashboard_pages.psychologists_total') }}</p>
        --}}
        <h3>{{ $stats['psychologists']['active'] ?? $stats['psychologists']['verified'] }}</h3>
        <p>{{ __('backend.dashboard_pages.psychologists_active') }}</p>
      </div>
      <div class="icon"><i class="bi bi-person-badge"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.psychologists_awaiting') }}: <strong>{{ $stats['psychologists']['pending_verification'] }}</strong>
        · {{ __('backend.dashboard_pages.psychologists_total') }}: <strong>{{ $stats['psychologists']['total'] }}</strong>
      </div>
    </div>
  </div>
</div>

<h5 class="mb-3">{{ __('backend.dashboard_pages.section_subscriptions') }}</h5>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-info">
      <div class="inner">
        <h3>{{ $stats['subscriptions']['active'] }}</h3>
        <p>{{ __('backend.dashboard_pages.subscriptions_active') }}</p>
      </div>
      <div class="icon"><i class="bi bi-card-checklist"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.subscriptions_new_month') }}: <strong>{{ $stats['subscriptions']['new_this_month'] ?? 0 }}</strong>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-secondary">
      <div class="inner">
        <h3>{{ ($stats['subscriptions']['expired'] ?? 0) + ($stats['subscriptions']['cancelled'] ?? 0) }}</h3>
        <p>{{ __('backend.dashboard_pages.subscriptions_ended') }}</p>
      </div>
      <div class="icon"><i class="bi bi-x-circle"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.subscriptions_expired') }}: <strong>{{ $stats['subscriptions']['expired'] ?? 0 }}</strong>
        · {{ __('backend.dashboard_pages.subscriptions_cancelled') }}: <strong>{{ $stats['subscriptions']['cancelled'] ?? 0 }}</strong>
      </div>
    </div>
  </div>
</div>

<h5 class="mb-3">{{ __('backend.dashboard_pages.section_activity') }}</h5>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-primary"><i class="bi bi-chat-dots"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.conversations_active') }}</span>
        <span class="info-box-number">{{ $stats['activity']['active_conversations'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-warning"><i class="bi bi-person-plus"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.relationship_requests') }}</span>
        <span class="info-box-number">{{ $stats['activity']['new_requests'] ?? 0 }}</span>
        <span class="info-box-text">{{ __('backend.dashboard_pages.relationship_requests_pending') }}: {{ $stats['activity']['pending_requests'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-success"><i class="bi bi-arrow-left-right"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_consumed') }}</span>
        <span class="info-box-number">{{ $stats['activity']['exchanges_consumed'] ?? 0 }}</span>
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_consumed_month') }}: {{ $stats['activity']['exchanges_consumed_month'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-secondary"><i class="bi bi-chat-text"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.messages_today') }}</span>
        <span class="info-box-number">{{ $stats['messages']['sent_today'] }}</span>
      </div>
    </div>
  </div>
</div>

{{--<h5 class="mb-3">{{ __('backend.dashboard_pages.section_exchanges') }}</h5>
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-success"><i class="bi bi-check2-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_completed_month') }}</span>
        <span class="info-box-number">{{ $stats['exchanges']['completed_month'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-warning"><i class="bi bi-hourglass-split"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_pending') }}</span>
        <span class="info-box-number">{{ $stats['exchanges']['pending'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-info"><i class="bi bi-bag-plus"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_purchased_month') }}</span>
        <span class="info-box-number">{{ $stats['exchanges']['purchased_month'] ?? 0 }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-secondary"><i class="bi bi-sliders"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.exchanges_admin_adjustments_month') }}</span>
        <span class="info-box-number">{{ $stats['exchanges']['admin_adjustments_month'] ?? 0 }}</span>
      </div>
    </div>
  </div>
</div>--}}



{{-- Previous dashboard cards
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-info">
      <div class="inner">
        <h3>{{ $stats['subscriptions']['active'] }}</h3>
        <p>{{ __('backend.dashboard_pages.subscriptions_active') }}</p>
      </div>
      <div class="icon"><i class="bi bi-card-checklist"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.subscriptions_cancelled_month') }}: <strong>{{ $stats['subscriptions']['cancelled_this_month'] }}</strong>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box text-bg-warning">
      <div class="inner">
        <h3>{{ number_format($stats['revenue']['this_month'] / 100, 2) }}</h3>
        <p>{{ __('backend.dashboard_pages.revenue_this_month') }} (CHF)</p>
      </div>
      <div class="icon"><i class="bi bi-currency-exchange"></i></div>
      <div class="small-box-footer px-3 py-2 text-start">
        {{ __('backend.dashboard_pages.revenue_last_30') }}: <strong>{{ number_format($stats['revenue']['last_30_days'] / 100, 2) }}</strong>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-secondary"><i class="bi bi-chat-dots"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.messages_today') }}</span>
        <span class="info-box-number">{{ $stats['messages']['sent_today'] }}</span>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <div class="info-box">
      <span class="info-box-icon text-bg-success"><i class="bi bi-check-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ __('backend.dashboard_pages.psychologists_accepting') }}</span>
        <span class="info-box-number">{{ $stats['psychologists']['accepting_patients'] }}</span>
      </div>
    </div>
  </div>
</div>
--}}
