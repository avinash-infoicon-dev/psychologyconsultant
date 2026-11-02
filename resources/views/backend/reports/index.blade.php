@extends('backend.layouts.app')

@section('title', __('backend.reports.title'))
@section('page_title', __('backend.reports.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.reports') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.reports.title') }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
        <select name="status" class="form-select form-select-sm">
          <option value="">{{ __('backend.reports.all_statuses') }}</option>
          @foreach (\App\Models\Report::STATUSES as $status)
            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
              {{ __('backend.reports.status_'.$status) }}
            </option>
          @endforeach
        </select>
        <select name="type" class="form-select form-select-sm">
          <option value="">{{ __('backend.reports.all_types') }}</option>
          @foreach (\App\Models\Report::TYPES as $type)
            <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>
              {{ __('backend.reports.type_'.$type) }}
            </option>
          @endforeach
        </select>
        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('backend.reports.reporter') }}</th>
            <th>{{ __('backend.reports.reported') }}</th>
            <th>{{ __('backend.reports.type') }}</th>
            <th>{{ __('backend.reports.reason') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.reports.created_at') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($reports as $item)
            <tr>
              <td>{{ $item->id }}</td>
              <td>{{ $item->reporter?->name ?: '—' }}</td>
              <td>{{ $item->reportedUser?->name ?: '—' }}</td>
              <td>{{ __('backend.reports.type_'.$item->type) }}</td>
              <td>{{ __('backend.reports.reason_'.$item->reason) }}</td>
              <td><span class="badge {{ $item->adminBadgeClass() }}">{{ __('backend.reports.status_'.$item->status) }}</span></td>
              <td>{{ $item->created_at?->format('d.m.Y H:i') }}</td>
              <td>
                <a href="{{ route('admin.reports.show', $item) }}" class="btn btn-sm btn-outline-primary">
                  {{ __('backend.reports.view') }}
                </a>
              </td>
            </tr>
          @empty
            @include('backend.partials.empty-row', [
              'colspan' => 8,
              'title' => ! empty($filters['status']) || ! empty($filters['type']) || ! empty($filters['q'])
                ? __('backend.empty_filtered')
                : __('backend.no_records'),
              'hint' => null,
            ])
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($reports->hasPages())
      <div class="card-footer">{{ $reports->links() }}</div>
    @endif
  </div>
@endsection
