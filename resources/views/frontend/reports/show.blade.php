@extends('frontend.layouts.dashboard')

@php
  $isReportedView = ($roleOnReport ?? 'reporter') === 'reported';
@endphp

@section('title', $isReportedView
  ? __('frontend.reports.clarification_title', ['id' => $report->id])
  : __('frontend.reports.show_title', ['id' => $report->id]))
@section('page_title', $isReportedView
  ? __('frontend.reports.clarification_title', ['id' => $report->id])
  : __('frontend.reports.show_title', ['id' => $report->id]))

@section('content')
  <div class="fe-cms-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <a href="{{ route($routePrefix.'.reports.index') }}" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('frontend.reports.back') }}</a>
      <span class="fe-report-badge {{ $report->statusBadgeClass() }}">{{ __('frontend.reports.status_'.$report->status) }}</span>
    </div>

    {{-- Old: every viewer saw type, reason, reported person, description, and the single public thread.
    <dl class="fe-report-meta">
      <div>
        <dt>{{ __('frontend.reports.type') }}</dt>
        <dd>{{ __('frontend.reports.type_'.$report->type) }}</dd>
      </div>
      <div>
        <dt>{{ __('frontend.reports.reason') }}</dt>
        <dd>{{ __('frontend.reports.reason_'.$report->reason) }}</dd>
      </div>
      <div>
        <dt>{{ __('frontend.reports.person') }}</dt>
        <dd>{{ $report->reportedUser?->name ?: '—' }}</dd>
      </div>
      @if ($report->conversation)
        <div>
          <dt>{{ __('frontend.reports.conversation') }}</dt>
          <dd>
            <a href="{{ route($routePrefix.'.conversations.show', $report->conversation->uuid) }}">
              #{{ $report->conversation->id }}
            </a>
          </dd>
        </div>
      @endif
      <div>
        <dt>{{ __('frontend.reports.created_at') }}</dt>
        <dd>{{ $report->created_at?->format('d.m.Y H:i') }}</dd>
      </div>
    </dl>
    <h3 class="h6 fw-semibold">{{ __('frontend.reports.description') }}</h3>
    <p>{{ $report->description }}</p>
    --}}

    @if ($isReportedView)
      <p class="fe-muted">{{ __('frontend.reports.clarification_hint') }}</p>
    @else
      <dl class="fe-report-meta">
        <div>
          <dt>{{ __('frontend.reports.type') }}</dt>
          <dd>{{ __('frontend.reports.type_'.$report->type) }}</dd>
        </div>
        <div>
          <dt>{{ __('frontend.reports.reason') }}</dt>
          <dd>{{ __('frontend.reports.reason_'.$report->reason) }}</dd>
        </div>
        <div>
          <dt>{{ __('frontend.reports.person') }}</dt>
          <dd>{{ $report->reportedUser?->name ?: '—' }}</dd>
        </div>
        @if ($report->conversation)
          <div>
            <dt>{{ __('frontend.reports.conversation') }}</dt>
            <dd>
              <a href="{{ route($routePrefix.'.conversations.show', $report->conversation->uuid) }}">
                #{{ $report->conversation->id }}
              </a>
            </dd>
          </div>
        @endif
        <div>
          <dt>{{ __('frontend.reports.created_at') }}</dt>
          <dd>{{ $report->created_at?->format('d.m.Y H:i') }}</dd>
        </div>
      </dl>

      <h3 class="h6 fw-semibold">{{ __('frontend.reports.description') }}</h3>
      <p>{{ $report->description }}</p>
    @endif

    <h3 class="h6 fw-semibold mt-4">{{ __('frontend.reports.thread') }}</h3>
    @forelse ($report->comments as $comment)
      <article class="fe-report-comment">
        <strong>{{ $comment->user?->name }}</strong>
        <small class="fe-muted">{{ $comment->created_at?->diffForHumans() }}</small>
        <p class="mb-0">{{ $comment->body }}</p>
      </article>
    @empty
      <p class="fe-muted">{{ __('frontend.reports.thread_empty') }}</p>
    @endforelse

    @if (! $report->isClosed())
      <form method="post" action="{{ route($routePrefix.'.reports.comments.store', $report) }}" class="mt-3">
        @csrf
        <input type="hidden" name="audience" value="{{ $audience }}">
        <div class="fe-form-group">
          <label for="body">{{ __('frontend.reports.add_information') }}</label>
          <textarea name="body" id="body" class="fe-input" rows="4" required minlength="2">{{ old('body') }}</textarea>
        </div>
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.reports.send') }}</button>
      </form>
    @else
      <p class="fe-muted mt-3 mb-0">{{ __('frontend.reports.closed_hint') }}</p>
    @endif
  </div>
@endsection
