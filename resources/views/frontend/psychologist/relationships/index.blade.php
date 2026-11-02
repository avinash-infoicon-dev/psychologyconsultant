@extends('frontend.layouts.dashboard')

@section('title', __('relationship.pending_requests'))

@section('content')
  <div class="fe-dashboard-header">
    <h1>{{ __('relationship.pending_requests') }}</h1>
  </div>

  <div class="fe-card">
    @if ($pendingRequests->isEmpty())
      <div class="fe-empty-state">
        <i class="bi bi-inbox fe-empty-state__icon"></i>
        <p>{{ __('relationship.no_pending_requests') }}</p>
      </div>
    @else
      <div class="fe-table-responsive">
        <table class="fe-table">
          <thead>
            <tr>
              <th>{{ __('relationship.patient') }}</th>
              <th>{{ __('relationship.requested_at') }}</th>
              <th>{{ __('relationship.status') }}</th>
              <th>{{ __('relationship.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pendingRequests as $request)
              <tr>
                <td>{{ $request->patient->user->name ?? trim($request->patient->first_name . ' ' . $request->patient->last_name) }}</td>
                <td>{{ $request->requested_at->format('d M Y H:i') }}</td>
                <td>{{ __('relationship.status_'.$request->status) }}</td>
                <td>
                  <form action="{{ route('frontend.psychologist.relationships.accept', $request) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="fe-btn fe-btn--primary fe-btn--sm">{{ __('relationship.accept') }}</button>
                  </form>
                  <form action="{{ route('frontend.psychologist.relationships.reject', $request) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('relationship.reject') }}</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection
