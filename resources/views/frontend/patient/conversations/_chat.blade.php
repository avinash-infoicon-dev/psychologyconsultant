@php
  $authUser = auth()->user();
  $isPsychologist = $authUser->isPsychologist();
  $routePrefix = $chatRoutePrefix ?? ($isPsychologist ? 'frontend.psychologist' : 'frontend.patient');
  $active = $activeConversation ?? null;
  $active?->loadMissing('psychologist.user');
  $quotaService = app(\App\Services\Messaging\ExchangeQuotaService::class);
  $allowsAudioChannel = $isPsychologist
      || $quotaService->patientAllowsChannel($authUser, \App\Models\Plan::CHANNEL_AUDIO);
  // Dictation = record voice → transcript → send as text (allowed on On-demand / Essential).
  $allowsDictationChannel = $isPsychologist
      || $quotaService->patientAllowsChannel($authUser, \App\Models\Plan::CHANNEL_DICTATION);
  $allowsMic = $allowsAudioChannel || $allowsDictationChannel;
  // Plan → global Messaging settings → config/messaging.php (0 skips to the next).
  $maxTextChars = \App\Support\MessagingLimits::maxTextChars($authUser);
  $maxAudioSeconds = \App\Support\MessagingLimits::maxAudioSeconds($authUser);
  $enforceTextLimit = $maxTextChars !== null;
  $enforceAudioLimit = $maxAudioSeconds !== null;

  $blockNewExchange = false;
  if (! $isPsychologist) {
      $remaining = (int) ($quotaService->balanceForPatient($authUser->patient)['remaining'] ?? 0);
      $openExchange = $active
          ? \App\Models\Exchange::query()
              ->where('conversation_id', $active->id)
              ->where('status', \App\Models\Exchange::STATUS_PENDING)
              ->exists()
          : false;
      $blockNewExchange = $remaining < 1 && ! $openExchange;
  }

  $exchangeMessageUsage = $active
      ? $quotaService->currentExchangeMessageUsage($active, $authUser)
      : null;
  $blockExchangeMessages = (bool) ($exchangeMessageUsage['at_cap'] ?? false);
  $pauseMessaging = $active
      ? \App\Support\PsychologistCareStatus::messagingPaused($active->psychologist)
      : false;

  if ($isPsychologist) {
      $peer = $active?->patient;
      $peerName = $peer?->user?->name
          ?? trim(($peer?->first_name ?? '').' '.($peer?->last_name ?? ''))
          ?: __('frontend.chat.unknown');
      $peerSpecialization = null;
  } else {
      $peer = $active?->psychologist;
      $peerName = $peer?->user?->name
          ?? trim(($peer?->first_name ?? '').' '.($peer?->last_name ?? ''))
          ?: __('frontend.chat.unknown');
      $peerSpecialization = $peer?->specialization;
  }

  $peerInitials = collect(preg_split('/\s+/', trim($peerName)))
      ->filter()
      ->take(2)
      ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
      ->implode('') ?: 'MC';

  $peerUserId = $peer?->user_id ?? $peer?->user?->id;

  $relationship = null;
  if ($active) {
      $relationship = $active->relationship;
      if (!$relationship) {
          $patientId = $isPsychologist ? $peer->id : $authUser->patient?->id;
          $psychologistId = $isPsychologist ? ($authUser->professional?->id ?? $authUser->psychologist?->id) : $peer->id;
          if ($patientId && $psychologistId) {
              $relationship = \App\Models\PatientPsychologistRelationship::query()
                  ->where('patient_id', $patientId)
                  ->where('psychologist_id', $psychologistId)
                  ->orderBy('id', 'desc')
                  ->first();
          }
      }
  }

  $defaultAvatar = asset('commonassets/images/user.png');
  $authAvatarUrl = $authUser->professional?->profile_image_url ?? $defaultAvatar;
  $peerAvatarUrl = (! $isPsychologist && $peer)
      ? ($peer->profile_image_url ?? $defaultAvatar)
      : $defaultAvatar;
  $profileRoute = $isPsychologist
      ? route('frontend.psychologist.profile.show')
      : route('frontend.patient.profile.show');
@endphp

{{--
  Legacy WhatsApp-style chrome (kept for reference):
  <div class="fe-chat">
    <aside class="fe-chat-sidebar">
      <div class="fe-chat-sidebar__head"> tabs + list items with fe-chat-avatar initials </div>
      <div class="fe-chat-sidebar__foot"> current user + theme toggle </div>
    </aside>
    <section class="fe-chat-panel">
      <header class="fe-chat-panel__head"> peer + close buttons </header>
      <div class="fe-chat-feed"> fe-chat-bubble-row / fe-chat-bubble </div>
      <form class="fe-chat-composer"> pill input + round send </form>
    </section>
  </div>
--}}

<div
  class="fe-chat chat-wrapper"
  data-chat-root
  data-chat-theme="normal"
  data-chat-list-url="{{ route($routePrefix.'.conversations.index') }}"
  data-chat-audio-url-pattern="{{ url(($isPsychologist ? 'psychologist' : 'patient').'/messages/__ID__/audio') }}"
  data-chat-user-id="{{ $authUser->id }}"
  data-chat-peer-avatar="{{ $peerAvatarUrl }}"
  data-presence-online="{{ __('frontend.chat.online') }}"
  data-presence-offline="{{ __('frontend.chat.offline') }}"
>
  <aside class="fe-chat-sidebar chat-sidebar card">
    <div class="chat-sidebar-single active top-profile">
      <div
        class="img fe-chat-avatar"
        data-tone="1"
        data-presence-user-id="{{ $authUser->id }}"
      >
        <img src="{{ $authAvatarUrl }}" alt="{{ $authUser->name }}">
      </div>
      <div class="info">
        {{-- Legacy: <strong>{{ $authUser->name }}</strong> --}}
        <h2 class="h6 text-md mb-0">{{ $authUser->name }}</h2>
        <p class="mb-0 fe-chat-presence" data-presence-status data-presence-user-id="{{ $authUser->id }}">
          <i class="bi bi-circle-fill" data-presence-dot hidden></i>
          <span data-presence-label>{{ __('frontend.chat.offline') }}</span>
        </p>
      </div>
      <div class="action">
        {{-- Legacy footer theme toggle moved into this menu:
        <div class="fe-chat-theme-toggle" data-chat-theme-toggle>
          <button type="button" class="is-active" data-chat-theme-option="normal"></button>
          <button type="button" data-chat-theme-option="image"></button>
        </div>
        --}}
        <div class="btn-group" data-chat-theme-toggle>
          <button type="button" class="text-secondary-light text-xl" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
            <iconify-icon icon="bi:three-dots"></iconify-icon>
          </button>
          <ul class="dropdown-menu dropdown-menu-lg-end border">
            {{--<li>
              <a href="{{ $profileRoute }}" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                <iconify-icon icon="fluent:person-32-regular"></iconify-icon>
                {{ __('frontend.dashboard.profile') }}
              </a>
            </li>--}}
            <li>
              <button type="button" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 is-active" data-chat-theme-option="normal">
                <iconify-icon icon="solar:gallery-linear"></iconify-icon>
                {{ __('frontend.chat.theme_normal') }}
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2" data-chat-theme-option="image">
                <iconify-icon icon="solar:moon-stars-outline"></iconify-icon>
                {{ __('frontend.chat.theme_image') }}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="fe-chat-sidebar__head">
      {{--<div class="fe-chat-sidebar__title">
        <h2>{{ __('frontend.chat.title') }}</h2>
        @unless ($isPsychologist)
          <form action="{{ route('frontend.patient.conversations.store') }}" method="post">
            @csrf
            <button type="submit" class="fe-chat-icon-btn" title="{{ __('frontend.dashboard.start_conversation') }}">
              <i class="bi bi-plus-lg"></i>
            </button>
          </form>
        @endunless
      </div>--}}

      {{-- Legacy search pill:
      <label class="fe-chat-search">
        <i class="bi bi-search"></i>
        <input type="search" data-chat-search placeholder="{{ __('frontend.chat.search') }}">
      </label>
      --}}
      <div class="chat-search">
      {{-- <span class="icon">
          <iconify-icon icon="iconoir:search"></iconify-icon>
        </span>
        <input type="search" name="chatSearch" autocomplete="off" data-chat-search placeholder="{{ __('frontend.chat.search') }}">--}}
      </div>

      @php
        $isActiveClosed = $active && $active->status === 'closed';
      @endphp
      <div class="fe-chat-tabs" role="tablist">
        {{-- Legacy:
        <button type="button" class="active" data-chat-filter="active">{{ __('frontend.chat.tab_active') }}</button>
        <button type="button" class="" data-chat-filter="history">{{ __('frontend.chat.tab_history') }}</button>
        --}}
        <button type="button" class="{{ $isActiveClosed ? '' : 'active' }}" data-chat-filter="active">{{ __('frontend.chat.tab_active') }}</button>
        <button type="button" class="{{ $isActiveClosed ? 'active' : '' }}" data-chat-filter="history">{{ __('frontend.chat.tab_history') }}</button>
      </div>
    </div>

    <div class="fe-chat-list chat-all-list" data-chat-list-container>
      {{-- Tab 1: Active Chats --}}
      <div data-chat-active-list-wrapper @if($isActiveClosed) style="display: none;" @endif>
        @forelse ($conversations->where('status', 'active') as $item)
          @php
            if ($isPsychologist) {
                $itemPeer = $item->patient;
                $itemName = $itemPeer?->user?->name
                    ?? trim(($itemPeer?->first_name ?? '').' '.($itemPeer?->last_name ?? ''))
                    ?: __('frontend.chat.unknown');
                $itemAvatarUrl = $defaultAvatar;
            } else {
                $itemPeer = $item->psychologist;
                $itemName = $itemPeer?->user?->name
                    ?? trim(($itemPeer?->first_name ?? '').' '.($itemPeer?->last_name ?? ''))
                    ?: __('frontend.chat.unknown');
                $itemAvatarUrl = $itemPeer?->profile_image_url ?? $defaultAvatar;
            }
            $itemInitials = collect(preg_split('/\s+/', trim($itemName)))
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->implode('') ?: 'MC';

            $itemPeerUserId = $itemPeer?->user_id ?? $itemPeer?->user?->id;
            $latest = $item->messages->first();
            $preview = $latest
                ? ($latest->type === 'audio' ? __('frontend.chat.audio_message') : \Illuminate\Support\Str::limit($latest->body ?? '—', 48))
                : __('frontend.chat.no_messages_yet');
            $unread = (int) ($item->unread_count ?? 0);
          @endphp
          <a
            href="{{ route($routePrefix.'.conversations.show', $item->uuid) }}"
            class="fe-chat-item chat-sidebar-single {{ $active && $active->id === $item->id ? 'is-active' : '' }}"
            data-chat-item
            data-chat-uuid="{{ $item->uuid }}"
            data-chat-status="{{ $item->status }}"
            data-chat-name="{{ strtolower($itemName) }}"
          >
            {{-- Legacy initials avatar:
            <span class="fe-chat-avatar" data-tone="{{ ($item->id % 4) + 1 }}" @if ($itemPeerUserId) data-presence-user-id="{{ $itemPeerUserId }}" @endif>{{ $itemInitials }}</span>
            --}}
            <div
              class="img fe-chat-avatar"
              data-tone="{{ ($item->id % 4) + 1 }}"
              @if ($itemPeerUserId) data-presence-user-id="{{ $itemPeerUserId }}" @endif
            >
              <img src="{{ $itemAvatarUrl }}" alt="{{ $itemName }}" class="rounded-circle">
            </div>
            <div class="info fe-chat-item__body">
              <h2 class="h6 text-sm mb-1" data-chat-item-name>{{ $itemName }}</h2>
              <p class="mb-0 text-xs fe-chat-item__preview" data-chat-item-preview>{{ $preview }}</p>
            </div>
            <div class="action text-end">
              <p class="mb-0 text-neutral-400 text-xs lh-1">
                <time data-chat-item-time>{{ $item->last_message_at?->format('H:i') ?? $item->updated_at?->format('H:i') }}</time>
              </p>
              <span class="w-16-px h-16-px text-xs rounded-circle bg-warning-main text-white d-inline-flex align-items-center justify-content-center fe-chat-badge" data-chat-item-badge @if ($unread < 1) hidden @endif>{{ $unread }}</span>
            </div>
          </a>
        @empty
          <div class="fe-chat-empty-list">
            <p>{{ __('frontend.chat.empty_list') }}</p>
            @unless ($isPsychologist)
              <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-btn fe-btn--primary">
                {{ __('frontend.chat.find_psychologist') }}
              </a>
            @endunless
          </div>
        @endforelse
      </div>

      {{-- Tab 2: Consultation History (Grouped) --}}
      <div data-chat-history-list-wrapper @if(!$isActiveClosed) style="display: none;" @endif>
        @php
          $groupedHistory = $conversations->where('status', 'closed')->groupBy(function ($item) use ($isPsychologist) {
              $peer = $isPsychologist ? $item->patient : $item->psychologist;
              return $peer?->id ?? 0;
          });
        @endphp

        @forelse ($groupedHistory as $peerId => $items)
          @php
            $firstItem = $items->first();
            if ($isPsychologist) {
                $histPeer = $firstItem->patient;
                $histPeerName = $histPeer?->user?->name ?? trim(($histPeer?->first_name ?? '').' '.($histPeer?->last_name ?? '')) ?: __('frontend.chat.unknown');
                $histAvatarUrl = $defaultAvatar;
            } else {
                $histPeer = $firstItem->psychologist;
                $histPeerName = $histPeer?->user?->name ?? trim(($histPeer?->first_name ?? '').' '.($histPeer?->last_name ?? '')) ?: __('frontend.chat.unknown');
                $histAvatarUrl = $histPeer?->profile_image_url ?? $defaultAvatar;
            }
            $colorTone = ($peerId % 4) + 1;
            $bulletColor = ['#ff9800', '#9c27b0', '#4caf50', '#2196f3'][$peerId % 4];
          @endphp
          <div class="fe-history-group">
            <div class="fe-history-group-header">
              <span class="fe-history-group-dot" style="background-color: {{ $bulletColor }};"></span>
              <span>{{ $histPeerName }}</span>
            </div>
            <div class="fe-history-group-items">
              @foreach ($items as $item)
                @php
                  $latest = $item->messages->first();
                  $preview = $item->last_message_at ? ($item->last_message_at->format('M d, Y') . ' • ' . ($latest ? \Illuminate\Support\Str::limit($latest->body ?? '—', 28) : '')) : 'Ended';
                @endphp
                <a
                  href="{{ route($routePrefix.'.conversations.show', $item->uuid) }}"
                  class="fe-chat-item chat-sidebar-single {{ $active && $active->id === $item->id ? 'is-active' : '' }}"
                  data-chat-item
                  data-chat-uuid="{{ $item->uuid }}"
                  data-chat-status="{{ $item->status }}"
                  data-chat-name="{{ strtolower($histPeerName) }}"
                >
                  {{-- Legacy history row:
                  <span style="margin-right: 0.5rem;">⚫</span>
                  <div style="flex: 1;">
                    <div style="font-weight: 500;">{{ __('relationship.consultation') }} #{{ $item->id }}</div>
                    <div style="font-size: 0.75rem; color: var(--fe-muted);">{{ __('relationship.status_closed') }}</div>
                    <div style="font-size: 0.75rem; color: var(--fe-muted);">{{ $preview }}</div>
                  </div>
                  --}}
                  <div class="img">
                    <img src="{{ $histAvatarUrl }}" alt="{{ $histPeerName }}" class="rounded-circle">
                  </div>
                  <div class="info">
                    <h2 class="h6 text-sm mb-1">{{ __('relationship.consultation') }} #{{ $item->id }}</h2>
                    <p class="mb-0 text-xs">{{ __('relationship.status_closed') }}</p>
                    <p class="mb-0 text-xs text-neutral-400" data-chat-item-preview>{{ $preview }}</p>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @empty
          <div class="fe-chat-empty-list">
            <p>{{ __('relationship.no_consultation_history') }}</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Legacy sidebar footer (user + theme) is now the top-profile block above.
    <div class="fe-chat-sidebar__foot">
      <div class="fe-chat-user">...</div>
      <div class="fe-chat-theme-toggle" data-chat-theme-toggle>...</div>
    </div>
    --}}
  </aside>

  <section class="fe-chat-panel chat-main card">
    @if ($active)
      <header class="fe-chat-panel__head chat-sidebar-single active">
        <div class="fe-chat-panel__peer">
          <a href="{{ route($routePrefix.'.conversations.index') }}" class="fe-chat-icon-btn fe-chat-back">
            <i class="bi bi-arrow-left"></i>
          </a>
          {{-- Legacy always-online initials:
          <span class="fe-chat-avatar is-online" data-tone="2">{{ $peerInitials }}</span>
          --}}
          <div
            class="img fe-chat-avatar"
            data-tone="2"
            @if ($peerUserId) data-presence-user-id="{{ $peerUserId }}" @endif
          >
            <img src="{{ $peerAvatarUrl }}" alt="{{ $peerName }}">
          </div>
          <div class="info">
            {{-- Legacy: <strong>{{ $peerName }}</strong> --}}
            <h2 class="h6 text-md mb-0">{{ $peerName }}</h2>
            {{-- Legacy hardcoded online:
            <small>
              <i class="bi bi-circle-fill"></i> {{ __('frontend.chat.online') }}
              @if ($peerSpecialization)
                · {{ $peerSpecialization }}
              @endif
            </small>
            --}}
            <p
              class="mb-0 fe-chat-presence"
              data-presence-status
              @if ($peerUserId) data-presence-user-id="{{ $peerUserId }}" @endif
            >
              <i class="bi bi-circle-fill" data-presence-dot hidden></i>
              <span data-presence-label>{{ __('frontend.chat.offline') }}</span>
              @if ($peerSpecialization)
                <span> · {{ $peerSpecialization }}</span>
              @endif
            </p>
          </div>
        </div>
        <div class="action d-inline-flex align-items-center gap-3 fe-chat-panel__actions">
          {{-- Legacy visible close buttons:
          <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="close-chat-modal">...</button>
          <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="close-relationship-modal">...</button>
          --}}
          @if ($active->status !== 'closed' && (!$relationship || $relationship->status !== 'closed'))
            <button type="button" class="text-xl text-primary-light" title="{{ __('relationship.close') }}" data-fe-modal-open="close-chat-modal">
              <i class="bi bi-x-circle"></i>
            </button>
          @endif
          @if ($relationship && $relationship->status !== 'closed')
            <button type="button" class="text-xl text-primary-light" title="Close Relationship" data-fe-modal-open="close-relationship-modal">
              <i class="bi bi-person-x"></i>
            </button>
          @endif
          @if (($active->status === 'closed' || ($relationship && $relationship->status === 'closed')) && !($relationship && $relationship->status !== 'closed'))
            <span class="fe-badge fe-badge--secondary">{{ __('relationship.status_closed') }}</span>
          @endif
          <div class="btn-group">
            <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
              <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
            </button>
            <ul class="dropdown-menu dropdown-menu-lg-end border">
              @if ($active->status !== 'closed' && (!$relationship || $relationship->status !== 'closed'))
                <li>
                  <button class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2" type="button" data-fe-modal-open="close-chat-modal">
                    <iconify-icon icon="mdi:clear-circle-outline"></iconify-icon>
                    {{ __('relationship.close') }}
                  </button>
                </li>
              @endif
              @if ($relationship && $relationship->status !== 'closed')
                <li>
                  <button class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2" type="button" data-fe-modal-open="close-relationship-modal">
                    <iconify-icon icon="ic:baseline-block"></iconify-icon>
                    Close Relationship
                  </button>
                </li>
              @endif
              {{--<li>
                <a href="{{ route($routePrefix.'.reports.create', ['type' => 'conversation', 'conversation' => $active->uuid]) }}" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                  <iconify-icon icon="solar:flag-outline"></iconify-icon>
                  {{ __('frontend.reports.report_conversation') }}
                </a>
              </li>
              @if ($peerUserId)
                <li>
                  <a href="{{ route($routePrefix.'.reports.create', ['type' => 'user', 'conversation' => $active->uuid, 'user' => $peerUserId]) }}" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:user-block-outline"></iconify-icon>
                    {{ __('frontend.reports.report_person') }}
                  </a>
                </li>
              @endif
              <li>
                <a href="{{ route($routePrefix.'.conversations.index') }}" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                  <iconify-icon icon="solar:chat-round-line-outline"></iconify-icon>
                  {{ __('frontend.chat.title') }}
                </a>
              </li>--}}
            </ul>
          </div>
        </div>
      </header>

      <div
        class="fe-chat-feed chat-message-list"
        data-chat-feed
        data-chat-older-url="{{ route($routePrefix.'.messages.index', $active->uuid) }}"
        data-chat-conversation-uuid="{{ $active->uuid }}"
        data-chat-next-page="{{ $nextMessagesPage ?? '' }}"
        data-chat-has-more="{{ ($hasOlderMessages ?? false) ? '1' : '0' }}"
        data-chat-user-id="{{ $authUser->id }}"
        data-chat-last-id="{{ $messages->last()?->id ?? 0 }}"
        data-chat-audio-label="{{ __('frontend.chat.audio_message') }}"
        data-chat-today-label="{{ __('frontend.chat.today') }}"
        data-chat-yesterday-label="{{ __('frontend.chat.yesterday') }}"
        data-chat-sent-label="{{ __('frontend.chat.status_sent') }}"
        data-chat-read-label="{{ __('frontend.chat.status_read') }}"
        data-chat-mark-read-url="{{ route($routePrefix.'.messages.mark-conversation-read', $active->uuid) }}"
      >
        <div class="fe-chat-loader" data-chat-loader @unless($hasOlderMessages ?? false) hidden @endunless>
          <span></span>
          <small>{{ __('frontend.chat.loading_older') }}</small>
        </div>

        @php
          $grouped = $messages->groupBy(fn ($message) => optional($message->created_at)->toDateString());
        @endphp

        @forelse ($grouped as $date => $dayMessages)
          <div class="fe-chat-day" data-chat-day-date="{{ $date }}">
            <span>
              @if ($date === now()->toDateString())
                {{ __('frontend.chat.today') }}
              @elseif ($date === now()->subDay()->toDateString())
                {{ __('frontend.chat.yesterday') }}
              @else
                {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}
              @endif
            </span>
          </div>

          @foreach ($dayMessages as $message)
            @php
              $isMine = (int) $message->sender_id === (int) $authUser->id;
              $senderName = $message->sender?->name ?? __('frontend.chat.unknown');
              $senderInitials = collect(preg_split('/\s+/', trim($senderName)))
                  ->filter()
                  ->take(2)
                  ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                  ->implode('') ?: 'MC';
              $senderAvatarUrl = $isMine ? $authAvatarUrl : $peerAvatarUrl;
            @endphp
            <div
              class="fe-chat-bubble-row chat-single-message {{ $isMine ? 'is-out right' : 'is-in left' }}"
              data-message-row
              data-message-id="{{ $message->id }}"
              data-message-date="{{ $message->created_at?->toDateString() }}"
            >
              @unless ($isMine)
                {{-- Legacy: <span class="fe-chat-avatar fe-chat-avatar--sm" data-tone="2">{{ $senderInitials }}</span> --}}
                <img src="{{ $senderAvatarUrl }}" alt="{{ $senderName }}" class="avatar-lg object-fit-cover rounded-circle">
              @endunless
              <div class="fe-chat-bubble chat-message-content">
                @if ($message->type === 'audio')
                  <div class="fe-chat-audio">
                    <p class="fe-chat-audio__label mb-3"><i class="bi bi-mic-fill"></i> {{ __('frontend.chat.audio_message') }}</p>
                    @if ($message->audio)
                      <audio
                        class="fe-chat-audio__player"
                        controls
                        preload="metadata"
                        src="{{ route($routePrefix.'.messages.audio', $message) }}"
                      ></audio>
                      @if ($message->audio->duration_seconds)
                        <span class="fe-chat-audio__duration">{{ gmdate($message->audio->duration_seconds >= 3600 ? 'H:i:s' : 'i:s', $message->audio->duration_seconds) }}</span>
                      @endif
                    @endif
                  </div>
                @else
                  <p class="mb-3">{{ $message->body }}</p>
                @endif
                {{-- Legacy meta:
                <div class="fe-chat-bubble__meta">
                  <time>{{ $message->created_at?->format('h:i A') }}</time>
                </div>
                --}}
                <p class="chat-time mb-0 fe-chat-bubble__meta">
                  <span>
                    <time datetime="{{ $message->created_at?->toIso8601String() }}" title="{{ $message->created_at?->format('d M Y H:i') }}">
                      {{ $message->created_at?->format('d M Y · H:i') }}
                    </time>
                  </span>
                  @if ($isMine)
                    <i
                      class="bi {{ $message->is_read ? 'bi-check2-all' : 'bi-check2' }} fe-chat-tick {{ $message->is_read ? 'is-read' : 'is-sent' }}"
                      title="{{ $message->is_read ? __('frontend.chat.status_read') : __('frontend.chat.status_sent') }}"
                      data-chat-tick
                    ></i>
                  @endif
                </p>
              </div>
            </div>
          @endforeach
        @empty
          <div class="fe-chat-empty-feed">
            <i class="bi bi-chat-heart"></i>
            <p>{{ __('frontend.chat.start_messaging') }}</p>
          </div>
        @endforelse
      </div>

      @if ($isPsychologist && ($responseTemplates ?? collect())->isNotEmpty())
        <div class="fe-chat-templates" data-chat-templates>
          <label class="visually-hidden" for="chat-template-select">{{ __('frontend.templates.title') }}</label>
          <select id="chat-template-select" class="fe-input fe-chat-templates__select" data-chat-template-select>
            <option value="">{{ __('frontend.templates.insert') }}</option>
            @foreach ($responseTemplates as $template)
              <option value="{{ $template->id }}" data-body="{{ rawurlencode($template->body) }}">{{ $template->title }}</option>
            @endforeach
          </select>
        </div>
      @endif

      @if ($active->status === 'closed' || ($relationship && $relationship->status === 'closed'))
        <div class="fe-chat-empty-panel chat-message-box">
          <p class="fe-muted mb-0"><i class="bi bi-info-circle"></i> {{ __('relationship.chat_is_closed') }}</p>
        </div>
      @elseif ($pauseMessaging)
        <div class="fe-quota-block chat-message-box">
          <p class="mb-1"><strong>{{ __('frontend.chat.conversation_paused') }}</strong></p>
          <p class="mb-2">{{ __('frontend.chat.conversation_paused_body') }}</p>
          @unless ($isPsychologist)
            <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-btn fe-btn--primary fe-btn--sm">
              {{ __('frontend.chat.find_another_psychologist') }}
            </a>
          @endunless
        </div>
      @elseif ($blockNewExchange)
        <div class="fe-quota-block chat-message-box">
          <p>{{ __('ui.quota_exhausted') }}</p>
          <a href="{{ route('frontend.patient.subscription.index') }}" class="fe-btn fe-btn--primary fe-btn--sm">
            {{ __('ui.buy_additional') }}
          </a>
        </div>
      @elseif ($blockExchangeMessages)
        <div class="fe-quota-block chat-message-box">
          @if ($exchangeMessageUsage)
            <p class="mb-1" data-exchange-message-usage-text>
              {{ __('frontend.chat.your_messages', ['used' => $exchangeMessageUsage['used'], 'limit' => $exchangeMessageUsage['limit']]) }}
            </p>
            <p class="mb-1" data-exchange-message-remaining-text>
              {{ __('frontend.chat.messages_remaining', ['count' => $exchangeMessageUsage['remaining']]) }}
            </p>
          @endif
          <p class="mb-0">{{ __('frontend.chat.composer_disabled_exchange_limit') }}</p>
        </div>
      @else
        <form
          class="fe-chat-composer chat-message-box"
          action="{{ route($routePrefix.'.messages.store', $active->uuid) }}"
          method="post"
          data-chat-composer
          data-chat-store-audio-url="{{ route($routePrefix.'.messages.store-audio', $active->uuid) }}"
          data-chat-transcribe-url="{{ route($routePrefix.'.speech.transcribe') }}"
          data-chat-audio-allowed="{{ $allowsAudioChannel ? '1' : '0' }}"
          data-chat-dictation-allowed="{{ $allowsDictationChannel ? '1' : '0' }}"
          data-chat-audio-not-allowed="{{ __('message_api.channel_not_allowed_audio') }}"
          data-chat-mic-unavailable="{{ __('frontend.chat.mic_unavailable') }}"
          data-chat-mic-denied="{{ __('frontend.chat.mic_denied') }}"
          data-chat-record-start="{{ __('frontend.chat.record_start') }}"
          data-chat-record-stop="{{ __('frontend.chat.record_stop') }}"
          data-chat-transcribing="{{ __('frontend.chat.transcribing') }}"
          data-chat-transcript-failed="{{ __('frontend.chat.transcript_failed') }}"
          data-chat-sending-audio="{{ __('frontend.chat.sending_audio') }}"
          data-chat-send-audio-failed="{{ __('frontend.chat.send_audio_failed') }}"
          data-chat-recording="{{ __('frontend.chat.recording') }}"
          data-chat-recording-ready="{{ __('frontend.chat.recording_ready') }}"
          data-chat-no-sound="{{ __('frontend.chat.no_sound_detected') }}"
          data-chat-limit-reached="{{ __('frontend.chat.recording_limit_reached') }}"
          data-chat-send-failed="{{ __('frontend.chat.send_failed') }}"
          data-chat-sending="{{ __('ui.sending') }}"
          data-chat-enforce-text-limit="{{ $enforceTextLimit ? '1' : '0' }}"
          @if ($enforceTextLimit)
            data-chat-max-text-chars="{{ $maxTextChars }}"
            data-chat-text-too-long="{{ __('frontend.chat.text_too_long', ['max' => $maxTextChars]) }}"
          @endif
          @if ($enforceAudioLimit)
            data-chat-max-audio-seconds="{{ $maxAudioSeconds }}"
          @endif
          data-chat-max-audio-bytes="{{ \App\Support\MessagingLimits::maxAudioUploadKb() * 1024 }}"
          data-chat-audio-extensions="{{ implode(',', \App\Support\MessagingLimits::allowedAudioExtensions()) }}"
          data-chat-audio-too-large="{{ __('frontend.chat.audio_too_large', ['size' => \App\Support\MessagingLimits::maxAudioUploadMb()]) }}"
          data-chat-audio-format-denied="{{ __('frontend.chat.audio_format_denied', ['formats' => implode(', ', \App\Support\MessagingLimits::configuredAudioFormatKeys())]) }}"
          data-chat-exchange-label-used="{{ __('frontend.chat.your_messages') }}"
          data-chat-exchange-label-remaining="{{ __('frontend.chat.messages_remaining') }}"
          data-chat-exchange-label-disabled="{{ __('frontend.chat.composer_disabled_exchange_limit') }}"
        >
          @csrf
          @if ($exchangeMessageUsage)
            <div
              class="fe-muted small mb-2"
              data-exchange-message-usage
              data-used="{{ $exchangeMessageUsage['used'] }}"
              data-limit="{{ $exchangeMessageUsage['limit'] }}"
              data-remaining="{{ $exchangeMessageUsage['remaining'] }}"
            >
              <span data-exchange-message-usage-text>{{ __('frontend.chat.your_messages', ['used' => $exchangeMessageUsage['used'], 'limit' => $exchangeMessageUsage['limit']]) }}</span>
              · <span data-exchange-message-remaining-text>{{ __('frontend.chat.messages_remaining', ['count' => $exchangeMessageUsage['remaining']]) }}</span>
            </div>
          @endif
          <div class="fe-chat-send-error" data-chat-send-error hidden role="alert"></div>
          @php
            // Legacy hardcoded:
            // $audioMaxMb = max(1, (int) ceil(((int) config('speech.max_upload_size_kb', 25600)) / 1024));
            // $audioFormats = implode(', ', ['webm', 'mp3', 'wav', 'ogg', 'm4a']);
            $audioMaxMb = \App\Support\MessagingLimits::maxAudioUploadMb();
            $audioFormats = implode(', ', \App\Support\MessagingLimits::configuredAudioFormatKeys());
          @endphp
          @if ($allowsMic)
            {{--<p class="fe-chat-audio-hint fe-muted mb-1">
              @if ($enforceAudioLimit)
                {{ __('frontend.chat.audio_constraints', [
                  'seconds' => $maxAudioSeconds,
                  'size' => $audioMaxMb,
                  'formats' => $audioFormats,
                ]) }}
              @else
                {{ __('frontend.chat.audio_constraints_unlimited', [
                  'size' => $audioMaxMb,
                  'formats' => $audioFormats,
                ]) }}
              @endif
            </p>--}}
          @endif
          {{--<button type="button" class="fe-chat-icon-btn" title="{{ __('frontend.chat.attach') }}"><i class="bi bi-paperclip"></i></button>
          <button type="button" class="fe-chat-icon-btn" title="{{ __('frontend.chat.emoji') }}"><i class="bi bi-emoji-smile"></i></button>--}}
          {{-- Legacy unbounded input for all roles:
          <input type="text" name="body" data-chat-body>
          --}}
          <div class="fe-chat-composer__field">
            <input
              type="text"
              name="body"
              value="{{ old('body') }}"
              placeholder="{{ __('frontend.chat.placeholder') }}"
              autocomplete="off"
              required
              @if ($enforceTextLimit) maxlength="{{ $maxTextChars }}" @endif
              data-chat-body
            >
            @if ($enforceTextLimit)
              <div
                class="fe-chat-char-count"
                data-chat-char-count
                aria-live="polite"
                title="{{ __('frontend.chat.char_count_hint', ['max' => $maxTextChars]) }}"
              >
                <span data-chat-char-used>0</span>/{{ $maxTextChars }}
              </div>
            @endif
          </div>
          <div class="chat-message-box-action">
            {{-- Mic: shown when audio messages OR dictation is allowed.
                 “Send voice” is controlled separately (audio channel only). --}}
            @if ($allowsMic)
              <button
                type="button"
                class="fe-chat-icon-btn text-xl"
                data-chat-mic
                hidden
                title="{{ __('frontend.chat.record_start') }}"
                aria-label="{{ __('frontend.chat.record_start') }}"
              >
                <i class="bi bi-mic-fill" data-chat-mic-icon></i>
              </button>
            @endif
            {{-- Legacy round send:
            <button type="submit" class="fe-chat-send" title="{{ __('frontend.chat.send') }}">
              <i class="bi bi-send-fill"></i>
            </button>
            --}}
            <button type="submit" class="btn btn-sm btn-primary-600 radius-8 d-inline-flex align-items-center gap-1 fe-chat-send" title="{{ __('frontend.chat.send') }}">
              {{ __('frontend.chat.send') }}
              <iconify-icon icon="f7:paperplane"></iconify-icon>
            </button>
          </div>
        </form>
      @endif
      @unless ($blockNewExchange || $blockExchangeMessages || $pauseMessaging)
      <div class="fe-chat-voice-bar" data-chat-voice-bar hidden>
        <span class="fe-chat-voice-bar__status" data-chat-voice-status></span>
        <audio class="fe-chat-voice-bar__player" data-chat-voice-preview controls hidden></audio>
        {{-- Send as voice file: only when plan includes audio channel --}}
        @if ($allowsAudioChannel)
          <button type="button" class="fe-btn fe-btn--primary fe-chat-voice-bar__btn" data-chat-send-audio>
            {{ __('frontend.chat.send_audio') }}
          </button>
        @endif
        {{-- Dictation: record → transcript into the text box (On-demand / Essential OK) --}}
        @if ($allowsDictationChannel || $allowsAudioChannel)
          {{--<button type="button" class="fe-btn fe-btn--primary fe-chat-voice-bar__btn" data-chat-transcribe>
            {{ __('frontend.chat.transcribe') }}
          </button>--}}
        @endif
        <button type="button" class="fe-btn fe-btn--primary fe-chat-voice-bar__btn" data-chat-voice-discard>
          {{ __('frontend.chat.discard_recording') }}
        </button>
      </div>
      @endunless
    @else
      <div class="fe-chat-empty-panel chat-empty">
        <div>
          <i class="bi bi-chat-dots"></i>
          <h3>{{ __('frontend.chat.select_title') }}</h3>
          <p>{{ __('frontend.chat.select_text') }}</p>
          {{--@unless ($isPsychologist)
            <form action="{{ route('frontend.patient.conversations.store') }}" method="post">
              @csrf
              <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.dashboard.start_conversation') }}</button>
            </form>
          @endunless--}}
        </div>
      </div>
    @endif
  </section>
</div>

@if ($active && $active->status !== 'closed' && (!$relationship || $relationship->status !== 'closed'))
  <dialog id="close-chat-modal" class="fe-modal">
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h2>{{ __('relationship.close') }}</h2>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.cancel') }}">&times;</button>
      </div>
      <form action="{{ route($routePrefix.'.conversations.close', $active->uuid) }}" method="POST" class="fe-modal__body">
        @csrf
        <p>{{ __('relationship.confirm_close') }}</p>
        <div class="fe-modal__actions" style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
          <button type="submit" class="fe-btn fe-btn--primary">{{ __('relationship.close') }}</button>
        </div>
      </form>
    </div>
  </dialog>
@endif

@if ($relationship && $relationship->status !== 'closed')
  <dialog id="close-relationship-modal" class="fe-modal">
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h2>Close Relationship</h2>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.cancel') }}">&times;</button>
      </div>
      <form action="{{ route($routePrefix.'.relationships.close', $relationship->id) }}" method="POST" class="fe-modal__body">
        @csrf
        <p>Are you sure you want to end the care relationship? This will also close all active chats with this psychologist.</p>
        <div class="fe-modal__actions" style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
          <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
          <button type="submit" class="fe-btn fe-btn--primary">Close Relationship</button>
        </div>
      </form>
    </div>
  </dialog>
@endif
