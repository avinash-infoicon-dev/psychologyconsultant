@php
  $themeToggleLabelLight = __('frontend.theme.to_light');
  $themeToggleLabelDark = __('frontend.theme.to_dark');
@endphp

<button
  type="button"
  class="fe-header-icon-btn"
  data-fe-theme-toggle
  data-label-light="{{ $themeToggleLabelLight }}"
  data-label-dark="{{ $themeToggleLabelDark }}"
  aria-label="{{ $themeToggleLabelDark }}"
  title="{{ $themeToggleLabelDark }}"
>
  <i class="bi bi-moon-stars" data-fe-theme-icon-dark></i>
  <i class="bi bi-sun" data-fe-theme-icon-light hidden></i>
</button>
