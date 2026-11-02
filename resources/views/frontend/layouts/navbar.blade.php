<nav class="navbar navbar-expand bg-body border-bottom">
  <div class="container">
    <a class="navbar-brand mb-0 h1 text-decoration-none" href="{{ url('/') }}">{{ config('app.name') }}</a>

    @php
      $headerMenus = app(\App\Services\Cms\CmsMenuService::class)->forLocation('header');
    @endphp

    <ul class="navbar-nav me-auto flex-row align-items-center gap-2 ms-3">
      @foreach ($headerMenus as $item)
        <li class="nav-item @if(! empty($item['children'])) dropdown @endif">
          @if (! empty($item['children']))
            <a class="nav-link dropdown-toggle" href="{{ $item['href'] }}" data-bs-toggle="dropdown" target="{{ $item['target'] }}">
              @if ($item['icon'])<i class="{{ $item['icon'] }} me-1"></i>@endif
              {{ $item['title'] }}
            </a>
            <ul class="dropdown-menu">
              @foreach ($item['children'] as $child)
                <li>
                  <a class="dropdown-item" href="{{ $child['href'] }}" target="{{ $child['target'] }}">
                    @if ($child['icon'])<i class="{{ $child['icon'] }} me-1"></i>@endif
                    {{ $child['title'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            <a class="nav-link" href="{{ $item['href'] }}" target="{{ $item['target'] }}">
              @if ($item['icon'])<i class="{{ $item['icon'] }} me-1"></i>@endif
              {{ $item['title'] }}
            </a>
          @endif
        </li>
      @endforeach
    </ul>

    <ul class="navbar-nav ms-auto flex-row align-items-center gap-2">
      @auth
        @if (auth()->user()->isPatient())
          <li class="nav-item">
            <a class="nav-link @if(request()->routeIs('frontend.patient.profile.*')) active fw-semibold @endif"
              href="{{ route('frontend.patient.profile.show') }}">
              {{ __('profile_api.web.title') }}
            </a>
          </li>
        @endif
      @endauth

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-translate"></i>
          {{ __('backend.language') }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          @foreach (\App\Support\Locales::all() as $code => $meta)
            <li>
              <a class="dropdown-item @if(app()->getLocale() === $code) active @endif"
                href="{{ route('frontend.locale', $code) }}">
                {{ $meta['label'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </li>
    </ul>
  </div>
</nav>
