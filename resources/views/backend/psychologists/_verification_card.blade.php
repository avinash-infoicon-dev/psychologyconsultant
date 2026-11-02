@php
  $status = $professional->verification_status ?: 'draft';
  $badge = match ($status) {
    'verified' => 'text-bg-success',
    'submitted', 'under_review' => 'text-bg-warning',
    'rejected' => 'text-bg-danger',
    'suspended' => 'text-bg-dark',
    default => 'text-bg-secondary',
  };
@endphp

<div class="card mb-3">
  <div class="card-header">
    <h3 class="card-title mb-0">{{ __('backend.psychologists.section_verification') }}</h3>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <tbody>
        <tr>
          <th style="width:40%">{{ __('backend.psychologists.verification') }}</th>
          <td><span class="badge {{ $badge }}">{{ __('backend.psychologists.status_'.$status) }}</span></td>
        </tr>
        <tr>
          <th>{{ __('backend.psychologists.active') }}</th>
          <td>
            @if ($professional->status)
              <span class="badge text-bg-secondary">{{ __('backend.psychologists.active') }}</span>
            @else
              <span class="badge text-bg-dark">{{ __('backend.psychologists.inactive') }}</span>
            @endif
          </td>
        </tr>
        <tr>
          <th>{{ __('backend.psychologists.last_reason') }}</th>
          <td>{{ $professional->verification_reason ?: '—' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="card-header border-top">
    <h3 class="card-title mb-0">{{ __('backend.psychologists.actions') }}</h3>
  </div>
  <div class="card-body">
    @include('backend.psychologists._verification_actions')
  </div>
  <div class="card-header border-top">
    <h3 class="card-title mb-0">{{ __('backend.psychologists.history') }}</h3>
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <tbody>
        @forelse ($verificationLogs as $log)
          @php
            $actionKey = match ($log->action) {
              'psychologist.submitted' => 'submitted',
              'admin.psychologist.review_started' => 'review_started',
              'admin.psychologist.verified' => 'verified',
              'admin.psychologist.rejected' => 'rejected',
              'admin.psychologist.suspended' => 'suspended',
              'admin.psychologist.restored' => 'restored',
              default => null,
            };
          @endphp
          <tr>
            <td>
              {{ $actionKey ? __('backend.psychologists.action_'.$actionKey) : $log->action }}
              @if (! empty($log->properties['reason']))
                <div class="text-muted small">{{ $log->properties['reason'] }}</div>
              @endif
            </td>
            <td class="text-muted">{{ $log->user?->name ?: '—' }}</td>
            <td class="text-muted text-nowrap">{{ $log->created_at?->format('d.m.Y H:i') }}</td>
          </tr>
        @empty
          @include('backend.partials.empty-row', [
            'colspan' => 3,
            'title' => __('backend.psychologists.history_empty'),
          ])
        @endforelse
      </tbody>
    </table>
  </div>
  @if (method_exists($verificationLogs, 'hasPages') && $verificationLogs->hasPages())
    <div class="card-footer">{{ $verificationLogs->links() }}</div>
  @endif
</div>
