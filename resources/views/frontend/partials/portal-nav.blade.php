{{--
  Horizontal replacement for frontend.partials.dashboard-sidebar.
  Restore the sidebar layout in layouts/dashboard.blade.php if you prefer the old shell.
--}}
@auth
  @php
    $isPatient = auth()->user()->isPatient();
    $isPsychologist = auth()->user()->isPsychologist();
  @endphp

  @if ($isPatient || $isPsychologist)
    <nav class="fe-portal-nav" aria-label="{{ __('frontend.nav.dashboard') }}">
      <div class="fe-container fe-portal-nav__inner">
        @if ($isPatient)
          <a href="{{ route('frontend.patient.dashboard') }}" @class(['active' => request()->routeIs('frontend.patient.dashboard')])>
            <i class="bi bi-speedometer2"></i> {{ __('frontend.nav.dashboard') }}
          </a>
          <a href="{{ route('frontend.patient.profile.show') }}" @class(['active' => request()->routeIs('frontend.patient.profile.*')])>
            <i class="bi bi-person"></i> {{ __('frontend.dashboard.quick_profile') }}
          </a>
          <a href="{{ route('frontend.patient.psychologists.index') }}" @class(['active' => request()->routeIs('frontend.patient.psychologists.*')])>
            <i class="bi bi-search"></i> {{ __('frontend.dashboard.quick_psychologists') }}
          </a>
          <a href="{{ route('frontend.patient.conversations.index') }}" @class(['active' => request()->routeIs('frontend.patient.conversations.*')])>
            <i class="bi bi-chat-dots"></i> {{ __('frontend.dashboard.quick_conversations') }}
          </a>
          <a href="{{ route('frontend.patient.subscription.index') }}" @class(['active' => request()->routeIs('frontend.patient.subscription.*')])>
            <i class="bi bi-credit-card"></i> {{ __('frontend.dashboard.quick_subscription') }}
          </a>
        @endif

        @if ($isPsychologist)
          <a href="{{ route('frontend.psychologist.dashboard') }}" @class(['active' => request()->routeIs('frontend.psychologist.dashboard')])>
            <i class="bi bi-speedometer2"></i> {{ __('frontend.nav.dashboard') }}
          </a>
          <a href="{{ route('frontend.psychologist.conversations.index') }}" @class(['active' => request()->routeIs('frontend.psychologist.conversations.*')])>
            <i class="bi bi-chat-dots"></i> {{ __('frontend.dashboard.quick_conversations') }}
          </a>
          <a href="{{ route('frontend.psychologist.patients.index') }}" @class(['active' => request()->routeIs('frontend.psychologist.patients.*') || request()->routeIs('frontend.psychologist.clinical-notes.*')])>
            <i class="bi bi-people"></i> {{ __('frontend.patients.title') }}
          </a>
          <a href="{{ route('frontend.psychologist.templates.index') }}" @class(['active' => request()->routeIs('frontend.psychologist.templates.*')])>
            <i class="bi bi-file-text"></i> {{ __('frontend.templates.title') }}
          </a>
          <a href="{{ route('frontend.psychologist.absences.index') }}" @class(['active' => request()->routeIs('frontend.psychologist.absences.*')])>
            <i class="bi bi-calendar-x"></i> {{ __('frontend.absences.title') }}
          </a>
          <a href="{{ route('frontend.psychologist.tracking.index') }}" @class(['active' => request()->routeIs('frontend.psychologist.tracking.*')])>
            <i class="bi bi-graph-up"></i> {{ __('frontend.tracking.title') }}
          </a>
        @endif
      </div>
    </nav>
  @endif
@endauth
