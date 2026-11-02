@php
  $user = auth()->user();
  $isPatient = $user->isPatient();
  $isPsychologist = $user->isPsychologist();
  $dashboardHome = $isPatient
    ? route('frontend.patient.dashboard')
    : route('frontend.psychologist.dashboard');
  $profileRoute = $isPatient
    ? route('frontend.patient.profile.show')
    : route('frontend.psychologist.profile.show');
  $roleLabel = $isPatient
    ? __('relationship.patient')
    : ($isPsychologist ? __('backend.patients.psychologist') : '');
  $avatarUrl = $user->professional?->profile_image_url
    ?? asset('commonassets/images/user.png');
  $themeToggleLabelLight = __('frontend.theme.to_light');
  $themeToggleLabelDark = __('frontend.theme.to_dark');
@endphp

<div class="navbar-header">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <button type="button" class="twin-collapse line-height-1" aria-label="{{ __('frontend.nav.dashboard') }}">
          <i class="ph ph-arrow-u-up-left icon non-active"></i>
          <i class="ph ph-arrow-u-up-right icon active"></i>
          <!-- <i class="ph ph-arrow-u-up-left icon active"></i>
          <i class="ph ph-arrow-u-up-right icon non-active"></i> -->
        </button>
      </div>
    </div>

    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">
        @include('frontend.partials.language-dropdown')

        {{--<button
          type="button"
          data-fe-theme-toggle
          data-label-light="{{ $themeToggleLabelLight }}"
          data-label-dark="{{ $themeToggleLabelDark }}"
          aria-label="{{ $themeToggleLabelDark }}"
          title="{{ $themeToggleLabelDark }}"
          class="w-44-px h-44-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center bg-base fe-dashboard-theme-btn"
        >
          <i class="bi bi-moon-stars" data-fe-theme-icon-dark></i>
          <i class="bi bi-sun" data-fe-theme-icon-light hidden></i>
        </button>--}}

        @include('frontend.partials.header-message-bell')

        <div class="dropdown">
          <button
            class="d-flex justify-content-center align-items-center rounded-circle bg-transparent"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <span class="d-flex align-items-center gap-12">
              <span>
                <img
                  src="{{ $avatarUrl }}"
                  alt="{{ $user->name }}"
                  class="w-44-px h-44-px object-fit-cover rounded-circle"
                >
              </span>
              <span class="d-sm-block d-none text-start">
                <span class="text-lg text-primary-light fw-semibold mb-0 d-block lh-1">{{ $user->name }}</span>
                <span class="text-secondary-light fw-medium text-sm lh-1">{{ $roleLabel }}</span>
              </span>
              <span class="text-neutral-500">
                <i class="ph-bold ph-caret-down"></i>
              </span>
            </span>
          </button>
          <div class="dropdown-menu to-top dropdown-menu-sm">
            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h2 class="h6 text-lg text-primary-light fw-semibold mb-2">{{ $user->name }}</h2>
                <span class="text-secondary-light fw-medium text-sm">{{ $roleLabel }}</span>
              </div>
            </div>
            <ul class="to-top-list">
              {{--<li>
                <a
                  class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                  href="{{ $profileRoute }}"
                >
                  <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>
                  {{ __('frontend.dashboard.quick_profile') }}
                </a>
              </li>--}}
              <li>
                <a
                  class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                  href="{{ $dashboardHome }}"
                >
                  <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-xl"></iconify-icon>
                  {{ __('frontend.nav.dashboard') }}
                </a>
              </li>
              <li>
                <form action="{{ route('frontend.auth.logout') }}" method="post">
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 w-100"
                  >
                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                    {{ __('frontend.nav.logout') }}
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
