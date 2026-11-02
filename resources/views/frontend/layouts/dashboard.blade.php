<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @auth
    <meta name="user-id" content="{{ auth()->id() }}">
  @endauth
  @include('frontend.partials.theme-boot')
  <title>@yield('title', __('frontend.nav.dashboard')) | {{ config('app.name') }}</title>

  @php
    $assetVersion = static function (string $relative): string {
      $path = public_path($relative);

      return is_file($path) ? (string) filemtime($path) : (string) time();
    };
  @endphp

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  {{-- <link rel="stylesheet" href="{{ asset('frontendassets/css/app.css') }}?v={{ filemtime(public_path('frontendassets/css/app.css')) }}"> --}}
  <link rel="stylesheet" href="{{ asset('frontendassets/css/app.css') }}?v={{ $assetVersion('frontendassets/css/app.css') }}">

  <link rel="icon" type="image/png" href="{{ asset('commonassets/images/favicon.png')}}" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/remixicon.css')}}">
  <!-- BootStrap css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/bootstrap.min.css')}}">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/apexcharts.css')}}">
  <!-- Data Table css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/dataTables.min.css')}}">
  <!-- Text Editor css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/editor-katex.min.css')}}">
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/editor.atom-one-dark.min.css')}}">
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/editor.quill.snow.css')}}">
  <!-- Date picker css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/flatpickr.min.css')}}">
  <!-- Calendar css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/full-calendar.css')}}">
  <!-- Vector Map css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/jquery-jvectormap-2.0.5.css')}}">
  <!-- Popup css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/magnific-popup.css')}}">
  <!-- Slick Slider css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/slick.css')}}">
  <!-- prism css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/prism.css')}}">
  <!-- file upload css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/file-upload.css')}}">
  <link rel="stylesheet" href="{{ asset('commonassets/css/lib/audioplayer.css')}}">
  <!-- main css -->
  <link rel="stylesheet" href="{{ asset('commonassets/css/style.css')}}">
  {{-- <link rel="stylesheet" href="{{ asset('frontendassets/css/dashboard.css') }}?v={{ filemtime(public_path('frontendassets/css/dashboard.css')) }}"> --}}
  <link rel="stylesheet" href="{{ asset('frontendassets/css/dashboard.css') }}?v={{ $assetVersion('frontendassets/css/dashboard.css') }}">

  @stack('styles')
</head>
<body class="position-relative z-1">
{{--<body class="position-relative z-1 twin-collapsed">--}}
  <img
    src="{{ asset('commonassets/images/body-bg.png') }}"
    alt=""
    class="body-bg position-absolute top-0 start-0 h-100 w-100 z-n1"
  >

  @php
    $isChatPage = request()->routeIs('frontend.patient.conversations.*')
      || request()->routeIs('frontend.psychologist.conversations.*');
  @endphp

  @include('frontend.partials.dashboard-sidebar')

  <main class="dashboard-main">
    @include('frontend.partials.dashboard-navbar')

    <div class="dashboard-main-body {{ $isChatPage ? 'is-chat' : '' }}">
      @hasSection('page_title')
        @php
          $isDashboardHome = request()->routeIs('frontend.patient.dashboard')
            || request()->routeIs('frontend.psychologist.dashboard');
        @endphp
        @unless ($isDashboardHome)
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h2 class="h6 fw-semibold mb-0">@yield('page_title')</h2>
            <ul class="d-flex align-items-center gap-2 mb-0 list-unstyled">
              <li class="fw-medium">
                <a
                  href="{{ auth()->user()->isPatient() ? route('frontend.patient.dashboard') : route('frontend.psychologist.dashboard') }}"
                  class="d-flex align-items-center gap-1 hover-text-primary"
                >
                  <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                  {{ __('frontend.nav.dashboard') }}
                </a>
              </li>
              <li>-</li>
              <li class="fw-medium">@yield('page_title')</li>
            </ul>
          </div>
        @endunless
      @endif

      @if (session('success'))
        <div class="fe-alert fe-alert--success pos-alert">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div class="fe-alert fe-alert--error">{{ session('error') }}</div>
      @endif

      @if ($errors->any())
        <div class="fe-alert fe-alert--error" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>

    <footer class="d-footer">
      @php
        $footerMenus = app(\App\Services\Cms\CmsMenuService::class)->forLocation('footer');
        $headerMenus = $footerMenus->isEmpty()
          ? app(\App\Services\Cms\CmsMenuService::class)->forLocation('header')
          : $footerMenus;
      @endphp
      <div class="row align-items-center justify-content-between">
        <div class="col-auto">
          <p class="mb-0">&copy; {{ date('Y') }} <span class="fw-bold text-primary-600">{{ config('app.name') }}</span>. {{ __('frontend.brand') }}.</p>
        </div>
        <div class="col-auto">
          <div class="d-flex align-items-center gap-24 flex-wrap">
            @foreach ($headerMenus as $item)
              <a href="{{ $item['href'] }}" target="{{ $item['target'] }}" class="text-neutral-500 text-hover-primary-600">{{ $item['title'] }}</a>
            @endforeach
          </div>
        </div>
      </div>
    </footer>
  </main>

  @include('frontend.partials.broadcast-scripts')
  {{-- <script src="{{ asset('frontendassets/js/app.js') }}?v={{ filemtime(public_path('frontendassets/js/app.js')) }}"></script> --}}
  <script src="{{ asset('frontendassets/js/app.js') }}?v={{ $assetVersion('frontendassets/js/app.js') }}"></script>

  <!-- jQuery library js -->
  <script src="{{ asset('commonassets/js/lib/jquery-3.7.1.min.js')}}"></script>
  <!-- Bootstrap js -->
  <script src="{{ asset('commonassets/js/lib/bootstrap.bundle.min.js')}}"></script>
  <!-- Apex Chart js -->
  <script src="{{ asset('commonassets/js/lib/apexcharts.min.js')}}"></script>
  <!-- Data Table js -->
  <script src="{{ asset('commonassets/js/lib/dataTables.min.js')}}"></script>
  <!-- Iconify Font js -->
  <script src="{{ asset('commonassets/js/lib/iconify-icon.min.js')}}"></script>
  <!-- phosphor -->
  <script src="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2"></script>
  <!-- jQuery UI js -->
  <script src="{{ asset('commonassets/js/lib/jquery-ui.min.js')}}"></script>
  <!-- Vector Map js -->
  <script src="{{ asset('commonassets/js/lib/jquery-jvectormap-2.0.5.min.js')}}"></script>
  <script src="{{ asset('commonassets/js/lib/jquery-jvectormap-world-mill-en.js')}}"></script>
  <!-- Popup js -->
  <script src="{{ asset('commonassets/js/lib/magnifc-popup.min.js')}}"></script>
  <!-- Slick Slider js -->
  <script src="{{ asset('commonassets/js/lib/slick.min.js')}}"></script>
  <!-- prism js -->
  <script src="{{ asset('commonassets/js/lib/prism.js')}}"></script>
  <!-- file upload js -->
  <script src="{{ asset('commonassets/js/lib/file-upload.js')}}"></script>
  <!-- audioplayer -->
  <script src="{{ asset('commonassets/js/lib/audioplayer.js')}}"></script>
  <!-- main js -->
  <script src="{{ asset('commonassets/js/app.js')}}"></script>
  {{-- <script src="{{ asset('frontendassets/js/dashboard.js') }}?v={{ filemtime(public_path('frontendassets/js/dashboard.js')) }}"></script> --}}
  <script src="{{ asset('frontendassets/js/dashboard.js') }}?v={{ $assetVersion('frontendassets/js/dashboard.js') }}"></script>

  @stack('scripts')
</body>
</html>
