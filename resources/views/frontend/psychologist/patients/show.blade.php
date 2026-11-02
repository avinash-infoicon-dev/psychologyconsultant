@extends('frontend.layouts.dashboard')

@section('title', $patient->user?->name ?? __('frontend.patients.title'))
@section('page_title', $patient->user?->name ?? __('frontend.patients.title'))

@section('content')
  {{-- Success flash is rendered by frontend.layouts.dashboard --}}
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ $patient->user?->name }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ $patient->user?->email }}</p>
      </div>
      <a href="{{ route('frontend.psychologist.patients.clinical-notes.create', $patient) }}" class="fe-btn fe-btn--primary">
        {{ __('frontend.clinical_notes.create') }}
      </a>
    </div>

    <h3>{{ __('frontend.clinical_notes.title') }}</h3>
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
        <p>{{ \Illuminate\Support\Str::limit($note->content, 280) }}</p>
      </article>
    @empty
      <p class="fe-muted">{{ __('frontend.clinical_notes.empty') }}</p>
    @endforelse

    @if ($notes->hasPages())
      <div style="margin-top:1rem;">{{ $notes->links() }}</div>
    @endif

    <h3 style="margin-top:1.5rem;">{{ __('frontend.patients.conversations') }}</h3>
    <ul class="fe-simple-list">
      @forelse ($conversations as $conversation)
        <li>
          <a href="{{ route('frontend.psychologist.conversations.show', $conversation) }}">
            {{ __('frontend.patients.open_conversation') }}
            <span class="fe-muted">{{ $conversation->last_message_at?->format('d M Y H:i') ?: '—' }}</span>
          </a>
        </li>
      @empty
        <li class="fe-muted">{{ __('frontend.patients.no_conversations') }}</li>
      @endforelse
    </ul>
  </div>
@endsection
