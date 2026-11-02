@extends('backend.layouts.app')

@section('title', __('backend.psychologists.title'))
@section('page_title', __('backend.psychologists.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.psychologists') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.psychologists.title') }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        {{-- Old verified 0/1 filter
        <select name="verified" class="form-select form-select-sm">
          <option value="">{{ __('backend.psychologists.verification') }}</option>
          <option value="0" @selected(request('verified') === '0')>{{ __('backend.psychologists.pending') }}</option>
          <option value="1" @selected(request('verified') === '1')>{{ __('backend.psychologists.verified') }}</option>
        </select>
        --}}
        <select name="verification_status" class="form-select form-select-sm">
          <option value="">{{ __('backend.psychologists.all_statuses') }}</option>
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
            <th>{{ __('backend.psychologists.specialization') }}</th>
            <th>{{ __('backend.psychologists.email_verified') }}</th>
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
              <td>{{ $psychologist->specialization ?: '—' }}</td>
              <td>
                @if ($psychologist->user?->hasVerifiedEmail())
                  <span class="badge text-bg-success">{{ __('backend.yes') }}</span>
                @else
                  <span class="badge text-bg-warning">{{ __('backend.no') }}</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $badge }}">{{ __('backend.psychologists.status_'.$status) }}</span>
              </td>
              <td class="text-nowrap">
                <a href="{{ route('admin.psychologists.show', $psychologist) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.psychologists.view_details') }}</a>
                <a href="{{ route('admin.psychologists.verification.show', $psychologist) }}" class="btn btn-sm btn-outline-secondary">{{ __('backend.psychologists.section_verification') }}</a>
                {{-- Old inline approve/revoke
                @permission('psychologists.verify')
                  ...
                @endpermission
                --}}
              </td>
            </tr>
          @empty
            @include('backend.partials.empty-row', [
              'colspan' => 7,
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
