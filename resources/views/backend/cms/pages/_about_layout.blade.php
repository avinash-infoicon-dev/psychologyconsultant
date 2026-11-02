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
<div class="border rounded p-3 bg-body-tertiary">
  <h6 class="mb-3">{{ __('backend.cms.pages.about_layout') }}</h6>
  <p class="form-text mt-0">{{ __('backend.cms.pages.about_layout_help') }}</p>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.hero_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][hero_subtitle]" class="form-control" value="{{ $layout['hero_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.heading') }}</label>
    <textarea name="translations[{{ $locale }}][layout][heading]" class="form-control" rows="2">{{ $layout['heading'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.desc_1') }}</label>
    <textarea name="translations[{{ $locale }}][layout][desc_1]" class="form-control" rows="3">{{ $layout['desc_1'] ?? '' }}</textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.desc_2') }}</label>
    <textarea name="translations[{{ $locale }}][layout][desc_2]" class="form-control" rows="3">{{ $layout['desc_2'] ?? '' }}</textarea>
  </div>
  <div class="row">
    @foreach ([1,2,3,4] as $n)
      <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('backend.cms.pages.list_item') }} {{ $n }}</label>
        <input type="text" name="translations[{{ $locale }}][layout][list_{{ $n }}]" class="form-control" value="{{ $layout['list_'.$n] ?? '' }}">
      </div>
    @endforeach
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.button_text') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][button_text]" class="form-control" value="{{ $layout['button_text'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.button_url') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][button_url]" class="form-control" value="{{ $layout['button_url'] ?? '' }}">
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.hero_background') }}</label>
      <input type="file" name="translations[{{ $locale }}][hero_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('hero_background', 'hero-bg.webp'))
        <img src="{{ $layoutUrl('hero_background', 'hero-bg.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('hero_background'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_hero_background]" value="1" id="remove_hero_{{ $locale }}">
            <label class="form-check-label" for="remove_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.shape_image') }}</label>
      <input type="file" name="translations[{{ $locale }}][shape_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('shape_image', 'about-shape-1.webp'))
        <img src="{{ $layoutUrl('shape_image', 'about-shape-1.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('shape_image'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_shape_image]" value="1" id="remove_shape_{{ $locale }}">
            <label class="form-check-label" for="remove_shape_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.banner_image') }}</label>
      <input type="file" name="translations[{{ $locale }}][banner_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('banner_image', 'video-thumb-1.webp'))
        <img src="{{ $layoutUrl('banner_image', 'video-thumb-1.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('banner_image'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_banner_image]" value="1" id="remove_banner_{{ $locale }}">
            <label class="form-check-label" for="remove_banner_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.about_image') }}</label>
      <input type="file" name="translations[{{ $locale }}][about_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('about_image', 'thumb-1.webp'))
        <img src="{{ $layoutUrl('about_image', 'thumb-1.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('about_image'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_about_image]" value="1" id="remove_about_{{ $locale }}">
            <label class="form-check-label" for="remove_about_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    {{--<div class="col-md-4 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.cta_image') }}</label>
      <input type="file" name="translations[{{ $locale }}][cta_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('cta_image', 'cta-thumb-1.webp'))
        <img src="{{ $layoutUrl('cta_image', 'cta-thumb-1.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('cta_image'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_cta_image]" value="1" id="remove_cta_{{ $locale }}">
            <label class="form-check-label" for="remove_cta_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>--}}
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.team_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][team_subtitle]" class="form-control" value="{{ $layout['team_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.team_title') }}</label>
    <textarea name="translations[{{ $locale }}][layout][team_title]" class="form-control" rows="2">{{ $layout['team_title'] ?? '' }}</textarea>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.team_button_text') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][team_button_text]" class="form-control" value="{{ $layout['team_button_text'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.team_button_url') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][team_button_url]" class="form-control" value="{{ $layout['team_button_url'] ?? '' }}">
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.testimonials_title') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][testimonials_title]" class="form-control" value="{{ $layout['testimonials_title'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.testimonials_rating') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][testimonials_rating]" class="form-control" value="{{ $layout['testimonials_rating'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.cta_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][cta_subtitle]" class="form-control" value="{{ $layout['cta_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.cta_title') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][cta_title]" class="form-control" value="{{ $layout['cta_title'] ?? '' }}">
  </div>
  <div class="mb-0">
    <label class="form-label">{{ __('backend.cms.pages.cta_text') }}</label>
    <textarea name="translations[{{ $locale }}][layout][cta_text]" class="form-control" rows="2">{{ $layout['cta_text'] ?? '' }}</textarea>
  </div>
</div>
