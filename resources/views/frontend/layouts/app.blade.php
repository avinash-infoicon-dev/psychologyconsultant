<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', config('app.name'))</title>
  <link rel="stylesheet" href="{{ asset('backendassets/css/bootstrap-icons.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('backendassets/css/adminlte.min.css') }}" />
  @stack('styles')
</head>
<body class="bg-body-tertiary">
  @include('frontend.layouts.navbar')

  <main class="container pb-5 pt-4">
    <div class="mb-4">
      <h1 class="h3 mb-0">@yield('page_title')</h1>
      @hasSection('breadcrumb')
        <nav aria-label="breadcrumb" class="mt-2">
          <ol class="breadcrumb mb-0">
            @yield('breadcrumb')
          </ol>
        </nav>
      @endif
    </div>

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
  </main>

  <footer class="border-top py-3 mt-auto">
    <div class="container">
      @php
        $footerMenus = app(\App\Services\Cms\CmsMenuService::class)->forLocation('footer');
      @endphp
      @if ($footerMenus->isNotEmpty())
        <ul class="nav mb-2">
          @foreach ($footerMenus as $item)
            <li class="nav-item">
              <a class="nav-link px-2 text-muted" href="{{ $item['href'] }}" target="{{ $item['target'] }}">{{ $item['title'] }}</a>
            </li>
          @endforeach
        </ul>
      @endif
      <div class="text-muted small">
        &copy; {{ date('Y') }} {{ config('app.name') }}
      </div>
    </div>
  </footer>

  <script src="{{ asset('backendassets/js/bootstrap.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
