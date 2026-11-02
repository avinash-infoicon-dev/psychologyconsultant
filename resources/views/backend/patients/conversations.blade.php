@extends('backend.layouts.app')

@section('title', __('backend.patients.section_conversations'))
@section('page_title', __('backend.patients.section_conversations'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">{{ __('backend.menu.patients') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.patients.show', $patient) }}">{{ $patient->user?->name ?? __('backend.patients.details') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.patients.section_conversations') }}</li>
@endsection

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.patients.view_details') }}
    </a>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title mb-0">{{ $patient->user?->name ?? __('backend.patients.details') }} — {{ __('backend.patients.section_conversations') }}</h3></div>
    <div class="card-body p-0">
      <ul class="list-group list-group-flush">
        @forelse ($conversations as $conversation)
          <li class="list-group-item">
            <strong>{{ $conversation->psychologist?->user?->name ?: ($conversation->psychologist?->name ?? '—') }}</strong>
            <div class="text-muted small">
              {{ $conversation->status }}
              ·
              {{ $conversation->last_message_at?->format('d M Y H:i') ?: '—' }}
            </div>
          </li>
        @empty
          <li class="list-group-item text-muted">{{ __('backend.patients.empty_section') }}</li>
        @endforelse
      </ul>
    </div>
    @if ($conversations->hasPages())
      <div class="card-footer">{{ $conversations->links() }}</div>
    @endif
  </div>
@endsection
