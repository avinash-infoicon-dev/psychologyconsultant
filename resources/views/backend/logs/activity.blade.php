@extends('backend.layouts.app')

@section('title', __('backend.logs.activity_title'))
@section('page_title', __('backend.logs.activity_title'))
@section('breadcrumb')
  <li class="breadcrumb-item">{{ __('backend.menu.logs') }}</li>
  <li class="breadcrumb-item active">{{ __('backend.logs.activity') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.logs.activity_title') }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <input type="text" name="action" value="{{ request('action') }}" class="form-control form-control-sm" placeholder="{{ __('backend.logs.action') }}">
        <input type="number" name="user_id" value="{{ request('user_id') }}" class="form-control form-control-sm" placeholder="{{ __('backend.logs.user_id') }}" min="1">
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.logs.id') }}</th>
            <th>{{ __('backend.logs.user') }}</th>
            <th>{{ __('backend.logs.action') }}</th>
            <th>{{ __('backend.logs.subject') }}</th>
            <th>{{ __('backend.logs.description') }}</th>
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
              <td><code>{{ $log->action }}</code></td>
              <td>
                @if ($log->subject_type)
                  <span class="small">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                @else
                  —
                @endif
              </td>
              <td>{{ $log->description ?: '—' }}</td>
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
