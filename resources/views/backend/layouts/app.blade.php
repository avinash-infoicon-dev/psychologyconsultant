<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @auth
    <meta name="user-id" content="{{ auth()->id() }}">
  @endauth
  <title>@yield('title', __('backend.panel')) | {{ config('app.name') }}</title>
  <script>
    (() => {
      const STORAGE_KEY = 'lte-theme';
      let stored = null;
      try { stored = localStorage.getItem(STORAGE_KEY); } catch {}
      const prefersDark = globalThis.matchMedia('(prefers-color-scheme: dark)').matches;
      let resolved = 'light';
      if (stored === 'dark' || stored === 'light') resolved = stored;
      else if (prefersDark) resolved = 'dark';
      document.documentElement.setAttribute('data-bs-theme', resolved);
      document.documentElement.style.colorScheme = resolved;
    })();
  </script>
  {{-- Local CSS (icon/font files must live next to these stylesheets) --}}
  <link rel="stylesheet" href="{{ asset('backendassets/css/fontsource_index.css') }}" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="{{ asset('backendassets/css/bootstrap-icons.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('backendassets/css/overlayscrollbars.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('backendassets/css/adminlte.min.css') }}" />

  {{-- Fallback until local font files are downloaded (run: public/backendassets/download-fonts.sh) --}}
  @if (! file_exists(public_path('backendassets/css/fonts/bootstrap-icons.woff2')))
    <style>
      @font-face {
        font-display: block;
        font-family: bootstrap-icons;
        src: url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/fonts/bootstrap-icons.woff2") format("woff2"),
             url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/fonts/bootstrap-icons.woff") format("woff");
      }
    </style>
  @endif
  @stack('styles')
  <style>
    [data-notification-unread-badge][hidden] { display: none !important; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
  @php
    $adminNotificationUnread = auth()->user()->notifications()->whereNull('read_at')->count();
  @endphp
  <div class="app-wrapper">
    @include('backend.layouts.navbar')
    @include('backend.layouts.sidebar')

    <main class="app-main">
      <div class="app-content-header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h3 class="mb-0">@yield('page_title', __('backend.dashboard'))</h3>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('backend.dashboard') }}</a></li>
                @yield('breadcrumb')
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="app-content">
        <div class="container-fluid">
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          @yield('content')
        </div>
      </div>
    </main>

    <footer class="app-footer">
      <strong>{{ config('app.name') }}</strong> &mdash; {{ __('backend.panel') }}
    </footer>
  </div>

  {{--<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>--}}

  <script src="{{ asset('backendassets/js/popper.min.js') }}" ></script>
  <script src="{{ asset('backendassets/js/bootstrap.min.js') }}" ></script>
  <script src="{{ asset('backendassets/js/overlayscrollbars.browser.es6.min.js') }}" ></script>

  <script src="{{ asset('backendassets/js/adminlte.min.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sidebarWrapper = document.querySelector('.sidebar-wrapper');
      if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars && window.innerWidth > 992) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: { theme: 'os-theme-light', autoHide: 'leave', clickScroll: true },
        });
      }
    });
  </script>
  @stack('scripts')
  @include('frontend.partials.broadcast-scripts')
  <script src="{{ asset('backendassets/js/notifications.js') }}?v={{ is_file(public_path('backendassets/js/notifications.js')) ? filemtime(public_path('backendassets/js/notifications.js')) : time() }}"></script>
</body>
</html>
