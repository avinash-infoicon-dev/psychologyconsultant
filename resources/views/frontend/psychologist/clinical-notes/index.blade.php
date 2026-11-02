@extends('frontend.layouts.dashboard')

@section('title', __('frontend.clinical_notes.title'))
@section('page_title', __('frontend.clinical_notes.title'))

@section('content')
  {{-- Success flash is rendered by frontend.layouts.dashboard --}}
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ __('frontend.clinical_notes.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">
          {{ $patient->user?->name }}
          @if ($conversation)
            · {{ __('frontend.clinical_notes.for_conversation') }}
          @endif
        </p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('frontend.psychologist.patients.show', $patient) }}" class="fe-btn fe-btn--outline">{{ __('frontend.patients.view') }}</a>
        <a
          href="{{ route('frontend.psychologist.patients.clinical-notes.create', ['patient' => $patient] + ($conversation ? ['conversation_id' => $conversation->id] : [])) }}"
          class="fe-btn fe-btn--primary"
        >{{ __('frontend.clinical_notes.create') }}</a>
      </div>
    </div>

    @forelse ($notes as $note)
      <article class="fe-note-card">
        <div class="fe-note-card__meta">
          <span>{{ $note->created_at?->format('d M Y H:i') }}</span>
          @if ($note->isEditable())
            <a href="{{ route('frontend.psychologist.clinical-notes.edit', $note) }}">{{ __('frontend.edit') }}</a>
          @else
            <span class="fe-muted">{{ __('frontend.clinical_notes.read_only') }}</span>
          @endif
        </div>
        <p style="white-space:pre-wrap;">{{ $note->content }}</p>
      </article>
    @empty
      <p class="fe-muted">{{ __('frontend.clinical_notes.empty') }}</p>
    @endforelse

    @if ($notes->hasPages())
      <div style="margin-top:1rem;">{{ $notes->links() }}</div>
    @endif
  </div>
@endsection
