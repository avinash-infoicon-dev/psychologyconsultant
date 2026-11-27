@extends('backend.layouts.app')

@section('title', __('backend.notifications.title'))
@section('page_title', __('backend.notifications.title'))

@push('styles')
  <style>
    .admin-inbox {
      --inbox-ink: #17243b;
      --inbox-muted: #748198;
      --inbox-line: #e7ecf3;
    }

    .admin-inbox__panel {
      overflow: hidden;
      border: 1px solid var(--inbox-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 8px 26px rgba(24, 39, 75, .055);
    }

    .admin-inbox__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.25rem 1.4rem;
      border-bottom: 1px solid var(--inbox-line);
      background: linear-gradient(110deg, #f3f7ff, #fff 70%);
    }

    .admin-inbox__heading {
      display: flex;
      align-items: center;
      gap: .9rem;
      min-width: 0;
    }

    .admin-inbox__heading-icon {
      display: grid;
      width: 2.9rem;
      height: 2.9rem;
      flex: 0 0 2.9rem;
      place-items: center;
      border-radius: .9rem;
      background: linear-gradient(145deg, #477be7, #2855bd);
      box-shadow: 0 6px 15px rgba(53, 103, 214, .2);
      color: #fff;
      font-size: 1.15rem;
    }

    .admin-inbox__title {
      margin: 0 0 .2rem;
      color: var(--inbox-ink);
      font-size: 1.1rem;
      font-weight: 700;
    }

    .admin-inbox__subtitle {
      margin: 0;
      color: var(--inbox-muted);
      font-size: .82rem;
    }

    .admin-inbox__header-actions {
      display: flex;
      align-items: center;
      gap: .75rem;
    }

    .admin-inbox__unread {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .45rem .7rem;
      border: 1px solid #dfe8fb;
      border-radius: 999px;
      background: #f3f7ff;
      color: #315ebd;
      font-size: .78rem;
      font-weight: 700;
      white-space: nowrap;
    }

    .admin-inbox__unread::before {
      width: .45rem;
      height: .45rem;
      border-radius: 50%;
      background: #477be7;
      content: "";
    }

    .admin-inbox__mark-all {
      display: inline-flex;
      min-height: 2.35rem;
      align-items: center;
      gap: .45rem;
      padding: .45rem .75rem;
      border: 1px solid #dce3ed;
      border-radius: .65rem;
      background: #fff;
      color: #53637a;
      font-size: .78rem;
      font-weight: 650;
      white-space: nowrap;
    }

    .admin-inbox__mark-all:hover {
      border-color: #b6c8ef;
      background: #f7f9ff;
      color: #315ebd;
    }

    .admin-inbox__list {
      display: grid;
      gap: .7rem;
      padding: 1rem;
      background: #f8faff;
    }

    .admin-inbox__item {
      position: relative;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: start;
      gap: .9rem;
      padding: 1rem 1.05rem;
      border: 1px solid var(--inbox-line);
      border-radius: .85rem;
      background: #fff;
      transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .admin-inbox__item:hover {
      transform: translateY(-1px);
      border-color: #d6e1f4;
      box-shadow: 0 5px 16px rgba(24, 39, 75, .055);
    }

    .admin-inbox__list > .list-group-item {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      gap: .9rem;
      padding: 1rem 1.05rem;
      border: 1px solid var(--inbox-line);
      border-radius: .85rem;
      background: #fff;
      color: inherit;
    }

    .admin-inbox__list > .list-group-item-warning {
      background: #fff;
    }

    .admin-inbox__list > .list-group-item > div:first-child {
      min-width: 0;
    }

    .admin-inbox__list > .list-group-item strong {
      color: #27364d;
      font-size: .91rem;
      font-weight: 700;
    }

    .admin-inbox__list > .list-group-item p {
      margin: .3rem 0 .45rem !important;
      color: #65748b;
      font-size: .84rem;
      line-height: 1.55;
      overflow-wrap: anywhere;
    }

    .admin-inbox__list > .list-group-item small {
      color: #8a97aa !important;
      font-size: .75rem;
    }

    .admin-inbox__list > .list-group-item > div:last-child {
      align-items: center;
      flex-wrap: wrap;
    }

    .admin-inbox__list > .list-group-item form {
      margin: 0;
    }

    .admin-inbox__item.is-unread {
      border-color: #d8e4fb;
      background: #fff;
    }

    .admin-inbox__item.is-unread::after {
      position: absolute;
      top: 1.2rem;
      right: 1.1rem;
      width: .5rem;
      height: .5rem;
      border-radius: 50%;
      background: #477be7;
      content: "";
    }

    .admin-inbox__item-icon {
      display: grid;
      width: 2.55rem;
      height: 2.55rem;
      flex: 0 0 2.55rem;
      place-items: center;
      border-radius: .8rem;
      background: var(--notification-soft, #edf3ff);
      color: var(--notification-color, #416bc7);
      font-size: 1.05rem;
    }

    .admin-inbox__item-icon--payment { --notification-color: #168263; --notification-soft: #e8f7f1; }
    .admin-inbox__item-icon--security { --notification-color: #b7791f; --notification-soft: #fff6e6; }
    .admin-inbox__item-icon--profile { --notification-color: #7353bb; --notification-soft: #f2edfc; }
    .admin-inbox__item-icon--report { --notification-color: #bd4d65; --notification-soft: #fff0f2; }

    .admin-inbox__item-content {
      min-width: 0;
      padding-right: .4rem;
    }

    .admin-inbox__item-title {
      display: block;
      margin: .05rem 0 .3rem;
      color: #27364d;
      font-size: .91rem;
      font-weight: 700;
      line-height: 1.4;
    }

    .admin-inbox__item.is-read .admin-inbox__item-title {
      font-weight: 600;
    }

    .admin-inbox__item-body {
      margin: 0 0 .45rem;
      color: #65748b;
      font-size: .84rem;
      line-height: 1.55;
      overflow-wrap: anywhere;
    }

    .admin-inbox__item-time {
      color: #8a97aa;
      font-size: .75rem;
    }

    .admin-inbox__item-actions {
      display: flex;
      align-items: center;
      gap: .45rem;
      padding-right: .85rem;
    }

    .admin-inbox__item-actions form {
      margin: 0;
    }

    .admin-inbox__action {
      display: inline-flex;
      min-height: 2.1rem;
      align-items: center;
      justify-content: center;
      gap: .35rem;
      padding: .4rem .65rem;
      border: 1px solid #dfe6f0;
      border-radius: .6rem;
      background: #fff;
      color: #53637a;
      font-size: .76rem;
      font-weight: 650;
      text-decoration: none;
      white-space: nowrap;
    }

    .admin-inbox__action:hover {
      border-color: #b6c8ef;
      background: #f7f9ff;
      color: #315ebd;
    }

    .admin-inbox__action--primary {
      border-color: #3567d6;
      background: #3567d6;
      color: #fff;
    }

    .admin-inbox__action--primary:hover {
      border-color: #2855bd;
      background: #2855bd;
      color: #fff;
    }

    .admin-inbox__empty {
      display: grid;
      justify-items: center;
      padding: 3.5rem 1.2rem;
      text-align: center;
    }

    .admin-inbox__empty-icon {
      display: grid;
      width: 3.3rem;
      height: 3.3rem;
      margin-bottom: .85rem;
      place-items: center;
      border-radius: 1rem;
      background: #edf3ff;
      color: #6684c3;
      font-size: 1.4rem;
    }

    .admin-inbox__empty strong {
      margin-bottom: .25rem;
      color: var(--inbox-ink);
      font-size: .95rem;
    }

    .admin-inbox__pagination {
      padding: .9rem 1.2rem;
      border-top: 1px solid var(--inbox-line);
    }

    [data-bs-theme="dark"] .admin-inbox__panel,
    [data-bs-theme="dark"] .admin-inbox__item,
    [data-bs-theme="dark"] .admin-inbox__list > .list-group-item,
    [data-bs-theme="dark"] .admin-inbox__mark-all,
    [data-bs-theme="dark"] .admin-inbox__action {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .admin-inbox__list > .list-group-item strong {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-inbox__list > .list-group-item p {
      color: #b7c3d6;
    }

    [data-bs-theme="dark"] .admin-inbox__list > .list-group-item small {
      color: #9cabc0 !important;
    }

    [data-bs-theme="dark"] .admin-inbox__header {
      border-color: #29374d;
      background: linear-gradient(110deg, #1a2639, #182233 70%);
    }

    [data-bs-theme="dark"] .admin-inbox__title,
    [data-bs-theme="dark"] .admin-inbox__item-title,
    [data-bs-theme="dark"] .admin-inbox__empty strong {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-inbox__list {
      background: #131d2b;
    }

    [data-bs-theme="dark"] .admin-inbox__item.is-unread {
      border-color: #33486b;
    }

    [data-bs-theme="dark"] .admin-inbox__mark-all,
    [data-bs-theme="dark"] .admin-inbox__action {
      color: #c3cee0;
    }

    [data-bs-theme="dark"] .admin-inbox__unread {
      border-color: #354765;
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .admin-inbox__item-icon--payment { --notification-soft: #173a35; }
    [data-bs-theme="dark"] .admin-inbox__item-icon--security { --notification-soft: #40331d; }
    [data-bs-theme="dark"] .admin-inbox__item-icon--profile { --notification-soft: #33284b; }
    [data-bs-theme="dark"] .admin-inbox__item-icon--report { --notification-soft: #452b35; }

    @media (max-width: 767.98px) {
      .admin-inbox__header {
        align-items: flex-start;
        flex-direction: column;
        padding: 1rem;
      }

      .admin-inbox__header-actions {
        width: 100%;
        justify-content: space-between;
      }

      .admin-inbox__list {
        padding: .7rem;
      }

      .admin-inbox__item {
        grid-template-columns: auto minmax(0, 1fr);
        gap: .7rem;
        padding: .85rem;
      }

      .admin-inbox__list > .list-group-item {
        grid-template-columns: minmax(0, 1fr);
        justify-items: start;
        gap: .7rem;
        padding: .85rem;
      }

      .admin-inbox__list > .list-group-item > div:last-child {
        grid-column: 1;
      }

      .admin-inbox__item-icon {
        width: 2.25rem;
        height: 2.25rem;
      }

      .admin-inbox__item-actions {
        grid-column: 2;
        flex-wrap: wrap;
        padding-right: 0;
      }
    }
  </style>
@endpush

@section('content')
  <div
    class="admin-inbox"
    data-admin-notification-inbox
    data-feed-url="{{ route('admin.notifications.feed') }}"
    data-open-label="{{ __('backend.notifications.open') }}"
    data-mark-read-label="{{ __('backend.notifications.mark_read') }}"
    data-just-now-label="{{ __('backend.notifications.just_now') }}"
    data-read-url-template="{{ route('admin.notifications.read', '__ID__') }}"
    data-csrf="{{ csrf_token() }}"
  >
    <section class="admin-inbox__panel" aria-labelledby="admin-inbox-title">
      <header class="admin-inbox__header">
        <div class="admin-inbox__heading">
          <span class="admin-inbox__heading-icon" aria-hidden="true"><i class="bi bi-bell"></i></span>
          <div>
            <h1 class="admin-inbox__title" id="admin-inbox-title">{{ __('backend.notifications.title') }}</h1>
            <p class="admin-inbox__subtitle">{{ __('backend.notifications.subtitle') }}</p>
          </div>
        </div>
        <div class="admin-inbox__header-actions">
          <span
            class="admin-inbox__unread"
            data-notification-unread-label
            data-template="{{ __('backend.notifications.unread_count', ['count' => '__COUNT__']) }}"
            @if (($unreadCount ?? 0) < 1) hidden @endif
          >{{ __('backend.notifications.unread_count', ['count' => $unreadCount ?? 0]) }}</span>
          <form action="{{ route('admin.notifications.read-all') }}" method="post">
            @csrf
            <button type="submit" class="admin-inbox__mark-all">
              <i class="bi bi-check2-all" aria-hidden="true"></i>{{ __('backend.notifications.mark_all_read') }}
            </button>
          </form>
        </div>
      </header>

      <div class="admin-inbox__list" data-admin-notification-list aria-label="{{ __('backend.notifications.recent') }}">
        @forelse ($notifications as $item)
          @php
            $iconClass = match (true) {
              str_contains($item->type, 'payment'), str_contains($item->type, 'subscription') => 'admin-inbox__item-icon--payment',
              str_contains($item->type, 'security'), str_contains($item->type, 'password') => 'admin-inbox__item-icon--security',
              str_contains($item->type, 'profile'), str_contains($item->type, 'account') => 'admin-inbox__item-icon--profile',
              str_contains($item->type, 'report') => 'admin-inbox__item-icon--report',
              default => '',
            };
            $iconName = match (true) {
              str_contains($item->type, 'payment'), str_contains($item->type, 'subscription') => 'bi-credit-card',
              str_contains($item->type, 'security'), str_contains($item->type, 'password') => 'bi-shield-lock',
              str_contains($item->type, 'profile'), str_contains($item->type, 'account') => 'bi-person-check',
              str_contains($item->type, 'report') => 'bi-flag',
              default => 'bi-bell',
            };
          @endphp
          <article
            class="admin-inbox__item {{ $item->read_at ? 'is-read' : 'is-unread' }}"
            data-notification-id="{{ $item->id }}"
          >
            <span class="admin-inbox__item-icon {{ $iconClass }}" aria-hidden="true">
              <i class="bi {{ $iconName }}"></i>
            </span>
            <div class="admin-inbox__item-content">
              <strong class="admin-inbox__item-title">{{ $item->title }}</strong>
              <p class="admin-inbox__item-body">{{ $item->body }}</p>
              <small class="admin-inbox__item-time">{{ $item->created_at?->diffForHumans() }}</small>
            </div>
            <div class="admin-inbox__item-actions">
              @php $url = $item->actionUrl(); @endphp
              @if ($url)
                <a href="{{ $url }}" class="admin-inbox__action admin-inbox__action--primary">
                  <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>{{ __('backend.notifications.open') }}
                </a>
              @endif
              @if (! $item->read_at)
                <form action="{{ route('admin.notifications.read', $item) }}" method="post">
                  @csrf
                  <button type="submit" class="admin-inbox__action">
                    <i class="bi bi-check2" aria-hidden="true"></i>{{ __('backend.notifications.mark_read') }}
                  </button>
                </form>
              @endif
            </div>
          </article>
        @empty
          <div class="admin-inbox__empty" data-admin-notification-empty>
            <span class="admin-inbox__empty-icon" aria-hidden="true"><i class="bi bi-bell-slash"></i></span>
            <strong>{{ __('backend.notifications.empty') }}</strong>
          </div>
        @endforelse
      </div>

      @if ($notifications->hasPages())
        <div class="admin-inbox__pagination">{{ $notifications->links() }}</div>
      @endif
    </section>
  </div>
@endsection
