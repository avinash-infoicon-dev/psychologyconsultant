@extends('backend.layouts.app')

@section('title', $patient->user?->name ?? __('backend.patients.details'))
@section('page_title', __('backend.patients.details'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item active">{{ $patient->user?->name ?? __('backend.patients.details') }}</li>
@endsection

@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.back') }}
    </a>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('admin.patients.subscriptions', $patient) }}" class="btn btn-outline-primary btn-sm">{{ __('backend.patients.section_subscriptions') }}</a>
      <a href="{{ route('admin.patients.conversations', $patient) }}" class="btn btn-outline-primary btn-sm">{{ __('backend.patients.section_conversations') }}</a>
      <a href="{{ route('admin.patients.exchanges', $patient) }}" class="btn btn-outline-primary btn-sm">{{ __('backend.patients.section_credits') }}</a>
      <a href="{{ route('admin.patients.notes', $patient) }}" class="btn btn-outline-primary btn-sm">{{ __('backend.patients.section_notes') }}</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_profile') }}</h3></div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tbody>
              <tr><th style="width:40%">{{ __('backend.name') }}</th><td>{{ $patient->user?->name ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.email') }}</th><td>{{ $patient->user?->email ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.phone') }}</th><td>{{ $patient->user?->phone ?: '—' }}</td></tr>
              <tr>
                <th>{{ __('backend.status') }}</th>
                <td>
                  @if ($patient->user?->is_active)
                    <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                  @else
                    <span class="badge text-bg-secondary">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>{{ __('backend.patients.email_verified') }}</th>
                <td>{{ $patient->user?->hasVerifiedEmail() ? __('backend.yes') : __('backend.no') }}</td>
              </tr>
              <tr><th>{{ __('backend.patients.date_of_birth') }}</th><td>{{ $patient->date_of_birth?->format('Y-m-d') ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.gender') }}</th><td>{{ $patient->gender ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.address') }}</th><td>{{ $patient->address ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.city') }}</th><td>{{ $patient->city ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.country') }}</th><td>{{ $patient->country ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.psychologist') }}</th><td>{{ $patient->psychologist?->user?->name ?: ($patient->psychologist?->name ?? '—') }}</td></tr>
              <tr><th>{{ __('backend.patients.credit_balance') }}</th><td>{{ $creditBalance }}</td></tr>
              <tr>
                <th>{{ __('backend.patients.active_plan') }}</th>
                <td>
                  @if ($activeSubscription?->plan)
                    {{ $activeSubscription->plan->name }}
                    <span class="badge text-bg-success">{{ $activeSubscription->status }}</span>
                  @else
                    —
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_emergency') }}</h3></div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tbody>
              <tr><th style="width:40%">{{ __('backend.patients.emergency_name') }}</th><td>{{ $patient->emergency_contact_name ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.patients.emergency_phone') }}</th><td>{{ $patient->emergency_contact_phone ?: '—' }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      {{--<div class="card">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_preferences') }}</h3></div>
        <div class="card-body">
          @if (is_array($patient->preferences) && count($patient->preferences))
            <pre class="mb-0 small bg-light border rounded p-2">{{ json_encode($patient->preferences, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
          @else
            <span class="text-muted">{{ __('backend.patients.empty_section') }}</span>
          @endif
        </div>
      </div>--}}
    </div>

    @if (false)
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h3 class="card-title mb-0">{{ __('backend.patients.section_subscriptions') }}</h3>
          {{--<a href="{{ route('admin.patients.exchanges', $patient) }}" class="btn btn-sm btn-primary">
            {{ __('backend.patients.manage_exchanges') }}--}}
          </a>
        </div>
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
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_credits') }}</h3></div>
        <div class="card-body p-0 table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>{{ __('backend.patients.amount') }}</th>
                <th>{{ __('backend.patients.reason') }}</th>
                <th>{{ __('backend.patients.date') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($patient->credits as $credit)
                <tr>
                  <td>{{ $credit->amount }}</td>
                  <td>{{ $credit->note ?: ($credit->source ?: '—') }}</td>
                  <td>{{ $credit->created_at?->format('d M Y H:i') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted text-center py-3">{{ __('backend.patients.empty_section') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_conversations') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($patient->conversations as $conversation)
              <li class="list-group-item">
                <strong>{{ $conversation->psychologist?->user?->name ?: ($conversation->psychologist?->name ?? '—') }}</strong>
                <div class="text-muted small">
                  {{ $conversation->status }}
                  ·
                  {{ $conversation->last_message_at?->format('d M Y H:i') ?: '—' }}
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.patients.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_notes') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($patient->clinicalNotes as $note)
              <li class="list-group-item">
                <div class="text-muted small mb-1">{{ $note->created_at?->format('d M Y H:i') }}</div>
                <div>{{ \Illuminate\Support\Str::limit($note->content, 180) }}</div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.patients.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
    @endif
  </div>
@endsection
