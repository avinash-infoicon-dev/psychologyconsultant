@extends('frontend.layouts.dashboard')

@section('title', __('frontend.nav.dashboard'))
@section('page_title', __('frontend.nav.dashboard'))

@section('content')
  {{--
  <div class="fe-welcome-card">
    <h1 style="margin:0 0 .5rem;font-size:1.75rem;">{{ __('frontend.dashboard.welcome', ['name' => $user->name]) }}</h1>
    <p style="margin:0;opacity:.92;">{{ __('frontend.dashboard.patient_subtitle') }}</p>
  </div>

  <div class="fe-dash-stats">
    <div class="fe-dash-stat">
      <strong><i class="bi bi-search"></i></strong>
      <span>{{ __('frontend.dashboard.stat_psychologists') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_psychologists_hint') }}</small>
    </div>
    <div class="fe-dash-stat">
      <strong>{{ $conversationCount }}</strong>
      <span>{{ __('frontend.dashboard.stat_conversations') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_conversations_hint') }}</small>
    </div>
    <a href="{{ route('frontend.patient.subscription.index') }}" class="fe-dash-stat" style="text-decoration:none;color:inherit;">
      <strong><i class="bi bi-credit-card"></i></strong>
      <span>{{ __('frontend.dashboard.stat_plan') }}</span>
      <small style="color:var(--fe-muted);">{{ __('frontend.dashboard.stat_plan_hint') }}</small>
    </a>
  </div>

  <div class="fe-quick-links">
    <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-quick-link">
      <i class="bi bi-people"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('frontend.dashboard.quick_psychologists') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">{{ __('frontend.dashboard.stat_psychologists_hint') }}</p>
    </a>
    <a href="{{ route('frontend.patient.conversations.index') }}" class="fe-quick-link">
      <i class="bi bi-chat-left-text"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('frontend.dashboard.quick_conversations') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">{{ __('frontend.dashboard.stat_conversations_hint') }}</p>
    </a>
    <a href="{{ route('frontend.patient.subscription.index') }}" class="fe-quick-link">
      <i class="bi bi-credit-card"></i>
      <h3 style="margin:.5rem 0 .25rem;font-size:1rem;">{{ __('frontend.dashboard.quick_subscription') }}</h3>
      <p style="margin:0;color:var(--fe-muted);font-size:.9rem;">{{ __('frontend.dashboard.stat_plan_hint') }}</p>
    </a>
  </div>
  --}}

  @php
    $avatarUrl = asset('commonassets/images/user.png');
    $care = $care ?? [];
    $unreadMessageCount = $unreadMessageCount ?? 0;
  @endphp

  <div class="row gy-20-px mb-20">
    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.quick_conversations') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.conversations.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.quick_conversations') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0">{{ $conversationCount }}</h2>
            <div class="mt-16">
              <span class="text-primary-600 text-lg line-height-1">
                <i class="ph ph-chat-circle-dots"></i>
              </span>
              <span class="text-primary-600 fw-medium">{{ $unreadMessageCount }} {{ __('frontend.dashboard.unread_messages') }}</span>
            </div>
          </div>
          <div>
            {{-- <div id="barChart"></div> --}}
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ $subscription?->plan?->name ?: __('frontend.dashboard.no_plan') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.subscription.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.quick_subscription') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0">{{ (int) ($exchangeBalance['remaining'] ?? 0) }}</h2>
            <div class="mt-16">
              <span class="text-warning-600 text-lg line-height-1">
                <i class="ph ph-arrows-down-up"></i>
              </span>
              <span class="text-warning-600 fw-medium">{{ __('frontend.dashboard.exchanges_remaining') }}</span>
            </div>
            <div class="mt-8 text-secondary-light text-sm">
              {{ __('frontend.dashboard.renewal') }}:
              {{ $subscription?->current_period_ends_at?->format('d M Y') ?? ($exchangeBalance['renewal_label'] ?? '—') }}
            </div>
          </div>
          <div style="width:140px">
            <div class="remove-tooltip-title rounded-tooltip-value">
              {{-- <div id="lineChart"></div> --}}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.dashboard.find_psychologist') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.psychologists.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.find_psychologist') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0"><i class="ph ph-magnifying-glass"></i></h2>
            <div class="mt-16">
              <span class="text-danger-600 text-lg line-height-1">
                <i class="ph ph-arrow-right"></i>
              </span>
              <a href="{{ route('frontend.patient.psychologists.index') }}">
                <span class="text-danger-600 fw-medium">{{ __('frontend.dashboard.find_psychologist_hint') }}</span>
              </a>
            </div>
          </div>
          <div>
            {{-- <div id="radialChart" class="m--8-px" style="min-width:90px; min-height:90px;"></div> --}}
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="row gy-20-px mb-20">
    <div class="col-lg-4">
      <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
        <img src="{{ asset('commonassets/images/user-grid/user-grid-bg1.png') }}" alt="" class="w-100 object-fit-cover">
        <a
          href="{{ route('frontend.patient.profile.show') }}"
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
            <h2 class="h6 mb-0 mt-16">{{ __('frontend.dashboard.welcome', ['name' => $user->name]) }}</h2>
            <span class="text-secondary-light mb-8 d-block">{{ $user->email }}</span>
            <span class="text-secondary-light text-sm mb-16 d-block">{{ __('frontend.dashboard.patient_subtitle') }}</span>
          </div>
          <div class="mt-24">
            <h2 class="h6 text-xl mb-16">{{ __('frontend.dashboard.account_profile') }}</h2>
            <ul>
             {{-- <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('backend.name') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $user->name }}</span>
              </li>
              <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('backend.email') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $user->email }}</span>
              </li>
              <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('frontend.dashboard.stat_conversations') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $conversationCount }}</span>
              </li>--}}
              <li class="d-flex align-items-center gap-1 mb-12">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('frontend.dashboard.stat_plan') }}</span>
                <span class="w-70 text-secondary-light fw-medium">: {{ $subscription?->plan?->name ?: __('frontend.dashboard.no_plan') }}</span>
              </li>
              <li class="d-flex align-items-center gap-1 mb-16">
                <span class="w-30 text-md fw-semibold text-primary-light">{{ __('backend.patients.email_verified') }}</span>
                <span class="w-70 fw-medium">:
                  @if ($user->hasVerifiedEmail())
                    <span class="bg-success-focus text-success-600 border border-success-main px-12 py-2 radius-4 fw-medium text-sm">{{ __('frontend.dashboard.verified') }}</span>
                  @else
                    <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-2 radius-4 fw-medium text-sm">{{ __('ui.pending') }}</span>
                  @endif
                </span>
              </li>
            </ul>
            <div class="d-flex flex-wrap align-items-center gap-8">
              @if ($care['messaging_paused'] ?? false)
                <a href="{{ $care['history_url'] ?? route('frontend.patient.conversations.index') }}" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                  {{ __('frontend.dashboard.view_conversation_history') }}
                </a>
              @elseif (($care['continue_url'] ?? null))
                <a href="{{ $care['continue_url'] }}" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                  {{ __('frontend.dashboard.continue_conversation') }}
                </a>
              @else
                <a href="{{ route('frontend.patient.psychologists.index') }}" class="btn btn-primary btn-sm px-16 py-8 radius-8">
                  {{ __('frontend.dashboard.find_psychologist') }}
                </a>
              @endif
              <a
                href="{{ route('frontend.patient.conversations.index') }}"
                class="w-40-px h-40-px d-flex align-items-center justify-content-center bg-success-50 text-success-600 rounded-circle"
                aria-label="{{ __('frontend.dashboard.quick_conversations') }}"
                title="{{ __('frontend.dashboard.quick_conversations') }}"
              >
                <i class="bi bi-pencil-square"></i>
              </a>
              <a
                href="{{ route('frontend.patient.subscription.index') }}"
                class="w-40-px h-40-px d-flex align-items-center justify-content-center bg-danger-50 text-danger-600 rounded-circle"
                aria-label="{{ __('frontend.dashboard.quick_subscription') }}"
                title="{{ __('frontend.dashboard.quick_subscription') }}"
              >
                <i class="bi bi-credit-card"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-body p-24">
          <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="patient-home-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link d-flex align-items-center px-24 active" id="patient-home-care-tab"
                data-bs-toggle="pill" data-bs-target="#patient-home-care" type="button" role="tab"
                aria-controls="patient-home-care" aria-selected="true">
                {{ __('frontend.dashboard.care_title') }}
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link d-flex align-items-center px-24" id="patient-home-actions-tab"
                data-bs-toggle="pill" data-bs-target="#patient-home-actions" type="button" role="tab"
                aria-controls="patient-home-actions" aria-selected="false" tabindex="-1">
                {{ __('relationship.actions') }}
              </button>
            </li>
          </ul>

          <div class="tab-content" id="patient-home-tabContent">
            <div class="tab-pane fade show active" id="patient-home-care" role="tabpanel"
              aria-labelledby="patient-home-care-tab" tabindex="0">
              @if ($care['psychologist_name'] ?? null)
                <p class="mb-12">
                  <strong class="text-primary-light">{{ $care['psychologist_name'] }}</strong>
                  @if ($care['profile_url'] ?? null)
                    <a href="{{ $care['profile_url'] }}" class="ms-8 text-primary-600">
                      {{ __('frontend.professionals.view_profile') }}
                    </a>
                  @endif
                </p>
                <p class="text-secondary-light mb-16">
                  <span class="fw-semibold text-primary-light">{{ __('frontend.dashboard.care_last_message') }}</span>
                  {{ $care['last_message'] ?: __('frontend.dashboard.care_no_messages') }}
                </p>
                @if ($care['messaging_paused'] ?? false)
                  <p class="text-warning-600 fw-medium mb-8">{{ __('frontend.dashboard.conversation_paused') }}</p>
                  <p class="text-secondary-light mb-16">{{ __('frontend.dashboard.psychologist_unavailable') }}</p>
                  <div class="d-flex flex-wrap align-items-center gap-12">
                    @if ($care['history_url'] ?? null)
                      <a href="{{ $care['history_url'] }}" class="border border-primary-600 text-primary-600 text-md px-24 py-11 radius-8 text-decoration-none">
                        {{ __('frontend.dashboard.view_conversation_history') }}
                      </a>
                    @endif
                    <a href="{{ route('frontend.patient.psychologists.index') }}" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8">
                      {{ __('frontend.dashboard.find_another_psychologist') }}
                    </a>
                  </div>
                @else
                  <p class="fw-medium mb-16 {{ $care['response_pending'] ? 'text-warning-600' : 'text-secondary-light' }}">
                    @if ($care['response_pending'])
                      {{ __('frontend.dashboard.care_response_pending') }}
                    @else
                      {{ __('frontend.dashboard.care_no_pending') }}
                    @endif
                  </p>
                  @if ($care['continue_url'] ?? null)
                    <a href="{{ $care['continue_url'] }}" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                      {{ __('frontend.dashboard.continue_conversation') }}
                    </a>
                  @endif
                @endif
              @else
                <p class="text-secondary-light mb-16">{{ __('frontend.dashboard.care_none') }}</p>
                <a href="{{ route('frontend.patient.psychologists.index') }}" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                  {{ __('frontend.dashboard.find_psychologist') }}
                </a>
              @endif
            </div>

            <div class="tab-pane fade" id="patient-home-actions" role="tabpanel"
              aria-labelledby="patient-home-actions-tab" tabindex="0">
              <div class="row gy-16">
                <div class="col-sm-4">
                  <a href="{{ route('frontend.patient.psychologists.index') }}" class="d-block border radius-12 p-16 h-100 text-decoration-none">
                    <i class="bi bi-people text-primary-600 text-2xl"></i>
                    <h3 class="text-md fw-semibold text-primary-light mt-12 mb-4">{{ __('frontend.dashboard.quick_psychologists') }}</h3>
                    <p class="mb-0 text-secondary-light text-sm">{{ __('frontend.dashboard.stat_psychologists_hint') }}</p>
                  </a>
                </div>
                <div class="col-sm-4">
                  <a href="{{ route('frontend.patient.conversations.index') }}" class="d-block border radius-12 p-16 h-100 text-decoration-none">
                    <i class="bi bi-chat-left-text text-primary-600 text-2xl"></i>
                    <h3 class="text-md fw-semibold text-primary-light mt-12 mb-4">{{ __('frontend.dashboard.quick_conversations') }}</h3>
                    <p class="mb-0 text-secondary-light text-sm">{{ $pendingCount }} {{ __('frontend.dashboard.stat_pending_exchanges_hint') }}</p>
                  </a>
                </div>
                <div class="col-sm-4">
                  <a href="{{ route('frontend.patient.subscription.index') }}" class="d-block border radius-12 p-16 h-100 text-decoration-none">
                    <i class="bi bi-credit-card text-primary-600 text-2xl"></i>
                    <h3 class="text-md fw-semibold text-primary-light mt-12 mb-4">{{ __('frontend.dashboard.quick_subscription') }}</h3>
                    <p class="mb-0 text-secondary-light text-sm">{{ __('frontend.dashboard.stat_plan_hint') }}</p>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @php
    $balance = $exchangeBalance ?? [];
    $balanceTotal = (int) ($balance['total'] ?? ((int) ($balance['included'] ?? 0) + (int) ($balance['purchased'] ?? 0)));
  @endphp

  <div class="row gy-20-px mb-20">
    <div class="col-sm-4">
      <div
        class="bg-base p-24 radius-20 h-100{{ (int) ($balance['remaining'] ?? 0) <= 2 ? ' is-low' : '' }}"
        data-exchange-balance
      >
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.subscription.remaining_headline') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.subscription.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.quick_subscription') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0">
              <span data-exchange-remaining>{{ (int) ($balance['remaining'] ?? 0) }}</span>
              /
              <span data-exchange-total>{{ $balanceTotal }}</span>
            </h2>
            <div class="mt-16">
              <span class="text-primary-600 text-lg line-height-1">
                <i class="ph ph-arrow-up"></i>
              </span>
              <span class="text-primary-600 fw-medium">
                {{ __('frontend.subscription.included') }}
                <strong data-exchange-included-value>{{ (int) ($balance['included'] ?? 0) }}</strong>
              </span>
            </div>
          </div>
        </div>
        <ul class="d-flex flex-wrap gap-12 mt-16 mb-0 list-unstyled text-secondary-light text-sm">
          <li>
            {{ __('frontend.subscription.purchased') }}
            <strong class="text-neutral-900" data-exchange-purchased>+{{ (int) ($balance['purchased'] ?? 0) }}</strong>
          </li>
          <li>
            {{ __('frontend.subscription.consumed') }}
            <strong class="text-neutral-900" data-exchange-consumed>-{{ (int) ($balance['consumed'] ?? 0) }}</strong>
          </li>
        </ul>
        <p class="mt-12 mb-0 text-secondary-light text-sm" data-exchange-renewal-wrap @if (! ($balance['renewal_label'] ?? null)) hidden @endif>
          {{ __('frontend.subscription.renews_on') }}
          <time data-exchange-renewal datetime="{{ $balance['renewal_date'] ?? null }}">{{ $balance['renewal_label'] ?? '' }}</time>
        </p>
        <p class="mt-8 mb-0 text-warning-600 text-sm" data-exchange-low-hint @if ((int) ($balance['remaining'] ?? 0) > 2) hidden @endif>
          {{ __('frontend.subscription.low_balance_hint') }}
        </p>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('relationship.accepted_requests') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.psychologists.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.quick_psychologists') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0">{{ $acceptedRequests->count() }}</h2>
            <div class="mt-16">
              <span class="text-warning-600 text-lg line-height-1">
                <i class="ph ph-arrows-down-up"></i>
              </span>
              <span class="text-warning-600 fw-medium">{{ __('relationship.accepted_requests') }}</span>
            </div>
          </div>
        </div>
        <div class="d-flex flex-column gap-12 mt-20">
          @forelse ($acceptedRequests as $req)
            @php
              $psych = $req->psychologist;
              $psychName = $psych?->user?->name
                ?? trim(($psych?->first_name ?? '').' '.($psych?->last_name ?? ''))
                ?: __('frontend.chat.unknown');
            @endphp
            <div class="d-flex flex-column gap-8 pb-12 border-bottom border-neutral-200">
              <strong class="text-neutral-900">{{ $psychName }}</strong>
              <span class="text-secondary-light text-sm">{{ __('relationship.request_accepted_notification', ['name' => $psychName]) }}</span>
              <div class="d-flex flex-wrap align-items-center gap-8">
                @if ($req->accepted_at)
                  <span class="text-secondary-light text-sm">{{ $req->accepted_at->diffForHumans() }}</span>
                @endif
                <form action="{{ route('frontend.patient.relationships.activate', $req) }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm px-12 py-4 radius-8">
                    {{ __('relationship.status_accepted_pay') }}
                  </button>
                </form>
                @if ($psych?->uuid)
                  <a href="{{ route('frontend.patient.psychologists.show', $psych->uuid) }}" class="btn btn-outline-primary btn-sm px-12 py-4 radius-8">
                    {{ __('frontend.professionals.view_profile') }}
                  </a>
                @endif
              </div>
            </div>
          @empty
            <p class="text-secondary-light mb-0">{{ __('relationship.no_accepted_requests') }}</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-sm-4">
      <div class="bg-base p-24 radius-20 h-100">
        <div class="d-flex align-items-center justify-content-between gap-8 pb-20 mb-20 border-bottom border-neutral-200">
          <span class="text-neutral-700 fw-medium">{{ __('frontend.psych_queue.title') }}</span>
          <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a href="{{ route('frontend.patient.conversations.index') }}"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10">
                  <iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon>
                  {{ __('frontend.dashboard.quick_messages') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-8">
          <div>
            <h2 class="text-2xl mb-0">{{ $pendingCount }}</h2>
            <div class="mt-16">
              <span class="text-danger-600 text-lg line-height-1">
                <i class="ph ph-arrow-down"></i>
              </span>
              <span class="text-danger-600 fw-medium">{{ __('frontend.psych_queue.title') }}</span>
            </div>
          </div>
        </div>
        <div class="d-flex flex-column gap-12 mt-20">
          @forelse ($pendingExchanges as $item)
            <a href="{{ route('frontend.patient.conversations.show', $item['uuid']) }}" class="d-flex flex-column gap-4 pb-12 border-bottom border-neutral-200 text-decoration-none">
              <strong class="text-neutral-900">{{ $item['psychologist_name'] }}</strong>
              <span class="text-secondary-light text-sm">{{ $item['preview'] }}</span>
              <div class="d-flex flex-wrap align-items-center gap-8">
                @if ($item['plan_name'])
                  <span class="badge bg-primary-50 text-primary-600">{{ $item['plan_name'] }}</span>
                @endif
                <span class="text-secondary-light text-sm">{{ $item['remaining_label'] }}</span>
              </div>
            </a>
          @empty
            <p class="text-secondary-light mb-0">{{ __('frontend.psych_queue.empty') }}</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      function getCssVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
      }

      var primaryColor = getCssVar('--primary-600') || '#487FFF';
      var trackColor = getCssVar('--primary-100') || '#E4F1FF';

      var barEl = document.querySelector('#barChart');
      if (barEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(barEl, {
          series: [{ data: [20, 30, 25, 35, 90, 25] }],
          chart: { type: 'bar', height: 80, width: 130, sparkline: { enabled: true } },
          plotOptions: { bar: { columnWidth: '65%', borderRadius: 3, distributed: true } },
          colors: [trackColor, trackColor, trackColor, trackColor, primaryColor, trackColor],
          dataLabels: { enabled: false },
          tooltip: { enabled: false },
        }).render();
      }

      var lineEl = document.querySelector('#lineChart');
      if (lineEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(lineEl, {
          series: [{ data: [30, 45, 32, 50, 40, 60, 38, 55, 42, 58, 35, 48, 52, 40, 46] }],
          chart: { type: 'area', height: 80, sparkline: { enabled: true } },
          stroke: { curve: 'smooth', width: 2 },
          fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0 } },
          colors: [primaryColor],
          tooltip: { enabled: true },
        }).render();
      }

      var radialEl = document.querySelector('#radialChart');
      if (radialEl && typeof ApexCharts !== 'undefined') {
        new ApexCharts(radialEl, {
          series: [35.5],
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
          labels: ['Conversion Rate'],
        }).render();
      }
    })();
  </script>
@endpush
