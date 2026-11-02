{{-- Reusable empty state. Pass $title, $hint, optional $actionUrl, $actionLabel. --}}
<div class="fe-empty-state">
  <p class="fe-empty-state__title">{{ $title }}</p>
  @if (! empty($hint))
    <p class="fe-empty-state__hint">{{ $hint }}</p>
  @endif
  @if (! empty($actionUrl) && ! empty($actionLabel))
    <a href="{{ $actionUrl }}" class="fe-btn fe-btn--primary fe-btn--sm">{{ $actionLabel }}</a>
  @endif
</div>
