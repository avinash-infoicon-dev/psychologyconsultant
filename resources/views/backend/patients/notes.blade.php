@extends('backend.layouts.app')

@section('title', __('backend.patients.section_notes'))
@section('page_title', __('backend.patients.section_notes'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient) }}">{{ $patient->user?->name ?? __('backend.patients.details') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.patients.section_notes') }}</li>
@endsection

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.patients.view_details') }}
    </a>
  </div>

  {{-- Compact list (replaced to match psychologist clinical notes):
  <div class="card">
    <div class="card-header"><h3 class="card-title mb-0">{{ $patient->user?->name ?? __('backend.patients.details') }} — {{ __('backend.patients.section_notes') }}</h3></div>
    <div class="card-body p-0">
      <ul class="list-group list-group-flush">
        @forelse ($clinicalNotes as $note)
          <li class="list-group-item">
            <div class="text-muted small mb-1">{{ $note->created_at?->format('d M Y H:i') }}</div>
            <div>{{ \Illuminate\Support\Str::limit($note->content, 180) }}</div>
          </li>
        @empty
          <li class="list-group-item text-muted">{{ __('backend.patients.empty_section') }}</li>
        @endforelse
      </ul>
    </div>
    @if ($clinicalNotes->hasPages())
      <div class="card-footer">{{ $clinicalNotes->links() }}</div>
    @endif
  </div>
  --}}

  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0">{{ $patient->user?->name ?? __('backend.patients.details') }} — {{ __('backend.patients.section_notes') }}</h3>
    </div>
    <div class="card-body p-0">
      @forelse ($clinicalNotes as $note)
        <div class="border-bottom p-3">
          <div class="d-flex flex-wrap justify-content-between gap-2 text-muted small mb-2">
            <span>{{ $note->created_at?->format('d M Y H:i') }}</span>
            <span>{{ $note->psychologist?->user?->name ?: ($note->psychologist?->name ?? '') }}</span>
          </div>
          <div style="white-space:pre-wrap;">{{ $note->content }}</div>
        </div>
      @empty
        <p class="text-muted p-3 mb-0">{{ __('backend.patients.empty_section') }}</p>
      @endforelse
    </div>
    @if ($clinicalNotes->hasPages())
      <div class="card-footer">{{ $clinicalNotes->links() }}</div>
    @endif
  </div>
@endsection
