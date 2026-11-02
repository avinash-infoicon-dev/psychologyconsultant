@extends('frontend.layouts.dashboard')

@section('title', __('frontend.reports.title'))
@section('page_title', __('frontend.reports.title'))

@section('content')
  <div class="fe-cms-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div>
        <p class="mb-0 fe-muted">{{ __('frontend.reports.hint') }}</p>
      </div>
      <a href="{{ route($routePrefix.'.reports.create') }}" class="fe-btn fe-btn--primary fe-btn--sm">
        {{ __('frontend.reports.create') }}
      </a>
    </div>

    {{-- Old: every row showed reason and reported person; only the reporter's own reports.
    @forelse ($reports as $item)
      <article class="fe-report-row">
        <div>
          <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <strong>#{{ $item->id }}</strong>
            <span class="fe-report-badge {{ $item->statusBadgeClass() }}">{{ __('frontend.reports.status_'.$item->status) }}</span>
          </div>
          <p class="mb-1">{{ __('frontend.reports.reason_'.$item->reason) }}</p>
          <small class="fe-muted">
            {{ $item->reportedUser?->name ? __('frontend.reports.about_user', ['name' => $item->reportedUser->name]) : '' }}
            · {{ $item->created_at?->diffForHumans() }}
          </small>
        </div>
        <a href="{{ route($routePrefix.'.reports.show', $item) }}" class="fe-btn fe-btn--outline fe-btn--sm">
          {{ __('frontend.reports.view') }}
        </a>
      </article>
    @empty
      <p class="fe-muted mb-0">{{ __('frontend.reports.empty') }}</p>
    @endforelse
    --}}

    @forelse ($reports as $item)
      @php $isOwn = $item->reporter_id === auth()->id(); @endphp
      <article class="fe-report-row">
        <div>
          <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <strong>#{{ $item->id }}</strong>
            <span class="fe-report-badge {{ $item->statusBadgeClass() }}">{{ __('frontend.reports.status_'.$item->status) }}</span>
            <span class="fe-report-role">{{ $isOwn ? __('frontend.reports.role_submitted') : __('frontend.reports.asked_you_to_reply') }}</span>
          </div>
          @if ($isOwn)
            <p class="mb-1">{{ __('frontend.reports.reason_'.$item->reason) }}</p>
            <small class="fe-muted">
              {{ $item->reportedUser?->name ? __('frontend.reports.about_user', ['name' => $item->reportedUser->name]) : '' }}
              · {{ $item->created_at?->diffForHumans() }}
            </small>
          @else
            <small class="fe-muted">{{ $item->created_at?->diffForHumans() }}</small>
          @endif
        </div>
        <a href="{{ route($routePrefix.'.reports.show', $item) }}" class="fe-btn fe-btn--outline fe-btn--sm">
          {{ __('frontend.reports.view') }}
        </a>
      </article>
    @empty
      @include('frontend.partials.ui-empty', [
        'title' => __('frontend.reports.empty'),
        'hint' => __('frontend.reports.empty_hint'),
        'actionUrl' => route($routePrefix.'.reports.create'),
        'actionLabel' => __('frontend.reports.create'),
      ])
    @endforelse

    @if ($reports->hasPages())
      <div class="fe-pagination mt-3">{{ $reports->links() }}</div>
    @endif
  </div>
@endsection
