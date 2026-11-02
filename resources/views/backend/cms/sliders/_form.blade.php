@php($sliderModel = $slider ?? null)
<div class="row mb-3">
  <div class="col-md-3 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $sliderModel?->sort_order ?? 0) }}">
  </div>
  <div class="col-md-3 mb-3 d-flex align-items-end">
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $sliderModel?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#slider-locale-{{ $locale }}" type="button" role="tab">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $sliderModel?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="slider-locale-{{ $locale }}" role="tabpanel">
      <div class="mb-3">
        <label class="form-label" for="subtitle_{{ $locale }}">{{ __('backend.cms.sliders.subtitle') }}</label>
        <input type="text" name="translations[{{ $locale }}][subtitle]" id="subtitle_{{ $locale }}" class="form-control"
          value="{{ old("translations.$locale.subtitle", $tr?->subtitle) }}">
      </div>
      <div class="mb-3">
        <label class="form-label" for="title_{{ $locale }}">{{ __('backend.cms.title') }}</label>
        <input type="text" name="translations[{{ $locale }}][title]" id="title_{{ $locale }}" class="form-control"
          value="{{ old("translations.$locale.title", $tr?->title) }}" required>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="button_text_{{ $locale }}">{{ __('backend.cms.sliders.button_text') }}</label>
          <input type="text" name="translations[{{ $locale }}][button_text]" id="button_text_{{ $locale }}" class="form-control"
            value="{{ old("translations.$locale.button_text", $tr?->button_text) }}">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="button_url_{{ $locale }}">{{ __('backend.cms.sliders.button_url') }}</label>
          <input type="text" name="translations[{{ $locale }}][button_url]" id="button_url_{{ $locale }}" class="form-control"
            value="{{ old("translations.$locale.button_url", $tr?->button_url ?? '#contact-form') }}" placeholder="#contact-form">
          <div class="form-text">{{ __('backend.cms.sliders.button_url_help') }}</div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="image_{{ $locale }}">{{ __('backend.cms.sliders.image') }}</label>
        <input type="file" name="translations[{{ $locale }}][image]" id="image_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
        <div class="form-text">{{ __('backend.cms.sliders.image_help') }}</div>
        @if ($tr?->image_url)
          <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
            <img src="{{ $tr->image_url }}" alt="" style="width:160px;height:90px;object-fit:cover;border-radius:6px;">
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_image]" value="1" id="remove_image_{{ $locale }}">
              <label class="form-check-label" for="remove_image_{{ $locale }}">{{ __('backend.cms.sliders.remove_image') }}</label>
            </div>
          </div>
        @endif
      </div>
    </div>
  @endforeach
</div>
