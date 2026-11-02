@php
  $layout = old("translations.$locale.layout", $tr?->layout ?? []);
  $layout = is_array($layout) ? $layout : [];
  $includePageFields = $includePageFields ?? false;
  $featureIndexes = [1, 2, 3];
  $videoPreview = \App\Support\PublicMedia::url($layout['video_image'] ?? null)
      ?: asset('frontendassets/assets/img/video/video-thumb-1.webp');
  $videoIsUploaded = is_string($layout['video_image'] ?? null) && str_starts_with($layout['video_image'], 'cms/pages/');
@endphp
<div class="border rounded p-3 bg-body-secondary">
  @if ($includePageFields)
    <div class="mb-3">
      <label class="form-label" for="title_{{ $locale }}">{{ __('backend.cms.title') }}</label>
      <input type="text" name="translations[{{ $locale }}][title]" id="title_{{ $locale }}" class="form-control"
        value="{{ old("translations.$locale.title", $tr?->title) }}" required>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label" for="meta_title_{{ $locale }}">{{ __('backend.cms.meta_title') }}</label>
        <input type="text" name="translations[{{ $locale }}][meta_title]" id="meta_title_{{ $locale }}" class="form-control"
          value="{{ old("translations.$locale.meta_title", $tr?->meta_title) }}">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label" for="meta_description_{{ $locale }}">{{ __('backend.cms.meta_description') }}</label>
        <textarea name="translations[{{ $locale }}][meta_description]" id="meta_description_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.meta_description", $tr?->meta_description) }}</textarea>
      </div>
    </div>
  @endif

  <h6 class="mb-3">{{ __('backend.cms.pages.home_layout') }}</h6>
  <p class="form-text mt-0">{{ __('backend.cms.pages.home_layout_help') }}</p>

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.feature_section_bg') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][feature_bg_color]" class="form-control" value="{{ $layout['feature_bg_color'] ?? '#F3F1F2' }}">
  </div>

  @foreach ($featureIndexes as $n)
    @php($itemBg = $layout['feature_'.$n.'_bg'] ?? '')
    <div class="border rounded p-3 bg-body mb-3">
      <h6 class="mb-3">{{ __('backend.cms.pages.feature_item', ['n' => $n]) }}</h6>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.title') }}</label>
          <input type="text" name="translations[{{ $locale }}][layout][feature_{{ $n }}_title]" class="form-control" value="{{ $layout['feature_'.$n.'_title'] ?? '' }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.pages.service_item_icon') }}</label>
          <input type="text" name="translations[{{ $locale }}][layout][feature_{{ $n }}_icon]" class="form-control" value="{{ $layout['feature_'.$n.'_icon'] ?? '' }}" placeholder="fa-solid fa-user-secret">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.pages.service_item_bg') }}</label>
          <select name="translations[{{ $locale }}][layout][feature_{{ $n }}_bg]" class="form-select">
            <option value="secondary" @selected($itemBg === 'secondary')>secondary</option>
            <option value="black" @selected($itemBg === 'black')>black</option>
            <option value="primary" @selected($itemBg === 'primary')>primary</option>
          </select>
        </div>
        <div class="col-12 mb-0">
          <label class="form-label">{{ __('backend.cms.content') }}</label>
          <textarea name="translations[{{ $locale }}][layout][feature_{{ $n }}_desc]" class="form-control" rows="3">{{ $layout['feature_'.$n.'_desc'] ?? '' }}</textarea>
        </div>
      </div>
    </div>
  @endforeach

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.feature_bottom_text') }}</label>
    <textarea name="translations[{{ $locale }}][layout][feature_bottom_text]" class="form-control" rows="3">{{ $layout['feature_bottom_text'] ?? '' }}</textarea>
  </div>
  <div class="mb-0">
    <label class="form-label">{{ __('backend.cms.pages.home_video_image') }}</label>
    <input type="file" name="translations[{{ $locale }}][video_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($videoPreview)
      <img src="{{ $videoPreview }}" alt="" class="mt-2" style="max-width:100%;height:90px;object-fit:cover;">
      @if ($videoIsUploaded)
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_video_image]" value="1" id="remove_video_image_{{ $locale }}">
          <label class="form-check-label" for="remove_video_image_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
        </div>
      @endif
    @endif
  </div>
</div>
