@php
  $layout = old("translations.$locale.layout", $tr?->layout ?? []);
  $layout = is_array($layout) ? $layout : [];
  $layoutUrl = function (string $key, ?string $defaultFile = null) use ($layout) {
      return \App\Support\PublicMedia::url($layout[$key] ?? null)
          ?: ($defaultFile ? \App\Support\PublicMedia::url(\App\Support\PublicMedia::aboutDefault($defaultFile)) : null);
  };
  $isUploaded = function (string $key) use ($layout) {
      $path = $layout[$key] ?? null;

      return is_string($path) && str_starts_with($path, 'cms/pages/');
  };
@endphp
<div class="border rounded p-3 bg-body-tertiary mt-3">
  <h6 class="mb-3">{{ __('backend.cms.pages.default_layout') }}</h6>
  <p class="form-text mt-0">{{ __('backend.cms.pages.default_layout_help') }}</p>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.hero_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][hero_subtitle]" class="form-control" value="{{ $layout['hero_subtitle'] ?? '' }}">
  </div>
  <div class="mb-0">
    <label class="form-label">{{ __('backend.cms.pages.hero_background') }}</label>
    <input type="file" name="translations[{{ $locale }}][hero_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($layoutUrl('hero_background', 'hero-bg.webp'))
      <img src="{{ $layoutUrl('hero_background', 'hero-bg.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
      @if ($isUploaded('hero_background'))
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_hero_background]" value="1" id="remove_default_hero_{{ $locale }}">
          <label class="form-check-label" for="remove_default_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
        </div>
      @endif
    @endif
  </div>
</div>
