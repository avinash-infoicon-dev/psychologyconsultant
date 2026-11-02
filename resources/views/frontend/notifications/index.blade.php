@extends('frontend.layouts.dashboard')

@section('title', __('frontend.notifications.history_title'))
@section('page_title', __('frontend.notifications.history_title'))

@section('content')
  <div
    class="fe-cms-card"
    data-fe-notification-inbox
    data-open-label="{{ __('frontend.notifications.open') }}"
    data-mark-read-label="{{ __('frontend.notifications.mark_read') }}"
    data-delete-label="{{ __('frontend.notifications.delete') }}"
    data-confirm-delete="{{ __('frontend.notifications.confirm_delete') }}"
    data-read-url-template="{{ str_replace('0', '__ID__', route('frontend.notifications.read', 0)) }}"
    data-delete-url-template="{{ str_replace('0', '__ID__', route('frontend.notifications.destroy', 0)) }}"
    data-csrf="{{ csrf_token() }}"
  >
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div>
        <p class="mb-1 fe-muted">{{ __('frontend.notifications.history_hint') }}</p>
        <p class="mb-0 fw-semibold">
          {{ __('frontend.notifications.history_total', ['count' => $notifications->total()]) }}
          <span
            class="fe-muted"
            data-notification-unread-label
            data-template="{{ __('frontend.notifications.history_unread', ['count' => '__COUNT__']) }}"
            @if ($unreadCount < 1) hidden @endif
          >· {{ __('frontend.notifications.history_unread', ['count' => $unreadCount]) }}</span>
        </p>
      </div>
      <form action="{{ route('frontend.notifications.read-all') }}" method="post">
        @csrf
        <button type="submit" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('frontend.notifications.mark_all_read') }}</button>
      </form>
    </div>

    <div data-fe-notification-list>
    @forelse ($notifications as $item)
      <article class="fe-notification-row {{ $item->read_at ? 'is-read' : 'is-unread' }}" data-notification-id="{{ $item->id }}">
        <div>
          <strong>{{ $item->localizedTitle() }}</strong>
          <p class="mb-1">{{ $item->localizedBody() }}</p>
          <small class="fe-muted">{{ $item->created_at?->diffForHumans() }}</small>
        </div>
        {{-- Old: <div class="d-flex flex-wrap gap-2"> — Open <a> stretched to the row height. --}}
        <div class="fe-notification-row__actions">
          @if (! $item->read_at)
            <form action="{{ route('frontend.notifications.read', $item) }}" method="post">
              @csrf
              <button type="submit" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('frontend.notifications.mark_read') }}</button>
            </form>
          @endif
          @php $url = is_array($item->data) ? ($item->data['url'] ?? null) : null; @endphp
          @if ($url)
            <a href="{{ $url }}" class="fe-btn fe-btn--primary fe-btn--sm">{{ __('frontend.notifications.open') }}</a>
          @endif
          <form action="{{ route('frontend.notifications.destroy', $item) }}" method="post" onsubmit="return confirm(@json(__('frontend.notifications.confirm_delete')))">
            @csrf
            @method('DELETE')
            <button type="submit" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('frontend.notifications.delete') }}</button>
          </form>
        </div>
      </article>
    @empty
      <div data-fe-notification-empty>
        @include('frontend.partials.ui-empty', [
          'title' => __('frontend.notifications.history_empty'),
          'hint' => __('frontend.notifications.history_empty_hint'),
        ])
      </div>
    @endforelse
    </div>

    @if ($notifications->total() > 0)
      <div class="fe-pagination d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
        <p class="mb-0 fe-muted">
          {{ __('frontend.notifications.history_showing', [
            'from' => $notifications->firstItem() ?: 0,
            'to' => $notifications->lastItem() ?: 0,
            'total' => $notifications->total(),
          ]) }}
        </p>
        {{ $notifications->links() }}
      </div>
    @endif
  </div>

  {{-- Preferences moved to admin Settings → Notifications. --}}
  {{-- Restored: users can set types the admin has not applied globally. --}}
  {{--@include('frontend.partials.notification-preferences')--}}
@endsection
