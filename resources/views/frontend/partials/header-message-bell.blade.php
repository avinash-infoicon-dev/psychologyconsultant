@auth
  @if (auth()->user()->isPatient() || auth()->user()->isPsychologist())
    @php
      $messageBell = app(\App\Services\NotificationService::class)
          ->inboxSummary(auth()->user());
      $messageBellInbox = route('frontend.notifications.index');
    @endphp

    <div
      class="fe-header-bell"
      data-fe-dropdown
      data-message-bell
      data-message-bell-url="{{ route('frontend.notifications.feed') }}"
      data-message-bell-inbox="{{ $messageBellInbox }}"
      data-message-bell-user-id="{{ auth()->id() }}"
      data-message-bell-label-empty="{{ __('frontend.notifications.bell_empty') }}"
      data-message-bell-label-new="{{ __('frontend.notifications.bell_new_message') }}"
      data-message-bell-label-notification="{{ __('frontend.notifications.bell_new') }}"
    >
      <button
        type="button"
        class="fe-header-bell__btn"
        data-fe-dropdown-toggle
        aria-expanded="false"
        aria-haspopup="true"
        title="{{ __('frontend.notifications.bell_title') }}"
        aria-label="{{ __('frontend.notifications.bell_title') }}"
      >
        <i class="bi bi-bell" data-message-bell-icon></i>
        <span
          class="fe-header-bell__badge"
          data-message-bell-badge
          data-notification-unread-badge
          @if ($messageBell['unread_count'] < 1) hidden @endif
        >{{ $messageBell['unread_count'] > 99 ? '99+' : $messageBell['unread_count'] }}</span>
      </button>

      <div class="fe-header-bell__menu" data-fe-dropdown-menu data-message-bell-menu hidden>
        <div class="fe-header-bell__head">
          <strong>{{ __('frontend.notifications.bell_title') }}</strong>
          <a href="{{ $messageBellInbox }}">{{ __('frontend.notifications.bell_view_all') }}</a>
        </div>

        <div class="fe-header-bell__list" data-message-bell-list>
          @forelse ($messageBell['notifications'] as $item)
            <a
              href="{{ $item['url'] ?: $messageBellInbox }}"
              class="fe-header-bell__item {{ empty($item['read_at']) ? 'is-unread' : '' }}"
            >
              <span class="fe-header-bell__item-title">{{ $item['title'] }}</span>
              <span class="fe-header-bell__item-preview">{{ $item['body'] }}</span>
            </a>
          @empty
            <p class="fe-header-bell__empty" data-message-bell-empty>
              {{ __('frontend.notifications.bell_empty') }}
            </p>
          @endforelse
        </div>
      </div>

      <div class="fe-header-bell__toast" data-message-bell-toast hidden>
        {{ __('frontend.notifications.bell_new') }}
      </div>
    </div>
  @endif
@endauth




<?php
/*
{{--Legacy code for reference--}}
@auth
  @if (auth()->user()->isPatient() || auth()->user()->isPsychologist())
    @php
      $messageBell = app(\App\Services\Messaging\ConversationService::class)
          ->unreadSummaryFor(auth()->user());
      $messageBellPrefix = auth()->user()->isPsychologist()
          ? 'frontend.psychologist'
          : 'frontend.patient';
    @endphp

    <div
      class="fe-header-bell"
      data-fe-dropdown
      data-message-bell
      data-message-bell-url="{{ route($messageBellPrefix.'.messages.unread-summary') }}"
      data-message-bell-inbox="{{ $messageBell['inbox_href'] }}"
      data-message-bell-user-id="{{ auth()->id() }}"
      data-message-bell-label-empty="{{ __('frontend.notifications.bell_empty') }}"
      data-message-bell-label-new="{{ __('frontend.notifications.bell_new_message') }}"
    >
      <button
        type="button"
        class="fe-header-bell__btn"
        data-fe-dropdown-toggle
        aria-expanded="false"
        aria-haspopup="true"
        title="{{ __('frontend.notifications.bell_title') }}"
        aria-label="{{ __('frontend.notifications.bell_title') }}"
      >
        <i class="bi bi-bell" data-message-bell-icon></i>
        <span
          class="fe-header-bell__badge"
          data-message-bell-badge
          @if ($messageBell['total'] < 1) hidden @endif
        >{{ $messageBell['total'] > 99 ? '99+' : $messageBell['total'] }}</span>
      </button>

      <div class="fe-header-bell__menu" data-fe-dropdown-menu data-message-bell-menu hidden>
        <div class="fe-header-bell__head">
          <strong>{{ __('frontend.notifications.bell_title') }}</strong>
          <a href="{{ $messageBell['inbox_href'] }}">{{ __('frontend.notifications.bell_view_all') }}</a>
        </div>

        <div class="fe-header-bell__list" data-message-bell-list>
          @forelse ($messageBell['conversations'] as $item)
            <a href="{{ $item['href'] }}" class="fe-header-bell__item">
              <span class="fe-header-bell__item-title">
                {{ $item['name'] }}
                <em>{{ $item['unread'] }}</em>
              </span>
              <span class="fe-header-bell__item-preview">{{ $item['preview'] }}</span>
            </a>
          @empty
            <p class="fe-header-bell__empty" data-message-bell-empty>
              {{ __('frontend.notifications.bell_empty') }}
            </p>
          @endforelse
        </div>
      </div>

      <div class="fe-header-bell__toast" data-message-bell-toast hidden>
        {{ __('frontend.notifications.bell_new_message') }}
      </div>
    </div>
  @endif
@endauth
*/
?>