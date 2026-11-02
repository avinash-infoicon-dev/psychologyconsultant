<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-block">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">{{ __('backend.dashboard') }}</a>
      </li>
    </ul>

    <ul class="navbar-nav ms-auto">
      <!--begin::Color Mode Toggle-->
      <li class="nav-item dropdown">
        <a
          class="nav-link"
          href="#"
          id="bd-theme"
          aria-label="{{ __('backend.theme') }}"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
          <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
          <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
          <span class="d-none d-md-inline ms-1">{{ __('backend.theme') }}</span>
        </a>
        <ul
          class="dropdown-menu dropdown-menu-end"
          aria-labelledby="bd-theme"
          style="--bs-dropdown-min-width: 8rem"
        >
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center"
              data-bs-theme-value="light"
              aria-pressed="false"
            >
              <i class="bi bi-sun-fill me-2"></i>
              {{ __('backend.theme_light') }}
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center"
              data-bs-theme-value="dark"
              aria-pressed="false"
            >
              <i class="bi bi-moon-fill me-2"></i>
              {{ __('backend.theme_dark') }}
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center"
              data-bs-theme-value="auto"
              aria-pressed="false"
            >
              <i class="bi bi-circle-half me-2"></i>
              {{ __('backend.theme_auto') }}
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
        </ul>
      </li>
      <!--end::Color Mode Toggle-->

      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
          <i class="bi bi-translate"></i>
          <span class="d-none d-md-inline ms-1">{{ __('backend.language') }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          @foreach (\App\Support\Locales::all() as $code => $meta)
            <li>
              <a class="dropdown-item @if(app()->getLocale() === $code) active @endif" href="{{ route('admin.locale', $code) }}">
                {{ $meta['label'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
          <i class="bi bi-bell"></i>
          <span
            class="badge text-bg-danger ms-1"
            data-notification-unread-badge
            @if (($adminNotificationUnread ?? 0) < 1) hidden @endif
          >{{ ($adminNotificationUnread ?? 0) > 99 ? '99+' : ($adminNotificationUnread ?? 0) }}</span>
          <span class="d-none d-md-inline ms-1">{{ __('backend.notifications.title') }}</span>
        </a>
      </li>

      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-5"></i>
          <span class="d-none d-md-inline ms-1">{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <li class="user-header text-bg-primary">
            <p class="mb-0">{{ auth()->user()->name }}</p>
            <small>{{ auth()->user()->primaryRole()?->name }}</small>
          </li>
          <li class="user-footer d-flex justify-content-between align-items-center px-3 py-2">
            @if (auth()->user()->hasAnyRbacRole(['superadmin', 'admin']))
              <a href="{{ route('admin.profile.show') }}" class="btn btn-outline-primary btn-sm">{{ __('backend.profile.title') }}</a>
            @else
              <span></span>
            @endif
            <form action="{{ route('admin.logout') }}" method="post" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('backend.logout') }}</button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
