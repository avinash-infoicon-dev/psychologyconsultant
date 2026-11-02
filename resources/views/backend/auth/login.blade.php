<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name') }} | {{ __('backend.sign_in') }}</title>
    <script>
      (() => {
        'use strict';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="stylesheet" href="{{ asset('backendassets/css/fontsource_index.css') }}" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="{{ asset('backendassets/css/bootstrap-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('backendassets/css/adminlte.min.css') }}" />
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
  </head>
  <body class="login-page bg-body-secondary">
    <main class="login-box">
      <h1 class="login-logo">
        <a href="{{ url('/') }}"><b>{{ __('backend.panel') }}</b></a>
      </h1>
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">{{ __('backend.sign_in_msg') }}</p>

          <div class="mb-3 text-center">
            @foreach (\App\Support\Locales::all() as $code => $meta)
              <a href="{{ route('admin.locale', $code) }}" class="btn btn-sm @if(app()->getLocale() === $code) btn-primary @else btn-outline-secondary @endif">{{ $meta['short'] }}</a>
            @endforeach
          </div>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
          @endif

          <form action="{{ route('admin.login') }}" method="post">
            @csrf
            <label class="visually-hidden" for="loginEmail">{{ __('backend.email') }}</label>
            <div class="input-group mb-3">
              <input id="loginEmail" name="email" type="email" class="form-control" placeholder="{{ __('backend.email') }}" value="{{ old('email') }}" required autofocus />
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>
            <label class="visually-hidden" for="loginPassword">{{ __('backend.password') }}</label>
            <div class="input-group mb-3">
              <input id="loginPassword" name="password" type="password" class="form-control" placeholder="{{ __('backend.password') }}" required />
              <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
            </div>
            <div class="row">
              <div class="col-8">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="remember" value="1" id="rememberMe" @checked(old('remember')) />
                  <label class="form-check-label" for="rememberMe">{{ __('backend.remember') }}</label>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">{{ __('backend.sign_in') }}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
    <script src="{{ asset('backendassets/js/popper.min.js') }}"></script>
    <script src="{{ asset('backendassets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('backendassets/js/adminlte.min.js') }}"></script>
  </body>
</html>
