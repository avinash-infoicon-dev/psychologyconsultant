@extends('backend.layouts.app')

@section('title', __('backend.psychologists.section_verification'))
@section('page_title', __('backend.psychologists.section_verification'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.psychologists.index') }}">{{ __('backend.menu.psychologists') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.psychologists.section_verification') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.psychologists.section_verification') }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <select name="verification_status" class="form-select form-select-sm">
          <option value="">{{ __('backend.psychologists.queue_statuses') }}</option>
          @foreach (\App\Models\Professional::VERIFICATION_STATUSES as $status)
            <option value="{{ $status }}" @selected(request('verification_status') === $status)>
              {{ __('backend.psychologists.status_'.$status) }}
            </option>
          @endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.email') }}</th>
            <th>{{ __('backend.psychologists.license') }}</th>
            <th>{{ __('backend.psychologists.verification') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($psychologists as $psychologist)
            @php
              $status = $psychologist->verification_status ?: 'draft';
              $badge = match ($status) {
                'verified' => 'text-bg-success',
                'submitted', 'under_review' => 'text-bg-warning',
                'rejected' => 'text-bg-danger',
                'suspended' => 'text-bg-dark',
                default => 'text-bg-secondary',
              };
            @endphp
            <tr>
              <td>{{ $psychologist->user?->name ?: '—' }}</td>
              <td>{{ $psychologist->user?->email ?: '—' }}</td>
              <td><code>{{ $psychologist->license_number ?: '—' }}</code></td>
              <td>
                <span class="badge {{ $badge }}">{{ __('backend.psychologists.status_'.$status) }}</span>
              </td>
              <td class="text-nowrap">
                <a href="{{ route('admin.psychologists.verification.show', $psychologist) }}" class="btn btn-sm btn-primary">{{ __('backend.psychologists.section_verification') }}</a>
                <a href="{{ route('admin.psychologists.show', $psychologist) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.psychologists.view_details') }}</a>
              </td>
            </tr>
          @empty
            @include('backend.partials.empty-row', [
              'colspan' => 5,
              'title' => request()->filled('q') || request()->filled('verification_status')
                ? __('backend.empty_filtered')
                : __('backend.no_records'),
            ])
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($psychologists->hasPages())
      <div class="card-footer">{{ $psychologists->links() }}</div>
    @endif
  </div>
@endsection
