@extends('backend.layouts.app')

@section('title', __('backend.patients.exchanges_page_title'))
@section('page_title', __('backend.patients.exchanges_page_title'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient) }}">{{ $patient->user?->name ?? __('backend.patients.details') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.patients.exchanges_page_title') }}</li>
@endsection

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.back') }}
    </a>
  </div>

  <div class="card mb-3">
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

  {{--<div class="card mb-3">
    <div class="card-header">
      <h3 class="card-title mb-0">{{ $patient->user?->name ?? __('backend.patients.details') }}</h3>
    </div>
    <div class="card-body p-0">
      <table class="table mb-0">
        <tbody>
          <tr>
            <th style="width:32%">{{ __('backend.patients.plan') }}</th>
            <td>{{ $subscription?->plan?->name ?: __('backend.patients.no_subscription') }}</td>
          </tr>
          <tr>
            <th>{{ __('backend.status') }}</th>
            <td>
              @if ($subscription)
                <span class="badge text-bg-secondary">{{ $subscription->status }}</span>
              @else
                —
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('backend.patients.period_ends') }}</th>
            <td>{{ $balance['renewal_label'] ?? ($subscription?->current_period_ends_at?->format('d M Y') ?: '—') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>--}}

  {{--<div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box text-bg-info">
        <div class="inner">
          <h3>{{ $balance['included'] ?? 0 }}</h3>
          <p>{{ __('backend.patients.included') }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box text-bg-secondary">
        <div class="inner">
          <h3>{{ $balance['consumed'] ?? 0 }}</h3>
          <p>{{ __('backend.patients.consumed') }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box text-bg-warning">
        <div class="inner">
          <h3>{{ $balance['purchased'] ?? 0 }}</h3>
          <p>{{ __('backend.patients.purchased') }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box text-bg-success">
        <div class="inner">
          <h3>{{ $balance['remaining'] ?? 0 }}</h3>
          <p>{{ __('backend.patients.remaining') }}</p>
        </div>
      </div>
    </div>
  </div>

  @permission('subscriptions.adjust')
    <div class="row g-3 mb-3">
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.add_exchanges') }}</h3></div>
          <div class="card-body">
            <p class="text-muted small">{{ __('backend.patients.add_exchanges_help') }}</p>
            <form action="{{ route('admin.patients.exchanges.add', $patient) }}" method="post">
              @csrf
              <div class="mb-3">
                <label class="form-label" for="add_quantity">{{ __('backend.patients.quantity') }}</label>
                <input
                  type="number"
                  min="1"
                  step="1"
                  name="quantity"
                  id="add_quantity"
                  class="form-control @error('quantity', 'add') is-invalid @enderror"
                  value="{{ old('quantity', 1) }}"
                  required
                >
                @error('quantity', 'add')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label" for="add_reason">{{ __('backend.patients.reason') }}</label>
                <textarea name="reason" id="add_reason" rows="2" class="form-control @error('reason', 'add') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                @error('reason', 'add')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-primary">{{ __('backend.patients.add_exchanges') }}</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.adjust_balance') }}</h3></div>
          <div class="card-body">
            <p class="text-muted small">{{ __('backend.patients.adjust_balance_help') }}</p>
            <form action="{{ route('admin.patients.exchanges.adjust', $patient) }}" method="post">
              @csrf
              <div class="mb-3">
                <label class="form-label" for="adjust_quantity">{{ __('backend.patients.quantity') }}</label>
                <input
                  type="number"
                  step="1"
                  name="quantity"
                  id="adjust_quantity"
                  class="form-control @error('quantity', 'adjust') is-invalid @enderror"
                  value="{{ old('quantity') }}"
                  required
                >
                @error('quantity', 'adjust')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label" for="adjust_reason">{{ __('backend.patients.reason') }}</label>
                <textarea name="reason" id="adjust_reason" rows="2" class="form-control @error('reason', 'adjust') is-invalid @enderror" required>{{ old('reason') }}</textarea>
                @error('reason', 'adjust')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-outline-primary">{{ __('backend.patients.adjust_balance') }}</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endpermission

  <div class="card mb-3">
    <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.history') }}</h3></div>
    <div class="card-body p-0 table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.patients.date') }}</th>
            <th>{{ __('backend.patients.type') }}</th>
            <th>{{ __('backend.patients.quantity') }}</th>
            <th>{{ __('backend.patients.reason') }}</th>
            <th>{{ __('backend.patients.admin') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transactions as $row)
            <tr>
              <td>{{ $row->created_at?->format('d M Y H:i') }}</td>
              <td>{{ __('backend.patients.type_'.$row->type) }}</td>
              <td>{{ $row->quantity > 0 ? '+'.$row->quantity : $row->quantity }}</td>
              <td>{{ $row->reason ?: '—' }}</td>
              <td>{{ $row->admin?->name ?: '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted text-center py-3">{{ __('backend.patients.empty_section') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($transactions->hasPages())
      <div class="card-footer">{{ $transactions->links() }}</div>
    @endif
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.patients.section_exchange_requests') }}</h3></div>
    <div class="card-body p-0 table-responsive">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.patients.date') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.patients.psychologist') }}</th>
            <th>{{ __('backend.patients.completed_at') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($exchanges as $exchange)
            <tr>
              <td>{{ $exchange->created_at?->format('d M Y H:i') }}</td>
              <td>
                @if ($exchange->status === 'pending')
                  <span class="badge text-bg-warning">{{ __('backend.patients.exchange_status_pending') }}</span>
                @elseif ($exchange->status === 'completed')
                  <span class="badge text-bg-success">{{ __('backend.patients.exchange_status_completed') }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ __('backend.patients.exchange_status_cancelled') }}</span>
                @endif
              </td>
              <td>{{ $exchange->psychologist?->user?->name ?: ($exchange->psychologist?->name ?? '—') }}</td>
              <td>{{ $exchange->completed_at?->format('d M Y H:i') ?: '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted text-center py-3">{{ __('backend.patients.empty_section') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>--}}
@endsection
