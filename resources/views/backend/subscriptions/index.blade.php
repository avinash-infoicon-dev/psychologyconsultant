@extends('backend.layouts.app')

@section('title', __('backend.subscriptions.title'))
@section('page_title', __('backend.subscriptions.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.subscriptions') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.subscriptions.title') }}</h3>
      <form method="get" class="d-flex gap-2 flex-wrap">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <select name="status" class="form-select form-select-sm">
          <option value="">{{ __('backend.status') }}</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('backend.subscriptions.statuses.'.$status) }}</option>
          @endforeach
        </select>
        <select name="plan_id" class="form-select form-select-sm">
          <option value="">{{ __('backend.plans.title') }}</option>
          @foreach ($plans as $plan)
            <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
          @endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.subscriptions.patient') }}</th>
            <th>{{ __('backend.menu.plans') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.subscriptions.period') }}</th>
            <th>{{ __('backend.subscriptions.exchanges') }}</th>
            <th>{{ __('backend.subscriptions.started_at') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($subscriptions as $subscription)
            <tr>
              <td>
                @if ($subscription->patient?->user)
                  <div>{{ $subscription->patient->user->name }}</div>
                  <small class="text-muted">{{ $subscription->patient->user->email }}</small>
                @else
                  —
                @endif
              </td>
              <td>{{ $subscription->plan?->name ?? '—' }}</td>
              <td>
                @php
                  $statusClass = match ($subscription->status) {
                    'active', 'trialing' => 'success',
                    'past_due' => 'warning',
                    'cancelled', 'expired' => 'secondary',
                    default => 'secondary',
                  };
                @endphp
                <span class="badge text-bg-{{ $statusClass }}">{{ __('backend.subscriptions.statuses.'.$subscription->status) }}</span>
              </td>
              <td>
                @if ($subscription->current_period_starts_at && $subscription->current_period_ends_at)
                  {{ $subscription->current_period_starts_at->format('Y-m-d') }}
                  –
                  {{ $subscription->current_period_ends_at->format('Y-m-d') }}
                @else
                  —
                @endif
              </td>
              <td>
                {{ $subscription->exchanges_used }} / {{ $subscription->exchanges_remaining + $subscription->exchanges_used }}
              </td>
              <td>{{ $subscription->starts_at?->format('Y-m-d H:i') ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">{{ request()->filled('q') || request()->filled('status') ? __('backend.empty_filtered') : __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($subscriptions->hasPages())
      <div class="card-footer">{{ $subscriptions->links() }}</div>
    @endif
  </div>
@endsection
