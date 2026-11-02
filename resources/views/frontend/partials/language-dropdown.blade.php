@php
  $currentLocale = app()->getLocale();
  $currentMeta = \App\Support\Locales::all()[$currentLocale] ?? ['label' => $currentLocale, 'short' => strtoupper($currentLocale)];
@endphp

<div class="fe-lang-dropdown" data-fe-dropdown>
  <button type="button" class="fe-lang-dropdown__toggle" data-fe-dropdown-toggle aria-expanded="false">
    <i class="bi bi-translate"></i>
    <span>{{ $currentMeta['short'] }}</span>
    <i class="bi bi-chevron-down fe-nav__chevron"></i>
  </button>
  <div class="fe-lang-dropdown__menu" data-fe-dropdown-menu hidden>
    @foreach (\App\Support\Locales::all() as $code => $meta)
      <a
        href="{{ route('frontend.locale', $code) }}"
        class="fe-lang-dropdown__item @if($currentLocale === $code) active @endif"
      >
        <span class="fe-lang-dropdown__short">{{ $meta['short'] }}</span>
        <span>{{ $meta['label'] }}</span>
      </a>
    @endforeach
  </div>
</div>
