@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.subscriptions_history'))
@section('page_title', __('frontend.subscription.subscriptions_history'))

@section('content')
  <div class="card radius-16 border-0">
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
              <th>{{ __('frontend.subscription.renews_on') }}</th>
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
                <td>{{ $subItem->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-sm fe-subscription-status-label is-{{ $subItem->statusTone() }}">{{ $subItem->statusLabel() }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-secondary-light py-20">{{ __('frontend.subscription.no_subscriptions_history') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($subscriptionsHistory->total() > 0)
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-24">
          <p class="mb-0 text-secondary-light">
            Showing {{ $subscriptionsHistory->firstItem() ?: 0 }} to {{ $subscriptionsHistory->lastItem() ?: 0 }} of {{ $subscriptionsHistory->total() }} entries
          </p>
          {{ $subscriptionsHistory->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection
