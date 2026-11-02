@extends('backend.layouts.app')

@section('title', __('backend.patients.section_subscriptions'))
@section('page_title', __('backend.patients.section_subscriptions'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient) }}">{{ $patient->user?->name ?? __('backend.patients.details') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.patients.section_subscriptions') }}</li>
@endsection

@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.patients.view_details') }}
    </a>
    <a href="{{ route('admin.patients.exchanges', $patient) }}" class="btn btn-primary btn-sm">
      {{ __('backend.patients.manage_exchanges') }}
    </a>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title mb-0">{{ $patient->user?->name ?? __('backend.patients.details') }} — {{ __('backend.patients.section_subscriptions') }}</h3></div>
    <div class="card-body p-0 table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.patients.plan') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.patients.exchanges') }}</th>
            <th>{{ __('backend.patients.period_ends') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($patient->subscriptions as $subscription)
            <tr>
              <td>{{ $subscription->plan?->name ?: '—' }}</td>
              <td><span class="badge text-bg-secondary">{{ $subscription->status }}</span></td>
              <td>{{ $subscription->exchanges_remaining }} / {{ ($subscription->exchanges_used ?? 0) + ($subscription->exchanges_remaining ?? 0) }}</td>
              <td>{{ $subscription->current_period_ends_at?->format('d M Y') ?: '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted text-center py-3">{{ __('backend.patients.empty_section') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
