@auth
  <meta name="user-id" content="{{ auth()->id() }}">
  <script>
    window.FeBroadcast = {
      key: @json(config('broadcasting.connections.reverb.key')),
      host: @json(request()->getHost()),
      port: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
      scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
      authEndpoint: @json(rtrim(request()->getBasePath(), '/').'/broadcasting/auth'),
      enabled: @json(config('broadcasting.default') === 'reverb' && filled(config('broadcasting.connections.reverb.key'))),
    };

    (function () {
      const path = window.location.pathname;
      const marker = '/public/';
      const idx = path.indexOf(marker);
      if (idx !== -1) {
        window.FeBroadcast.authEndpoint = path.slice(0, idx + marker.length - 1) + '/broadcasting/auth';
      }
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.19.0/dist/echo.iife.js"></script>
@endauth
