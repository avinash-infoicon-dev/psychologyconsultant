@php
  $layout = old("translations.$locale.layout", $tr?->layout ?? []);
  $layout = is_array($layout) ? $layout : [];
  $layoutUrl = function (string $key, ?string $default = null) use ($layout) {
      return \App\Support\PublicMedia::url($layout[$key] ?? null)
          ?: ($default ? \App\Support\PublicMedia::url($default) : null);
  };
  $isUploaded = function (string $key) use ($layout) {
      $path = $layout[$key] ?? null;

      return is_string($path) && str_starts_with($path, 'cms/pages/');
  };
@endphp
<div class="border rounded p-3 bg-body-tertiary mt-3">
  <h6 class="mb-3">{{ __('backend.cms.pages.contact_layout') }}</h6>
  <p class="form-text mt-0">{{ __('backend.cms.pages.contact_layout_help') }}</p>

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.hero_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][hero_subtitle]" class="form-control" value="{{ $layout['hero_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.contact_kicker') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][contact_kicker]" class="form-control" value="{{ $layout['contact_kicker'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.heading') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][heading]" class="form-control" value="{{ $layout['heading'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.side_heading') }}</label>
    <textarea name="translations[{{ $locale }}][layout][side_heading]" class="form-control" rows="2">{{ $layout['side_heading'] ?? '' }}</textarea>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.phone_label') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][phone_label]" class="form-control" value="{{ $layout['phone_label'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.phone') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][phone]" class="form-control" value="{{ $layout['phone'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.email_label') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][email_label]" class="form-control" value="{{ $layout['email_label'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.email') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][email]" class="form-control" value="{{ $layout['email'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.hours_label') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][hours_label]" class="form-control" value="{{ $layout['hours_label'] ?? '' }}">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.hours') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][hours]" class="form-control" value="{{ $layout['hours'] ?? '' }}">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.map_embed') }}</label>
    <textarea name="translations[{{ $locale }}][layout][map_embed]" class="form-control" rows="3">{{ $layout['map_embed'] ?? '' }}</textarea>
    <div class="form-text">{{ __('backend.cms.pages.map_embed_help') }}</div>
  </div>

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.social_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][social_subtitle]" class="form-control" value="{{ $layout['social_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.social_text') }}</label>
    <textarea name="translations[{{ $locale }}][layout][social_text]" class="form-control" rows="2">{{ $layout['social_text'] ?? '' }}</textarea>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.social_facebook') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][social_facebook]" class="form-control" value="{{ $layout['social_facebook'] ?? '' }}">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.social_twitter') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][social_twitter]" class="form-control" value="{{ $layout['social_twitter'] ?? '' }}">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.social_instagram') }}</label>
      <input type="text" name="translations[{{ $locale }}][layout][social_instagram]" class="form-control" value="{{ $layout['social_instagram'] ?? '' }}">
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.hero_background') }}</label>
      <input type="file" name="translations[{{ $locale }}][hero_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp')))
        <img src="{{ $layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp')) }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('hero_background'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_hero_background]" value="1" id="remove_contact_hero_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.contact_background') }}</label>
      <input type="file" name="translations[{{ $locale }}][contact_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('contact_background'))
        <img src="{{ $layoutUrl('contact_background') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('contact_background'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_contact_background]" value="1" id="remove_contact_bg_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_bg_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.funfact_background') }}</label>
      <input type="file" name="translations[{{ $locale }}][funfact_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('funfact_background', 'frontendassets/assets/img/funfact-bg-shape-2.webp'))
        <img src="{{ $layoutUrl('funfact_background', 'frontendassets/assets/img/funfact-bg-shape-2.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('funfact_background'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_funfact_background]" value="1" id="remove_contact_funfact_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_funfact_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.funfact_circle') }}</label>
      <input type="file" name="translations[{{ $locale }}][funfact_circle]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('funfact_circle'))
        <img src="{{ $layoutUrl('funfact_circle') }}" alt="" class="mt-2" style="max-width:70px;height:70px;object-fit:cover;">
        @if ($isUploaded('funfact_circle'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_funfact_circle]" value="1" id="remove_contact_circle_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_circle_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.social_background') }}</label>
      <input type="file" name="translations[{{ $locale }}][social_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('social_background', 'frontendassets/assets/img/contact-us-shape.webp'))
        <img src="{{ $layoutUrl('social_background', 'frontendassets/assets/img/contact-us-shape.webp') }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('social_background'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_social_background]" value="1" id="remove_contact_social_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_social_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">{{ __('backend.cms.pages.cta_image') }}</label>
      <input type="file" name="translations[{{ $locale }}][cta_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
      @if ($layoutUrl('cta_image', \App\Support\PublicMedia::aboutDefault('cta-thumb-1.webp')))
        <img src="{{ $layoutUrl('cta_image', \App\Support\PublicMedia::aboutDefault('cta-thumb-1.webp')) }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
        @if ($isUploaded('cta_image'))
          <div class="form-check mt-1">
            <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_cta_image]" value="1" id="remove_contact_cta_{{ $locale }}">
            <label class="form-check-label" for="remove_contact_cta_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
          </div>
        @endif
      @endif
    </div>
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
