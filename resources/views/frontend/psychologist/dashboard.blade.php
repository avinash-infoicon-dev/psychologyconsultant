@extends('frontend.layouts.dashboard')

@section('title', __('frontend.nav.dashboard'))
@section('page_title', __('frontend.nav.dashboard'))

@section('content')
  {{--
  <div class="fe-welcome-card">
    <h1 style="margin:0 0 .5rem;font-size:1.75rem;">{{ __('frontend.dashboard.welcome', ['name' => $user->name]) }}</h1>
    <p style="margin:0;opacity:.92;">{{ __('frontend.dashboard.psychologist_subtitle') }}</p>
  </div>

  <div class="fe-dash-stats">
    <div class="fe-dash-stat">
      <strong>{{ $pendingCount }}</strong>
      <span>{{ __('frontend.dashboard.stat_pending_exchanges') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</small>
    </div>
    <div class="fe-dash-stat">
      <strong>{{ $overdueCount }}</strong>
      <span>{{ __('frontend.dashboard.stat_overdue') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_overdue_hint') }}</small>
    </div>
    <div class="fe-dash-stat">
      <strong>{{ $conversationCount }}</strong>
      <span>{{ __('frontend.dashboard.stat_conversations') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_conversations_hint') }}</small>
    </div>
    <div class="fe-dash-stat">
      <strong>
        @if ($psychologist?->is_verified)
          {{ __('frontend.dashboard.verified') }}
        @else
          {{ __('frontend.dashboard.pending') }}
        @endif
      </strong>
      <span>{{ __('frontend.dashboard.stat_verified') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_verified_hint') }}</small>
    </div>
  </div>
  --}}

  @php
    $avatarUrl = $user->professional?->profile_image_url
      ?? asset('commonassets/images/user.png');
    $displayName = trim(($psychologist?->title ?? '') . ' ' . $user->name);
    $psychKpiUnread = (int) ($unreadConversationCount ?? 0);
    $psychKpiUnreadMessages = (int) ($unreadMessageCount ?? 0);
    $psychKpiRequests = (int) ($pendingRequestCount ?? $pendingRequests->count());
    $psychKpiRadial = $psychKpiUnreadMessages > 0
      ? min(100, (int) round(($psychKpiUnread / max(1, $psychKpiUnreadMessages)) * 100))
      : ($psychKpiUnread > 0 ? 100 : 0);
  @endphp

  {{-- Dummy template KPI cards (Total Users / Psychologists / Patients):
  <div class="row gy-20-px mb-20">
    ... Total Users / Total Psychologists / Total Patients ...
  </div>
  --}}
  @if (false)
  <div class="row gy-20-px mb-20">
                <div class="col-sm-4">
                    <div class="bg-base p-24 radius-20 h-100">
                        <div
                            class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
                            <span class="text-neutral-700 fw-medium">Total Users</span>
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light">
                                    </iconify-icon>
                                </button>
                                <ul class="dropdown-menu p-12 border bg-base shadow">
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalView">
                                            <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            View
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                                            <iconify-icon icon="lucide:edit" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="delete-item dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-danger-100 text-hover-danger-600 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalDelete">
                                            <iconify-icon icon="fluent:delete-24-regular"
                                                class="icon text-lg line-height-1"></iconify-icon>
                                            Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-8">
                            <div>
                                <h2 class="text-2xl mb-0">100</h2>
                                <div class="mt-16">
                                    <span class="text-primary-600 text-lg line-height-1">
                                        <i class="ph ph-arrow-up"></i>
                                    </span>
                                    <span class="text-primary-600 fw-medium">100% Avg</span>
                                </div>
                            </div>
                            <div>
                                <div id="barChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="bg-base p-24 radius-20 h-100">
                        <div
                            class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
                            <span class="text-neutral-700 fw-medium">Total Psychologists</span>
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light">
                                    </iconify-icon>
                                </button>
                                <ul class="dropdown-menu p-12 border bg-base shadow">
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalViewOrders">
                                            <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            View
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalEditOrders">
                                            <iconify-icon icon="lucide:edit" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="delete-item dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-danger-100 text-hover-danger-600 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalDeleteOrders">
                                            <iconify-icon icon="fluent:delete-24-regular"
                                                class="icon text-lg line-height-1"></iconify-icon>
                                            Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-8">
                            <div>
                                <h2 class="text-2xl mb-0">100</h2>
                                <div class="mt-16">
                                    <span class="text-warning-600 text-lg line-height-1">
                                        <i class="ph ph-arrows-down-up"></i>
                                    </span>
                                    <span class="text-warning-600 fw-medium">100% Avg</span>
                                </div>
                            </div>
                            <div style="width:140px">
                                <div class="remove-tooltip-title rounded-tooltip-value">
                                    <div id="lineChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="bg-base p-24 radius-20 h-100">
                        <div
                            class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
                            <span class="text-neutral-700 fw-medium">Total Patients</span>
                            <div class="dropdown">
                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light">
                                    </iconify-icon>
                                </button>
                                <ul class="dropdown-menu p-12 border bg-base shadow">
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalViewConversion">
                                            <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            View
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalEditConversion">
                                            <iconify-icon icon="lucide:edit" class="icon text-lg line-height-1">
                                            </iconify-icon>
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                            class="delete-item dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-danger-100 text-hover-danger-600 d-flex align-items-center gap-10"
                                            data-bs-toggle="modal" data-bs-target="#exampleModalDeleteConversion">
                                            <iconify-icon icon="fluent:delete-24-regular"
                                                class="icon text-lg line-height-1"></iconify-icon>
                                            Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-8">
                            <div>
                                <h2 class="text-2xl mb-0">100</h2>
                                <div class="mt-16">
                                    <span class="text-danger-600 text-lg line-height-1">
                                        <i class="ph ph-arrow-down"></i>
                                    </span>
                                    <span class="text-danger-600 fw-medium">100% Avg</span>
                                </div>
                            </div>
                            <div>
                                <div id="radialChart" class="m--8-px" style="min-width:90px; min-height:90px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
  @endif

  <div class="row gy-20-px mb-20">
    <div class="col-sm-3">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.stat_active_patients') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.patients.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.patients.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ $patientCount }}</h2>
            <div class="mt-16">
              <span class="text-primary-600 text-lg line-height-1"><i class="ph ph-arrow-up"></i></span>
              <span class="text-primary-600 fw-medium">{{ __('frontend.dashboard.stat_active_patients') }}</span>
            </div>
          </div>
          <div>
            {{--<div id="psychBarChart"></div>--}}
          </div>
        </a>
      </div>
    </div>

    <div class="col-sm-3">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.stat_new_requests') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.relationships.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.relationships.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ $psychKpiRequests }}</h2>
            <div class="mt-16">
              <span class="text-warning-600 text-lg line-height-1"><i class="ph ph-arrows-down-up"></i></span>
              <span class="text-warning-600 fw-medium">{{ __('frontend.dashboard.stat_new_requests') }}</span>
            </div>
          </div>
          <div style="width:140px">
            <div class="remove-tooltip-title rounded-tooltip-value">
              {{--<div id="psychLineChart"></div>--}}
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="col-sm-3">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.stat_unread_conversations') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.conversations.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.conversations.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ $psychKpiUnread }}</h2>
            <div class="mt-16">
              <span class="text-danger-600 text-lg line-height-1"><i class="ph ph-arrow-down"></i></span>
              <span class="text-danger-600 fw-medium">{{ $psychKpiUnreadMessages }} {{ __('frontend.dashboard.unread_messages') }}</span>
            </div>
          </div>
          <div>
            {{--<div id="psychRadialChart" class="m--8-px" style="min-width:90px; min-height:90px;"></div>--}}
          </div>
        </a>
      </div>
    </div>

    <div class="col-sm-3">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.stat_awaiting_reply') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.conversations.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.conversations.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ $pendingCount }}</h2>
            <div class="mt-16">
              <span class="text-warning-600 text-lg line-height-1"><i class="ph ph-hourglass"></i></span>
              <span class="text-warning-600 fw-medium">{{ $overdueCount }} {{ __('frontend.dashboard.stat_overdue') }}</span>
            </div>
          </div>
          <div>
            {{--<div id="psychAwaitChart"></div>--}}
          </div>
        </a>
      </div>
    </div>
  </div>
 
  @if (false)
  <div class="fe-dash-home">
    <article class="fe-dash-profile-card">
      <div
        class="fe-dash-profile-card__banner"
        style="background-image: url('{{ asset('commonassets/images/user-grid/user-grid-bg1.png') }}');"
      >
        <a
          href="{{ route('frontend.psychologist.profile.show') }}"
          class="fe-dash-profile-card__menu"
          aria-label="{{ __('frontend.dashboard.quick_profile') }}"
        >
          <i class="bi bi-three-dots-vertical"></i>
        </a>
      </div>

      <div class="fe-dash-profile-card__body">
        <img
          class="fe-dash-profile-card__avatar"
          src="{{ $avatarUrl }}"
          alt="{{ $user->name }}"
        >
        <h2 class="fe-dash-profile-card__name">{{ $displayName }}</h2>
        {{-- <p class="fe-dash-profile-card__email">{{ $user->email }}</p> --}}
        <p class="fe-dash-profile-card__lead">{{ __('frontend.dashboard.psychologist_subtitle') }}</p>

        <div class="fe-dash-profile-card__split">
          <div class="fe-dash-profile-card__split-item is-lilac">
            <strong>{{ $psychologist?->license_number ?: '—' }}</strong>
            <span>{{ __('backend.psychologists.license') }}</span>
          </div>
          <div class="fe-dash-profile-card__split-item is-rose">
            <strong>{{ $psychologist?->specialization ?: '—' }}</strong>
            <span>{{ __('backend.psychologists.specialization') }}</span>
          </div>
        </div>

        <div class="fe-dash-profile-card__badges">
          {{--
          @if ($user->hasVerifiedEmail())
            <span class="fe-dash-pill">
              <i class="bi bi-check-circle-fill"></i>
              {{ __('backend.psychologists.email_verified') }}
            </span>
          @endif
          --}}
          <span class="fe-dash-pill {{ in_array($psychologist?->verification_status, ['verified'], true) ? '' : 'is-pending' }}">
            <i class="bi {{ $psychologist?->verification_status === 'verified' ? 'bi-shield-fill-check' : 'bi-hourglass-split' }}"></i>
            {{ __('frontend.psych_profile.verification_status_'.($psychologist?->verification_status ?: 'draft')) }}
          </span>
          {{-- Old pending/verified only
          <span class="fe-dash-pill {{ $psychologist?->is_verified ? '' : 'is-pending' }}">
            ...
          </span>
          @unless ($psychologist?->is_verified)
            <span class="fe-pending-hint">{{ __('ui.pending_approval_hint') }}</span>
          @endunless
          --}}
        </div>

        @if ($psychologist)
          @include('frontend.psychologist.partials.verification-banner', ['professional' => $psychologist])
        @endif

        <div class="fe-dash-profile-card__actions">
         <a href="{{ route('frontend.psychologist.profile.show') }}" class="fe-dash-btn-view">
            {{ __('frontend.dashboard.quick_profile') }}
          </a>
          <a
            href="{{ route('frontend.psychologist.conversations.index') }}"
            class="fe-dash-btn-icon is-mint"
            aria-label="{{ __('frontend.dashboard.quick_messages') }}"
            title="{{ __('frontend.dashboard.quick_messages') }}"
          >
            <i class="bi bi-pencil-square"></i>
          </a>
          <a
            href="{{ route('frontend.psychologist.availability.index') }}"
            class="fe-dash-btn-icon is-rose"
            aria-label="{{ __('frontend.dashboard.quick_availability') }}"
            title="{{ __('frontend.dashboard.quick_availability') }}"
          >
            <i class="bi bi-calendar-week"></i>
          </a>
          {{-- Legacy absences shortcut:
          <a
            href="{{ route('frontend.psychologist.absences.index') }}"
            class="fe-dash-btn-icon is-rose"
            aria-label="{{ __('frontend.absences.title') }}"
            title="{{ __('frontend.absences.title') }}"
          >
            <i class="bi bi-calendar-x"></i>
          </a>
          --}}
        </div>
      </div>
    </article>

    @if (false)
    <div class="fe-dash-home__side">
      {{-- Legacy 4-tile workload:
      <div class="fe-dash-stat-grid">
        <div class="fe-dash-stat-card is-blue">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-inbox"></i></span>
          <div>
            <strong>{{ $pendingCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_pending_exchanges') }}</span>
          </div>
        </div>
        <div class="fe-dash-stat-card is-peach">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-exclamation-circle"></i></span>
          <div>
            <strong>{{ $overdueCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_overdue') }}</span>
          </div>
        </div>
        <div class="fe-dash-stat-card is-lilac">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-chat-dots"></i></span>
          <div>
            <strong>{{ $conversationCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_conversations') }}</span>
          </div>
        </div>
        <div class="fe-dash-stat-card is-mint">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-shield-check"></i></span>
          <div>
            <strong>
              @if ($psychologist?->is_verified)
                {{ __('frontend.dashboard.verified') }}
              @else
                {{ __('frontend.dashboard.pending') }}
              @endif
            </strong>
            <span>{{ __('frontend.dashboard.stat_verified') }}</span>
          </div>
        </div>
      </div>
      --}}

      <div class="fe-dash-stat-grid is-workload">
        <div class="fe-dash-stat-card is-mint">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-shield-check"></i></span>
          <div>
            <strong>
              @if ($psychologist?->is_verified)
                {{ __('frontend.dashboard.verified') }}
              @else
                {{ __('frontend.dashboard.pending') }}
              @endif
            </strong>
            <span>{{ __('frontend.dashboard.stat_verified') }}</span>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.patients.index') }}" class="fe-dash-stat-card is-blue">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-people"></i></span>
          <div>
            <strong>{{ $patientCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_active_patients') }}</span>
          </div>
        </a>
        <a href="{{ route('frontend.psychologist.relationships.index') }}" class="fe-dash-stat-card is-peach">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-person-plus"></i></span>
          <div>
            <strong>{{ $pendingRequestCount ?? $pendingRequests->count() }}</strong>
            <span>{{ __('frontend.dashboard.stat_new_requests') }}</span>
          </div>
        </a>
        <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-dash-stat-card is-lilac">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-envelope-exclamation"></i></span>
          <div>
            <strong>{{ $unreadConversationCount ?? 0 }}</strong>
            <span>{{ __('frontend.dashboard.stat_unread_conversations') }}</span>
            <span>{{ $unreadMessageCount ?? 0 }} {{ __('frontend.dashboard.unread_messages') }}</span>
          </div>
        </a>
        <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-dash-stat-card is-blue">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-inbox"></i></span>
          <div>
            <strong>{{ $pendingCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_awaiting_reply') }}</span>
          </div>
        </a>
        <div class="fe-dash-stat-card is-peach">
          <span class="fe-dash-stat-card__icon"><i class="bi bi-exclamation-circle"></i></span>
          <div>
            <strong>{{ $overdueCount }}</strong>
            <span>{{ __('frontend.dashboard.stat_overdue') }}</span>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Pending Relationship Requests --}}
  <section class="fe-cms-card fe-exchange-queue fe-dash-queue-card">
    <div class="fe-exchange-queue__head">
      <h2>{{ __('frontend.dashboard.patient_requests_title') }}</h2>
      <a href="{{ route('frontend.psychologist.relationships.index') }}" class="fe-btn fe-btn--outline">
        {{ __('relationship.actions') }}
      </a>
    </div>

    {{-- Legacy compact rows:
    @forelse ($pendingRequests as $req)
      <div class="fe-exchange-queue__row">
        ...
      </div>
    @empty
      <p class="fe-muted" style="margin:0;">{{ __('relationship.no_pending_requests') }}</p>
    @endforelse
    --}}

    @if ($pendingRequests->isEmpty())
      <p class="fe-muted" style="margin:0;">{{ __('relationship.no_pending_requests') }}</p>
    @else
      <div class="fe-table-responsive">
        <table class="fe-table fe-dash-request-table">
          <thead>
            <tr>
              <th>{{ __('relationship.patient') }}</th>
              <th>{{ __('relationship.requested_at') }}</th>
              <th>{{ __('relationship.status') }}</th>
              <th>{{ __('relationship.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pendingRequests as $req)
              <tr>
                <td>{{ $req->patient->user->name ?? trim(($req->patient->first_name ?? '').' '.($req->patient->last_name ?? '')) }}</td>
                <td>{{ $req->requested_at?->format('d M Y H:i') }}</td>
                <td>{{ __('relationship.status_'.$req->status) }}</td>
                <td>
                  <form action="{{ route('frontend.psychologist.relationships.accept', $req) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="fe-btn fe-btn--primary fe-btn--sm">{{ __('relationship.accept') }}</button>
                  </form>
                  <form action="{{ route('frontend.psychologist.relationships.reject', $req) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="fe-btn fe-btn--outline fe-btn--sm">{{ __('relationship.reject') }}</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

  {{-- §5.7 queue of exchanges to process --}}
  <section class="fe-cms-card fe-exchange-queue fe-dash-queue-card">
    <div class="fe-exchange-queue__head">
      <h2>{{ __('frontend.psych_queue.title') }}</h2>
      <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-btn fe-btn--outline">
        {{ __('frontend.dashboard.quick_messages') }}
      </a>
    </div>

    @forelse ($pendingExchanges as $item)
      <a href="{{ route('frontend.psychologist.conversations.show', $item['uuid']) }}" class="fe-exchange-queue__row {{ $item['is_overdue'] ? 'is-overdue' : '' }}">
        <div class="fe-exchange-queue__main">
          <strong>{{ $item['patient_name'] }}</strong>
          <span class="fe-muted">{{ $item['preview'] }}</span>
        </div>
        <div class="fe-exchange-queue__meta">
          @if ($item['is_priority'])
            <span class="fe-badge fe-badge--priority">{{ __('frontend.psych_queue.priority') }}</span>
          @endif
          @if ($item['plan_name'])
            <span class="fe-badge">{{ $item['plan_name'] }}</span>
          @endif
          <span class="fe-exchange-queue__sla {{ $item['is_overdue'] ? 'is-overdue' : '' }}">
            {{ $item['remaining_label'] }}
          </span>
          <span class="fe-muted">{{ $item['sla_label'] }}</span>
        </div>
      </a>
    @empty
      <p class="fe-muted" style="margin:0;">{{ __('frontend.psych_queue.empty') }}</p>
    @endforelse
  </section>
  @endif

  <div class="row gy-20-px mb-20">
    <div class="col-lg-4">
      <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
        <img src="{{ asset('commonassets/images/user-grid/user-grid-bg1.png') }}" alt="" class="w-100 object-fit-cover">
        <a
          href="{{ route('frontend.psychologist.profile.show') }}"
          class="fe-dash-profile-card__menu z-2"
          aria-label="{{ __('frontend.dashboard.quick_profile') }}"
        >
          <i class="bi bi-three-dots-vertical"></i>
        </a>
        <div class="pb-24 ms-16 mb-24 me-16 mt--100">
          <div class="text-center border border-top-0 border-start-0 border-end-0">
            <img
              src="{{ $avatarUrl }}"
              alt="{{ $user->name }}"
              class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover"
            >
            <h2 class="h6 mb-0 mt-16">{{ $displayName }}</h2>
            {{-- <span class="text-secondary-light mb-8 d-block">{{ $user->email }}</span> --}}
            <span class="text-secondary-light text-sm mb-16 d-block">{{ __('frontend.dashboard.psychologist_subtitle') }}</span>
          </div>
          <div class="mt-24">
            <h2 class="h6 text-xl mb-16">{{ __('frontend.dashboard.account_profile') }}</h2>
            <ul>
              <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('backend.psychologists.license') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $psychologist?->license_number ?: '—' }}</span>
              </li>
              <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('backend.psychologists.specialization') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $psychologist?->specialization ?: '—' }}</span>
              </li>
              <li class="d-flex align-items-center gap-1 mb-16">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('frontend.dashboard.stat_verified') }}</span>
                <span class="w-70 fw-medium">:
                  @if (in_array($psychologist?->verification_status, ['verified'], true) || $psychologist?->is_verified)
                    <span class="bg-success-focus text-success-600 border border-success-main px-12 py-2 radius-4 fw-medium text-sm">{{ __('frontend.psych_profile.verification_status_'.($psychologist?->verification_status ?: 'verified')) }}</span>
                  @else
                    <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-2 radius-4 fw-medium text-sm">{{ __('frontend.psych_profile.verification_status_'.($psychologist?->verification_status ?: 'draft')) }}</span>
                  @endif
                </span>
              </li>
            </ul>
            @if ($psychologist)
              @include('frontend.psychologist.partials.verification-banner', ['professional' => $psychologist])
            @endif
            <div class="d-flex flex-wrap align-items-center gap-8">
              <a href="{{ route('frontend.psychologist.profile.show') }}" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                {{ __('frontend.dashboard.quick_profile') }}
              </a>
              <a
                href="{{ route('frontend.psychologist.conversations.index') }}"
                class="w-40-px h-40-px d-flex align-items-center justify-content-center bg-success-50 text-success-600 rounded-circle"
                aria-label="{{ __('frontend.dashboard.quick_messages') }}"
                title="{{ __('frontend.dashboard.quick_messages') }}"
              >
                <i class="bi bi-pencil-square"></i>
              </a>
              <a
                href="{{ route('frontend.psychologist.availability.index') }}"
                class="w-40-px h-40-px d-flex align-items-center justify-content-center bg-danger-50 text-danger-600 rounded-circle"
                aria-label="{{ __('frontend.dashboard.quick_availability') }}"
                title="{{ __('frontend.dashboard.quick_availability') }}"
              >
                <i class="bi bi-calendar-week"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-body p-24">
          <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="psych-home-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link d-flex align-items-center px-24 active" id="psych-home-requests-tab"
                data-bs-toggle="pill" data-bs-target="#psych-home-requests" type="button" role="tab"
                aria-controls="psych-home-requests" aria-selected="true">
                {{ __('frontend.dashboard.patient_requests_title') }}
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link d-flex align-items-center px-24" id="psych-home-queue-tab"
                data-bs-toggle="pill" data-bs-target="#psych-home-queue" type="button" role="tab"
                aria-controls="psych-home-queue" aria-selected="false" tabindex="-1">
                {{ __('frontend.psych_queue.title') }}
              </button>
            </li>
          </ul>

          <div class="tab-content" id="psych-home-tabContent">
            <div class="tab-pane fade show active" id="psych-home-requests" role="tabpanel"
              aria-labelledby="psych-home-requests-tab" tabindex="0">
              @if ($pendingRequests->isEmpty())
                <p class="text-secondary-light mb-16">{{ __('relationship.no_pending_requests') }}</p>
                <a href="{{ route('frontend.psychologist.relationships.index') }}" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                  {{ __('relationship.actions') }}
                </a>
              @else
                <div class="table-responsive">
                  <table class="table bordered-table sm-table mb-0">
                    <thead>
                      <tr>
                        <th>{{ __('relationship.patient') }}</th>
                        <th>{{ __('relationship.requested_at') }}</th>
                        <th>{{ __('relationship.status') }}</th>
                        <th>{{ __('relationship.actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($pendingRequests as $req)
                        <tr>
                          <td>{{ $req->patient->user->name ?? trim(($req->patient->first_name ?? '').' '.($req->patient->last_name ?? '')) }}</td>
                          <td>{{ $req->requested_at?->format('d M Y H:i') }}</td>
                          <td>{{ __('relationship.status_'.$req->status) }}</td>
                          <td>
                            <form action="{{ route('frontend.psychologist.relationships.accept', $req) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="btn btn-primary btn-sm px-16 py-8 radius-8">{{ __('relationship.accept') }}</button>
                            </form>
                            <form action="{{ route('frontend.psychologist.relationships.reject', $req) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="border border-primary-600 text-primary-600 text-md px-16 py-8 radius-8">{{ __('relationship.reject') }}</button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <a href="{{ route('frontend.psychologist.relationships.index') }}" class="border border-primary-600 text-primary-600 text-md px-24 py-11 radius-8 text-decoration-none d-inline-flex mt-16">
                  {{ __('relationship.actions') }}
                </a>
              @endif
            </div>

            <div class="tab-pane fade" id="psych-home-queue" role="tabpanel"
              aria-labelledby="psych-home-queue-tab" tabindex="0">
              @forelse ($pendingExchanges as $item)
                <a href="{{ route('frontend.psychologist.conversations.show', $item['uuid']) }}"
                  class="d-flex flex-wrap align-items-center justify-content-between gap-12 border radius-12 p-16 mb-12 text-decoration-none {{ $item['is_overdue'] ? 'border-danger-main' : '' }}">
                  <div>
                    <strong class="text-primary-light">{{ $item['patient_name'] }}</strong>
                    <p class="mb-0 text-secondary-light text-sm">{{ $item['preview'] }}</p>
                  </div>
                  <div class="d-flex flex-wrap align-items-center gap-8">
                    @if ($item['is_priority'])
                      <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-2 radius-4 fw-medium text-sm">{{ __('frontend.psych_queue.priority') }}</span>
                    @endif
                    @if ($item['plan_name'])
                      <span class="bg-success-focus text-success-600 border border-success-main px-12 py-2 radius-4 fw-medium text-sm">{{ $item['plan_name'] }}</span>
                    @endif
                    <span class="fw-medium {{ $item['is_overdue'] ? 'text-danger-600' : 'text-secondary-light' }}">
                      {{ $item['remaining_label'] }}
                    </span>
                    <span class="text-secondary-light text-sm">{{ $item['sla_label'] }}</span>
                  </div>
                </a>
              @empty
                <p class="text-secondary-light mb-16">{{ __('frontend.psych_queue.empty') }}</p>
              @endforelse
              <a href="{{ route('frontend.psychologist.conversations.index') }}" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                {{ __('frontend.dashboard.quick_messages') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{--
  <div class="fe-quick-links">
    <a href="{{ route('frontend.psychologist.relationships.index') }}" class="fe-quick-link">
      <i class="bi bi-person-plus"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('relationship.pending_requests') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">{{ __('relationship.no_pending_requests') }}</p>
    </a>
    <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-quick-link">
      <i class="bi bi-chat-dots"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('frontend.dashboard.quick_messages') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">{{ $pendingCount }} {{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</p>
    </a>
    <div class="fe-quick-link">
      <i class="bi bi-shield-check"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('frontend.dashboard.stat_verified') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">
        @if ($psychologist?->is_verified)
          {{ __('frontend.dashboard.verified') }}
        @else
          {{ __('frontend.dashboard.pending') }}
        @endif
      </p>
    </div>
  </div>
  --}}

  @if (false)
  <div class="fe-dash-action-grid">
    <a href="{{ route('frontend.psychologist.profile.show') }}" class="fe-dash-action-card">
      <i class="bi bi-person-badge"></i>
      <h3>{{ __('frontend.dashboard.quick_profile') }}</h3>
      <p>{{ __('frontend.dashboard.stat_profile_hint') }}</p>
    </a>
    <a href="{{ route('frontend.psychologist.availability.index') }}" class="fe-dash-action-card">
      <i class="bi bi-calendar-week"></i>
      <h3>{{ __('frontend.dashboard.quick_availability') }}</h3>
      <p>{{ __('frontend.availability.title') }}</p>
    </a>
    <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-dash-action-card">
      <i class="bi bi-chat-dots"></i>
      <h3>{{ __('frontend.dashboard.quick_messages') }}</h3>
      <p>{{ $pendingCount }} {{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</p>
    </a>
  </div>
  @endif

  <div class="row gy-20-px mb-20">
    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.quick_profile') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.profile.show') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.profile.show') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ __('frontend.dashboard.quick_profile') }}</h2>
            <div class="mt-16">
              <span class="text-primary-600 text-lg line-height-1"><i class="ph ph-user"></i></span>
              <span class="text-primary-600 fw-medium">{{ __('frontend.dashboard.stat_profile_hint') }}</span>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.quick_availability') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.availability.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.availability.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ __('frontend.dashboard.quick_availability') }}</h2>
            <div class="mt-16">
              <span class="text-warning-600 text-lg line-height-1"><i class="ph ph-calendar"></i></span>
              <span class="text-warning-600 fw-medium">{{ __('frontend.availability.title') }}</span>
            </div>
          </div>
        </a>
      </div>
    </div>

    {{--<div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.quick_messages') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.psychologist.conversations.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.reports.view') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <a href="{{ route('frontend.psychologist.conversations.index') }}" class="d-flex align-items-center justify-content-between gap-8 text-decoration-none">
          <div>
            <h2 class="text-2xl mb-0">{{ $pendingCount }}</h2>
            <div class="mt-16">
              <span class="text-danger-600 text-lg line-height-1"><i class="ph ph-chat-circle-dots"></i></span>
              <span class="text-danger-600 fw-medium">{{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</span>
            </div>
          </div>
        </a>
      </div>
    </div>--}}
  </div>

  {{-- Legacy action cards:
  <div class="fe-dash-action-grid">
    <a href="{{ route('frontend.psychologist.relationships.index') }}" class="fe-dash-action-card">
      <i class="bi bi-person-plus"></i>
      <h3>{{ __('relationship.pending_requests') }}</h3>
      <p>{{ __('relationship.no_pending_requests') }}</p>
    </a>
    <a href="{{ route('frontend.psychologist.conversations.index') }}" class="fe-dash-action-card">
      <i class="bi bi-chat-dots"></i>
      <h3>{{ __('frontend.dashboard.quick_messages') }}</h3>
      <p>{{ $pendingCount }} {{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</p>
    </a>
    <div class="fe-dash-action-card">
      <i class="bi bi-shield-check"></i>
      <h3>{{ __('frontend.dashboard.stat_verified') }}</h3>
      <p>
        @if ($psychologist?->is_verified)
          {{ __('frontend.dashboard.verified') }}
        @else
          {{ __('frontend.dashboard.pending') }}
        @endif
      </p>
    </div>
  </div>
  --}}
@endsection

@push('scripts')
  <script>
    (function () {
      function getCssVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
      }

      var primaryColor = getCssVar('--primary-600') || '#487FFF';
      var trackColor = getCssVar('--primary-100') || '#E4F1FF';
      var radialValue = {{ (int) $psychKpiRadial }};

      if (typeof ApexCharts === 'undefined') {
        return;
      }

      var barEl = document.querySelector('#psychBarChart');
      if (barEl) {
        new ApexCharts(barEl, {
          series: [{ data: [20, 30, 25, 35, 90, 25] }],
          chart: { type: 'bar', height: 80, width: 130, sparkline: { enabled: true } },
          plotOptions: { bar: { columnWidth: '65%', borderRadius: 3, distributed: true } },
          colors: [trackColor, trackColor, trackColor, trackColor, primaryColor, trackColor],
          dataLabels: { enabled: false },
          tooltip: { enabled: false },
        }).render();
      }

      var lineEl = document.querySelector('#psychLineChart');
      if (lineEl) {
        new ApexCharts(lineEl, {
          series: [{ data: [30, 45, 32, 50, 40, 60, 38, 55, 42, 58, 35, 48, 52, 40, 46] }],
          chart: { type: 'area', height: 80, sparkline: { enabled: true } },
          stroke: { curve: 'smooth', width: 2 },
          fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
          colors: [primaryColor],
          tooltip: { enabled: false },
        }).render();
      }

      var radialEl = document.querySelector('#psychRadialChart');
      if (radialEl) {
        new ApexCharts(radialEl, {
          series: [radialValue],
          chart: { type: 'radialBar', height: 90, width: 90, sparkline: { enabled: true } },
          plotOptions: {
            radialBar: {
              startAngle: 0,
              endAngle: 360,
              hollow: { size: '70%' },
              track: { background: trackColor, strokeWidth: '100%', margin: 0 },
              dataLabels: {
                name: { show: false },
                value: {
                  fontSize: '12px',
                  fontWeight: 500,
                  color: primaryColor,
                  offsetY: 0,
                  show: true,
                  formatter: function (val) { return val + '%'; },
                },
              },
            },
          },
          fill: { colors: [primaryColor] },
          stroke: { lineCap: 'round' },
        }).render();
      }

      var awaitEl = document.querySelector('#psychAwaitChart');
      if (awaitEl) {
        new ApexCharts(awaitEl, {
          series: [{ data: [18, 28, 22, 40, 70, 32] }],
          chart: { type: 'bar', height: 80, width: 130, sparkline: { enabled: true } },
          plotOptions: { bar: { columnWidth: '65%', borderRadius: 3, distributed: true } },
          colors: [trackColor, trackColor, trackColor, primaryColor, trackColor, trackColor],
          dataLabels: { enabled: false },
          tooltip: { enabled: false },
        }).render();
      }
    })();
  </script>
@endpush