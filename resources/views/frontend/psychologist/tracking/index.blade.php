@extends('frontend.layouts.dashboard')

@section('title', __('frontend.tracking.title'))
@section('page_title', __('frontend.tracking.title'))

@section('content')
  <div class="fe-cms-card">
    <h2 style="margin-top:0;">{{ __('frontend.tracking.title') }}</h2>
    <p class="fe-muted">{{ __('frontend.tracking.subtitle') }}</p>

    <form method="get" class="fe-form-grid" style="margin-bottom:1.25rem;">
      <div class="fe-form-group">
        <label for="from">{{ __('frontend.tracking.from') }}</label>
        <input type="date" name="from" id="from" class="fe-input" value="{{ $from->toDateString() }}">
      </div>
      <div class="fe-form-group">
        <label for="to">{{ __('frontend.tracking.to') }}</label>
        <input type="date" name="to" id="to" class="fe-input" value="{{ $to->toDateString() }}">
      </div>
      <div class="fe-form-group" style="align-self:end;">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.tracking.apply') }}</button>
      </div>
    </form>

    <div class="fe-dash-stats">
      <div class="fe-dash-stat">
        <strong>{{ $messagesSent }}</strong>
        <span>{{ __('frontend.tracking.messages_sent') }}</span>
      </div>
      <div class="fe-dash-stat">
        <strong>{{ $conversationsTouched }}</strong>
        <span>{{ __('frontend.tracking.conversations_replied') }}</span>
      </div>
      <div class="fe-dash-stat">
        <strong>{{ $activeConversations }}</strong>
        <span>{{ __('frontend.tracking.active_threads') }}</span>
      </div>
      <div class="fe-dash-stat">
        <strong>{{ $pendingCount }}</strong>
        <span>{{ __('frontend.dashboard.stat_pending_exchanges') }}</span>
      </div>
      <div class="fe-dash-stat">
        <strong>{{ $overdueCount }}</strong>
        <span>{{ __('frontend.dashboard.stat_overdue') }}</span>
      </div>
      <div class="fe-dash-stat">
        <strong>{{ $avgResponseHours !== null ? $avgResponseHours.'h' : '—' }}</strong>
        <span>{{ __('frontend.tracking.avg_response') }}</span>
      </div>
    </div>
  </div>
@endsection
