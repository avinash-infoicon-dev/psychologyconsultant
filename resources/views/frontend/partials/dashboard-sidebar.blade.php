{{-- Twin sidebar (icon rail + menu panel). Same routes and role visibility as before. --}}
@php
  $site = app(\App\Services\Cms\CmsSiteSettingService::class)->current();
  $headerLogo = $site->logoUrl('dark') ?: $site->logoUrl();
  $user = auth()->user();
  $isPatient = $user->isPatient();
  $isPsychologist = $user->isPsychologist();
  $notificationUnread = $user->notifications()->whereNull('read_at')->count();

  $dashboardHome = $isPatient
    ? route('frontend.patient.dashboard')
    : route('frontend.psychologist.dashboard');

  $activeMenu = 'dashboards';
  if ($isPatient) {
    if (request()->routeIs('frontend.patient.psychologists.*')) {
      $activeMenu = 'psychologist';
    } elseif (request()->routeIs('frontend.patient.conversations.*') || request()->routeIs('frontend.patient.messages.*')) {
      $activeMenu = 'conversations';
    } elseif (request()->routeIs('frontend.patient.subscription.*')) {
      $activeMenu = 'billing';
    } elseif (request()->routeIs('frontend.patient.profile.*') || request()->routeIs('frontend.notifications.*') || request()->routeIs('frontend.patient.reports.*')) {
      $activeMenu = 'profile';
    }
  }
  if ($isPsychologist) {
    if (request()->routeIs('frontend.psychologist.conversations.*') || request()->routeIs('frontend.psychologist.messages.*')) {
      $activeMenu = 'conversations';
    } elseif (request()->routeIs('frontend.psychologist.absences.*') || request()->routeIs('frontend.psychologist.availability.*')) {
      $activeMenu = 'absences';
    } elseif (request()->routeIs('frontend.psychologist.patients.*') || request()->routeIs('frontend.psychologist.clinical-notes.*')) {
      $activeMenu = 'patients';
    } elseif (request()->routeIs('frontend.psychologist.templates.*')) {
      $activeMenu = 'templates';
    } elseif (request()->routeIs('frontend.psychologist.tracking.*')) {
      $activeMenu = 'tracking';
    } elseif (request()->routeIs('frontend.psychologist.profile.*') || request()->routeIs('frontend.notifications.*') || request()->routeIs('frontend.psychologist.reports.*')) {
      $activeMenu = 'profile';
    }
  }
@endphp

<button type="button" class="twin-mobile-toggle" aria-label="{{ __('frontend.nav.dashboard') }}">
  <i class="ph ph-list"></i>
</button>
<div class="twin-backdrop"></div>

<aside class="twin-sidebar" aria-label="{{ __('frontend.nav.dashboard') }}">
{{--<aside class="twin-sidebar is-collapsed" aria-label="{{ __('frontend.nav.dashboard') }}">--}}
  <div class="twin-rail">
    <a href="{{ route('frontend.home') }}" class="twin-rail__brand" aria-label="{{ $site->brandName() }}">
      {{-- <img src="{{ asset('commonassets/images/logo-icon.png') }}" alt="{{ __('frontend.brand') }}"> --}}
      <img src="{{ $headerLogo }}" alt="{{ $site->brandName() }}">
    </a>

    <div class="twin-rail__nav">
      {{--<button type="button" class="rail-icon {{ $activeMenu === 'dashboards' ? 'active' : '' }}" data-menu="dashboards" aria-label="{{ __('frontend.nav.dashboard') }}">
        <i class="ph ph-house-simple"></i><span class="rail-tip">{{ __('frontend.nav.dashboard') }}</span>
      </button>--}}
      <a href="{{ $dashboardHome }}" class="rail-icon {{ $activeMenu === 'dashboards' ? 'active' : '' }}" aria-label="{{ __('frontend.nav.dashboard') }}">
        <i class="ph ph-house-simple"></i><span class="rail-tip">{{ __('frontend.nav.dashboard') }}</span>
      </a>

      @if ($isPatient)
        {{--<button type="button" class="rail-icon {{ $activeMenu === 'psychologist' ? 'active' : '' }}" data-menu="psychologist" aria-label="{{ __('frontend.dashboard.quick_psychologists') }}">
          <i class="ph ph-brain"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_psychologists') }}</span>
        </button>--}}
         <a href="{{ route('frontend.patient.psychologists.index') }}" class="rail-icon {{ $activeMenu === 'psychologist' ? 'active' : '' }}" aria-label="{{ __('frontend.dashboard.quick_psychologists') }}">
          <i class="ph ph-brain"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_psychologists') }}</span>
        </a>
      @endif

      @if ($isPsychologist)
        
        {{--<button type="button" class="rail-icon {{ $activeMenu === 'patients' ? 'active' : '' }}" data-menu="patients" aria-label="{{ __('frontend.patients.title') }}">
          <i class="ph ph-users"></i><span class="rail-tip">{{ __('frontend.patients.title') }}</span>
        </button>--}}
        <a href="{{ route('frontend.psychologist.patients.index') }}" type="button" class="rail-icon {{ $activeMenu === 'patients' ? 'active' : '' }}" aria-label="{{ __('frontend.patients.title') }}">
          <i class="ph ph-users"></i><span class="rail-tip">{{ __('frontend.patients.title') }}</span>
        </a>
      
        {{--<button type="button" class="rail-icon {{ $activeMenu === 'absences' ? 'active' : '' }}" data-menu="absences" aria-label="{{ __('frontend.absences.title') }}">
          <i class="ph ph-calendar-blank"></i><span class="rail-tip">{{ __('frontend.absences.title') }}</span>
        </button>--}}
        <a href="{{ route('frontend.psychologist.absences.index') }}" class="rail-icon {{ $activeMenu === 'absences' ? 'active' : '' }}" aria-label="{{ __('frontend.absences.title') }}">
          <i class="ph ph-calendar-blank"></i><span class="rail-tip">{{ __('frontend.absences.title') }}</span>
        </a>
      @endif

      @if ($isPatient)
        {{--<button type="button" class="rail-icon {{ $activeMenu === 'billing' ? 'active' : '' }}" data-menu="billing" aria-label="{{ __('frontend.dashboard.quick_subscription') }}">
          <i class="ph ph-credit-card"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_subscription') }}</span>
        </button>--}}
        <a href="{{ route('frontend.patient.subscription.index') }}" class="rail-icon {{ $activeMenu === 'billing' ? 'active' : '' }}" aria-label="{{ __('frontend.dashboard.quick_subscription') }}">
          <i class="ph ph-credit-card"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_subscription') }}</span>
        </a>
      @endif

      {{--<button type="button" class="rail-icon {{ $activeMenu === 'conversations' ? 'active' : '' }}" data-menu="conversations" aria-label="{{ __('frontend.dashboard.quick_conversations') }}">
        <i class="ph ph-chat-circle-text"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_conversations') }}</span>
      </button>--}}
      <a href="{{ $isPatient ? route('frontend.patient.conversations.index') : route('frontend.psychologist.conversations.index') }}" class="rail-icon {{ $activeMenu === 'conversations' ? 'active' : '' }}" aria-label="{{ __('frontend.dashboard.quick_conversations') }}">
        <i class="ph ph-chat-circle-text"></i><span class="rail-tip">{{ __('frontend.dashboard.quick_conversations') }}</span>
      </a>
    </div>
    

    {{--<div class="twin-rail__bottom">
      <button type="button" class="rail-icon {{ $activeMenu === 'profile' ? 'active' : '' }}" data-menu="profile" aria-label="{{ __('frontend.dashboard.profile') }}">
        <i class="ph ph-user-circle"></i><span class="rail-tip">{{ __('frontend.dashboard.profile') }}</span>
      </button>
    </div>--}}
      @if ($isPatient)
      <div class="twin-rail__bottom">
        <a href="{{ route('frontend.patient.profile.show') }}" class="rail-icon {{ $activeMenu === 'profile' ? 'active' : '' }}" aria-label="{{ __('frontend.dashboard.profile') }}">
          <i class="ph ph-user-circle"></i><span class="rail-tip">{{ __('frontend.dashboard.profile') }}</span>
        </a>
      </div>
      @endif
      @if ($isPsychologist)
      <div class="twin-rail__bottom">
        <a href="{{ route('frontend.psychologist.profile.show') }}" class="rail-icon {{ $activeMenu === 'profile' ? 'active' : '' }}" aria-label="{{ __('frontend.dashboard.profile') }}">
          <i class="ph ph-user-circle"></i><span class="rail-tip">{{ __('frontend.dashboard.profile') }}</span>
        </a>
      </div>
      @endif

  </div>

  <div class="twin-panel">
    <div class="twin-panel__head">
      <a href="{{ route('frontend.home') }}" class="twin-panel__logo" aria-label="{{ $site->brandName() }}">
        {{-- {{ __('frontend.brand') }} --}}
        <img src="{{ $headerLogo }}" alt="{{ $site->brandName() }}">
      </a>
    </div>

    <div class="twin-panel__body">
      <ul class="twin-menu {{ $activeMenu === 'dashboards' ? 'active' : '' }}" data-menu="dashboards">
        <li @class(['active' => request()->routeIs('frontend.patient.dashboard') || request()->routeIs('frontend.psychologist.dashboard')])>
          <a href="{{ $dashboardHome }}">
            <i class="ph ph-house-simple"></i><span>{{ __('frontend.nav.dashboard') }}</span>
          </a>
        </li>
        @if ($isPsychologist)
          {{--
          <li @class(['active' => request()->routeIs('frontend.psychologist.relationships.*')])>
            <a href="{{ route('frontend.psychologist.relationships.index') }}">
              <i class="ph ph-user-plus"></i><span>{{ __('relationship.pending_requests') }}</span>
            </a>
          </li>
          --}}
        @endif
      </ul>

      @if ($isPatient)
        <ul class="twin-menu {{ $activeMenu === 'psychologist' ? 'active' : '' }}" data-menu="psychologist">
          <li @class(['active' => request()->routeIs('frontend.patient.psychologists.*')])>
            <a href="{{ route('frontend.patient.psychologists.index') }}">
              <i class="ph ph-magnifying-glass"></i><span>{{ __('frontend.dashboard.quick_psychologists') }}</span>
            </a>
          </li>
        </ul>
      @endif

      @if ($isPsychologist)
        
        <ul class="twin-menu {{ $activeMenu === 'patients' ? 'active' : '' }}" data-menu="patients">
          <li @class(['active' => request()->routeIs('frontend.psychologist.patients.*') || request()->routeIs('frontend.psychologist.clinical-notes.*')])>
            <a href="{{ route('frontend.psychologist.patients.index') }}">
              <i class="ph ph-user-list"></i><span>{{ __('frontend.patients.title') }}</span>
            </a>
          </li>
        </ul>
        

        <ul class="twin-menu {{ $activeMenu === 'absences' ? 'active' : '' }}" data-menu="absences">
          <li @class(['active' => request()->routeIs('frontend.psychologist.absences.*')])>
            <a href="{{ route('frontend.psychologist.absences.index') }}">
              <i class="ph ph-calendar-x"></i><span>{{ __('frontend.absences.title') }}</span>
            </a>
          </li>
          
          <li @class(['active' => request()->routeIs('frontend.psychologist.availability.*')])>
            <a href="{{ route('frontend.psychologist.availability.index') }}">
              <i class="ph ph-clock"></i><span>{{ __('frontend.availability.title') }}</span>
            </a>
          </li>
          
        </ul>

        {{--
        <ul class="twin-menu {{ $activeMenu === 'templates' ? 'active' : '' }}" data-menu="templates">
          <li @class(['active' => request()->routeIs('frontend.psychologist.templates.*')])>
            <a href="{{ route('frontend.psychologist.templates.index') }}">
              <i class="ph ph-file-text"></i><span>{{ __('frontend.templates.title') }}</span>
            </a>
          </li>
        </ul>

        <ul class="twin-menu {{ $activeMenu === 'tracking' ? 'active' : '' }}" data-menu="tracking">
          <li @class(['active' => request()->routeIs('frontend.psychologist.tracking.*')])>
            <a href="{{ route('frontend.psychologist.tracking.index') }}">
              <i class="ph ph-chart-line-up"></i><span>{{ __('frontend.tracking.title') }}</span>
            </a>
          </li>
        </ul>
        --}}
      @endif

      @if ($isPatient)
        <ul class="twin-menu {{ $activeMenu === 'billing' ? 'active' : '' }}" data-menu="billing">
          <li @class(['active' => request()->routeIs('frontend.patient.subscription.index')])>
            <a href="{{ route('frontend.patient.subscription.index') }}">
              <i class="ph ph-wallet"></i><span>{{ __('frontend.dashboard.quick_subscription') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.patient.subscription.current')])>
            <a href="{{ route('frontend.patient.subscription.current') }}">
              <i class="ph ph-crown"></i><span>{{ __('frontend.subscription.current_plan') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.patient.subscription.plans')])>
            <a href="{{ route('frontend.patient.subscription.plans') }}">
              <i class="ph ph-package"></i><span>{{ __('frontend.subscription.all_plans') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.patient.subscription.history')])>
            <a href="{{ route('frontend.patient.subscription.history') }}">
              <i class="ph ph-clock-counter-clockwise"></i><span>{{ __('frontend.subscription.subscriptions_history') }}</span>
            </a>
          </li>
          {{--<li @class(['active' => request()->routeIs('frontend.patient.subscription.exchanges')])>
            <a href="{{ route('frontend.patient.subscription.exchanges') }}">
              <i class="ph ph-arrows-left-right"></i><span>{{ __('frontend.subscription.exchanges_history') }}</span>
            </a>
          </li>--}}
        </ul>
      @endif
      <ul class="twin-menu {{ $activeMenu === 'conversations' ? 'active' : '' }}" data-menu="conversations">
        <li @class(['active' => request()->routeIs('frontend.patient.conversations.*') || request()->routeIs('frontend.psychologist.conversations.*')])>
          <a href="{{ $isPatient ? route('frontend.patient.conversations.index') : route('frontend.psychologist.conversations.index') }}">
            <i class="ph ph-chat-circle-text"></i><span>{{ __('frontend.dashboard.quick_conversations') }}</span>
          </a>
        </li>
      </ul>

      <ul class="twin-menu {{ $activeMenu === 'profile' ? 'active' : '' }}" data-menu="profile">
        @if ($isPatient)
          <li @class(['active' => request()->routeIs('frontend.patient.profile.show') || request()->routeIs('frontend.patient.profile.update')])>
            <a href="{{ route('frontend.patient.profile.show') }}">
              <i class="ph ph-user-circle"></i><span>{{ __('frontend.dashboard.account_profile') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.notifications.*')])>
            <a href="{{ route('frontend.notifications.index') }}">
              <i class="ph ph-bell"></i>
              <span>{{ __('frontend.notifications.history_title') }}</span>
              <span class="twin-menu__badge" data-notification-unread-badge @if ($notificationUnread < 1) hidden @endif>{{ $notificationUnread > 99 ? '99+' : $notificationUnread }}</span>
            </a>
          </li>
         {{-- <li @class(['active' => request()->routeIs('frontend.patient.reports.*')])>
            <a href="{{ route('frontend.patient.reports.index') }}">
              <i class="ph ph-flag"></i><span>{{ __('frontend.reports.title') }}</span>
            </a>
          </li>
          <li>--}}
            <a href="{{ route('frontend.patient.profile.export') }}">
              <i class="ph ph-download-simple"></i><span>{{ __('frontend.dashboard.export_data') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.patient.profile.delete') || request()->routeIs('frontend.patient.profile.destroy')])>
            <a href="{{ route('frontend.patient.profile.delete') }}">
              <i class="ph ph-trash"></i><span>{{ __('frontend.dashboard.delete_profile') }}</span>
            </a>
          </li>
        @endif

        @if ($isPsychologist)
          <li @class(['active' => request()->routeIs('frontend.psychologist.profile.*')])>
            <a href="{{ route('frontend.psychologist.profile.show') }}">
              <i class="ph ph-user-circle"></i><span>{{ __('frontend.dashboard.quick_profile') }}</span>
            </a>
          </li>
          <li @class(['active' => request()->routeIs('frontend.notifications.*')])>
            <a href="{{ route('frontend.notifications.index') }}">
              <i class="ph ph-bell"></i>
              <span>{{ __('frontend.notifications.history_title') }}</span>
              <span class="twin-menu__badge" data-notification-unread-badge @if ($notificationUnread < 1) hidden @endif>{{ $notificationUnread > 99 ? '99+' : $notificationUnread }}</span>
            </a>
          </li>
          {{--<li @class(['active' => request()->routeIs('frontend.psychologist.reports.*')])>
            <a href="{{ route('frontend.psychologist.reports.index') }}">
              <i class="ph ph-flag"></i><span>{{ __('frontend.reports.title') }}</span>
            </a>
          </li>--}}
        @endif

        <li>
          <form action="{{ route('frontend.auth.logout') }}" method="post" class="twin-logout-form">
            @csrf
            @method('DELETE')
            <button type="submit">
              <i class="ph ph-sign-out"></i><span>{{ __('frontend.nav.logout') }}</span>
            </button>
          </form>
        </li>
      </ul>
    </div>

    {{--<div class="twin-panel__foot">
      @if ($isPatient)
        <div class="upgrade-card">
          <span class="upgrade-card__art"><i class="ph ph-rocket-launch"></i></span>
          <p class="upgrade-card__text">{{ __('frontend.subscription.plans_title') }}</p>
          <a href="{{ route('frontend.patient.subscription.index') }}" class="upgrade-btn">{{ __('frontend.subscription.subscribe') }}</a>
        </div>
      @endif
      <span class="twin-version">v1.0</span>
    </div>--}}
  </div>
</aside>
