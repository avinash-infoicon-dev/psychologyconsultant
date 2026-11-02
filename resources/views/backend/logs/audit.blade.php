@extends('backend.layouts.app')

@section('title', __('backend.logs.audit_title'))
@section('page_title', __('backend.logs.audit_title'))
@section('breadcrumb')
  <li class="breadcrumb-item">{{ __('backend.menu.logs') }}</li>
  <li class="breadcrumb-item active">{{ __('backend.logs.audit') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.logs.audit_title') }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <input type="text" name="auditable_type" value="{{ request('auditable_type') }}" class="form-control form-control-sm" placeholder="{{ __('backend.logs.auditable_type') }}">
        <input type="number" name="auditable_id" value="{{ request('auditable_id') }}" class="form-control form-control-sm" placeholder="{{ __('backend.logs.auditable_id') }}" min="1">
        <select name="event" class="form-select form-select-sm">
          <option value="">{{ __('backend.logs.event') }}</option>
          <option value="created" @selected(request('event') === 'created')>created</option>
          <option value="updated" @selected(request('event') === 'updated')>updated</option>
          <option value="deleted" @selected(request('event') === 'deleted')>deleted</option>
          <option value="restored" @selected(request('event') === 'restored')>restored</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.logs.id') }}</th>
            <th>{{ __('backend.logs.user') }}</th>
            <th>{{ __('backend.logs.event') }}</th>
            <th>{{ __('backend.logs.auditable') }}</th>
            <th>{{ __('backend.logs.changes') }}</th>
            <th>{{ __('backend.logs.ip') }}</th>
            <th>{{ __('backend.logs.date') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($logs as $log)
            <tr>
              <td>{{ $log->id }}</td>
              <td>
                @if ($log->user)
                  {{ $log->user->name }}
                  <div class="small text-muted">{{ $log->user->email }}</div>
                @else
                  —
                @endif
              </td>
              <td>
                <span class="badge text-bg-secondary">{{ $log->event }}</span>
              </td>
              <td>
                <span class="small">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</span>
              </td>
              <td class="small" style="max-width: 320px;">
                @if ($log->old_values || $log->new_values)
                  <details>
                    <summary>{{ __('backend.logs.view_details') }}</summary>
                    @if ($log->old_values)
                      <div class="mt-1"><strong>{{ __('backend.logs.old_values') }}:</strong>
                        <pre class="mb-0 small">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                      </div>
                    @endif
                    @if ($log->new_values)
                      <div class="mt-1"><strong>{{ __('backend.logs.new_values') }}:</strong>
                        <pre class="mb-0 small">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                      </div>
                    @endif
                  </details>
                @else
                  —
                @endif
              </td>
              <td><code>{{ $log->ip_address ?: '—' }}</code></td>
              <td class="text-nowrap">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($logs->hasPages())
      <div class="card-footer">{{ $logs->links() }}</div>
    @endif
  </div>
@endsection
