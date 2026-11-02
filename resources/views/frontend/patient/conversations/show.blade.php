@extends('frontend.layouts.dashboard')

@section('title', $conversation->psychologist?->user?->name ?? __('frontend.dashboard.quick_conversations'))
@section('page_title', $conversation->psychologist?->user?->name ?? __('frontend.dashboard.quick_conversations'))

@section('content')
  {{-- Chat UI is rendered by frontend.patient.conversations._chat (chat-message.html design). --}}
  {{--
  Legacy detail layout kept for reference:

  <div class="fe-cms-card">
    <p style="margin:0 0 1rem;color:var(--fe-muted);">
      {{ $conversation->psychologist?->specialization }}
      · {{ __('backend.status') }}: {{ $conversation->status }}
    </p>

    @php($latest = $conversation->messages->first())
    @if ($latest)
      <div class="fe-cms-content">
        <strong>{{ $latest->sender?->name ?? __('backend.logs.user') }}</strong>
        <p style="margin:.5rem 0 0;">{{ $latest->body ?: ($latest->type === 'audio' ? 'Audio' : '—') }}</p>
        <p style="margin:.5rem 0 0;color:var(--fe-muted);font-size:.875rem;">{{ $latest->created_at?->diffForHumans() }}</p>
      </div>
    @else
      <p style="margin:0;color:var(--fe-muted);">{{ __('backend.no_records') }}</p>
    @endif

    <div class="fe-form-actions">
      <a href="{{ route('frontend.patient.conversations.index') }}" class="fe-btn fe-btn--outline">{{ __('backend.back') }}</a>
    </div>
  </div>
  --}}

  @include('frontend.patient.conversations._chat', [
      'conversations' => $conversations,
      'activeConversation' => $activeConversation ?? $conversation,
      'messages' => $messages,
      'hasOlderMessages' => $hasOlderMessages ?? false,
      'nextMessagesPage' => $nextMessagesPage ?? null,
  ])
@endsection
