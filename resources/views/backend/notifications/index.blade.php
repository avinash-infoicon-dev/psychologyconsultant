@extends('backend.layouts.app')

@section('title', __('backend.notifications.title'))
@section('page_title', __('backend.notifications.title'))

@section('content')
  <div
    class="card"
    data-admin-notification-inbox
    data-feed-url="{{ route('admin.notifications.feed') }}"
    data-open-label="{{ __('backend.notifications.open') }}"
    data-mark-read-label="{{ __('backend.notifications.mark_read') }}"
    data-just-now-label="{{ __('backend.notifications.just_now') }}"
    data-read-url-template="{{ str_replace('0', '__ID__', route('admin.notifications.read', 0)) }}"
    data-csrf="{{ csrf_token() }}"
  >
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>
        {{ __('backend.notifications.title') }}
        <span
          class="text-muted fw-normal"
          data-notification-unread-label
          data-template="{{ __('backend.notifications.unread_count', ['count' => '__COUNT__']) }}"
          @if (($unreadCount ?? 0) < 1) hidden @endif
        >· {{ __('backend.notifications.unread_count', ['count' => $unreadCount ?? 0]) }}</span>
      </span>
      <form action="{{ route('admin.notifications.read-all') }}" method="post">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('backend.notifications.mark_all_read') }}</button>
      </form>
    </div>
    <div class="card-body p-0">
      <div class="list-group list-group-flush" data-admin-notification-list>
        @forelse ($notifications as $item)
          <div class="list-group-item d-flex justify-content-between align-items-start gap-3 {{ $item->read_at ? '' : 'list-group-item-warning' }}" data-notification-id="{{ $item->id }}">
            <div>
              <strong>{{ $item->title }}</strong>
              <p class="mb-1">{{ $item->body }}</p>
              <small class="text-muted">{{ $item->created_at?->diffForHumans() }}</small>
            </div>
            <div class="d-flex gap-2">
              @php $url = is_array($item->data) ? ($item->data['url'] ?? null) : null; @endphp
              @if ($url)
                <a href="{{ $url }}" class="btn btn-sm btn-primary">{{ __('backend.notifications.open') }}</a>
              @endif
              @if (! $item->read_at)
                <form action="{{ route('admin.notifications.read', $item) }}" method="post">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('backend.notifications.mark_read') }}</button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <div class="list-group-item" data-admin-notification-empty>{{ __('backend.notifications.empty') }}</div>
        @endforelse
      </div>
    </div>
    @if ($notifications->hasPages())
      <div class="card-footer">
        {{ $notifications->links() }}
      </div>
    @endif
  </div>
@endsection
