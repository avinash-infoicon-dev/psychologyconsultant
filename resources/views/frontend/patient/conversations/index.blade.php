@extends('frontend.layouts.dashboard')

@section('title', __('frontend.dashboard.quick_conversations'))
@section('page_title', __('frontend.dashboard.quick_conversations'))

@section('content')
  {{-- Chat UI is rendered by frontend.patient.conversations._chat (chat-message.html design). --}}
  {{--
  Legacy list layout kept for reference:

  <div class="fe-form-actions" style="margin-top:0;margin-bottom:1rem;">
    <form action="{{ route('frontend.patient.conversations.store') }}" method="post">
      @csrf
      <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.dashboard.start_conversation') }}</button>
    </form>
  </div>

  <div class="fe-cms-list">
    @forelse ($conversations as $conversation)
      <a href="{{ route('frontend.patient.conversations.show', $conversation->uuid) }}" class="fe-cms-list__item">
        <div>
          <h3>{{ $conversation->psychologist?->user?->name ?? '—' }}</h3>
          <p>
            {{ $conversation->psychologist?->specialization }}
            @if ($conversation->last_message_at)
              · {{ $conversation->last_message_at->diffForHumans() }}
            @endif
          </p>
        </div>
        <i class="bi bi-arrow-right"></i>
      </a>
    @empty
      <div class="fe-cms-card">
        <p style="margin:0;color:var(--fe-muted);">{{ __('backend.no_records') }}</p>
      </div>
    @endforelse
  </div>

  @if ($conversations->hasPages())
    <div class="fe-pagination">{{ $conversations->links() }}</div>
  @endif
  --}}

  @include('frontend.patient.conversations._chat', [
      'conversations' => $conversations,
      'activeConversation' => $activeConversation ?? null,
      'messages' => $messages ?? collect(),
  ])
@endsection
