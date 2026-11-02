<!doctype html>
<html class="no-js theme-light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @auth
    <meta name="user-id" content="{{ auth()->id() }}">
  @endauth
  @include('frontend.partials.theme-boot')
  <title>@yield('title', config('app.name'))</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  {{-- Previous cache-bust (fails on servers where this file is not uploaded):
  <link rel="stylesheet" href="{{ asset('frontendassets/css/app.css') }}?v={{ filemtime(public_path('frontendassets/css/app.css')) }}">
  @php($appCss = public_path('frontendassets/css/app.css'))
  @if (is_file($appCss))
    <link rel="stylesheet" href="{{ asset('frontendassets/css/app.css') }}?v={{ filemtime($appCss) }}">
  @endif
  --}}
  <link rel="stylesheet" href="{{ \App\Support\PublicMedia::versionedAssetHref('frontendassets/css/app.css') }}">
  <style>
    /* Header language / notification menus (demo often lacked app.css) */
    header, #header-sticky, .tp-header-area, .tp-header-right {
      overflow: visible;
      z-index: 10050;
    }
    .fe-lang-dropdown,
    .fe-header-bell {
      position: relative;
      z-index: 10051;
    }
    .tp-header-right .fe-lang-dropdown__toggle,
    .tp-header-right .fe-header-bell__btn {
      cursor: pointer;
      pointer-events: auto;
    }
    .fe-lang-dropdown__menu,
    .fe-header-bell__menu {
      position: absolute;
      top: calc(100% + 0.35rem);
      right: 0;
      left: auto;
      min-width: 220px;
      z-index: 10052;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
      padding: 0.35rem;
    }
    .fe-lang-dropdown__menu[hidden],
    .fe-header-bell__menu[hidden] {
      display: none !important;
    }
  </style>

  <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/swiper-bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/font-awesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/atropos.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontendassets/assets/css/main.css') }}">


  @stack('styles')
</head>
{{-- <body class="fe-body"> --}}
<body class="tp-magic-cursor">
  @unless($hideHeader ?? false)
    @include('frontend.partials.header')
  @endunless

  <div id="smooth-wrapper">
    <div id="smooth-content">
      @yield('content')

      @unless($hideFooter ?? false)
        @include('frontend.partials.footer')
      @endunless
    </div>
  </div>

  @include('frontend.partials.broadcast-scripts')
  {{-- Previous cache-bust (fails on servers where this file is not uploaded / skipped when missing):
  <script src="{{ asset('frontendassets/js/app.js') }}?v={{ filemtime(public_path('frontendassets/js/app.js')) }}"></script>
  @php($appJs = public_path('frontendassets/js/app.js'))
  @if (is_file($appJs))
    <script src="{{ asset('frontendassets/js/app.js') }}?v={{ filemtime($appJs) }}"></script>
  @endif
  --}}
  <script src="{{ \App\Support\PublicMedia::versionedAssetHref('frontendassets/js/app.js') }}"></script>

  <script src="{{ asset('frontendassets/assets/js/jquery.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/bootstrap-bundle.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/swiper-bundle.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/plugin.js') }}  "></script>
  <script src="{{ asset('frontendassets/assets/js/three.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/slick.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/scroll-magic.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/hover-effect.umd.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/magnific-popup.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/parallax-slider.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/nice-select.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/purecounter.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/isotope-pkgd.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/imagesloaded-pkgd.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/ajax-form.js') }}?v={{ is_file(public_path('frontendassets/assets/js/ajax-form.js')) ? filemtime(public_path('frontendassets/assets/js/ajax-form.js')) : time() }}"></script>
  <script src="{{ asset('frontendassets/assets/js/Observer.min.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/splitting.min.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/webgl.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/parallax-scroll.js') }} "></script>
  <script src="{{ asset('frontendassets/assets/js/atropos.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/slider-active.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/main.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/tp-cursor.js') }}"></script>
  <script src="{{ asset('frontendassets/assets/js/portfolio-slider-1.js') }}"></script>
  <script type="module" src="{{ asset('frontendassets/assets/js/distortion-img.js') }}"></script>
  <script type="module" src="{{ asset('frontendassets/assets/js/skew-slider/index.js') }}"></script>
  <script type="module" src="{{ asset('frontendassets/assets/js/img-revel/index.js') }}"></script>


  @stack('scripts')
  <script>
    (function () {
      if (window.__feDropdownBound) {
        return;
      }
      window.__feDropdownBound = true;

      var closeAll = function (exceptEl) {
        document.querySelectorAll('[data-fe-dropdown].open').forEach(function (dropdown) {
          if (exceptEl && dropdown === exceptEl) {
            return;
          }
          dropdown.classList.remove('open');
          var t = dropdown.querySelector('[data-fe-dropdown-toggle]');
          var m = dropdown.querySelector('[data-fe-dropdown-menu]');
          if (t) t.setAttribute('aria-expanded', 'false');
          if (m) m.setAttribute('hidden', '');
        });
      };

      document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-fe-dropdown-toggle]');
        if (toggle) {
          event.preventDefault();
          event.stopPropagation();
          var dropdown = toggle.closest('[data-fe-dropdown]');
          var menu = dropdown ? dropdown.querySelector('[data-fe-dropdown-menu]') : null;
          if (!dropdown || !menu) {
            return;
          }
          var isOpen = dropdown.classList.contains('open');
          closeAll();
          if (!isOpen) {
            dropdown.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
            menu.removeAttribute('hidden');
          }
          return;
        }
        closeAll();
      });
    })();
  </script>
</body>
</html>
