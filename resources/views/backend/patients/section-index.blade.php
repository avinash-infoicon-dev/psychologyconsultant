@extends('backend.layouts.app')

@section('title', $pageTitle)
@section('page_title', $pageTitle)
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item active">{{ $pageTitle }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ $pageTitle }}</h3>
      <form method="get" class="d-flex gap-2 align-items-center">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
        <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
      </form>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.email') }}</th>
            <th>{{ __('backend.patients.psychologist') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($patients as $patient)
            <tr>
              <td>{{ $patient->user?->name ?: '—' }}</td>
              <td>{{ $patient->user?->email ?: '—' }}</td>
              <td>{{ $patient->psychologist?->user?->name ?: ($patient->psychologist?->name ?? '—') }}</td>
              <td class="text-nowrap">
                <a href="{{ route($actionRoute, $patient) }}" class="btn btn-sm btn-primary">{{ $actionLabel }}</a>
                <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.patients.view_details') }}</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center py-4">{{ request()->filled('q') ? __('backend.empty_filtered') : __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($patients->hasPages())
      <div class="card-footer">{{ $patients->links() }}</div>
    @endif
  </div>
@endsection
