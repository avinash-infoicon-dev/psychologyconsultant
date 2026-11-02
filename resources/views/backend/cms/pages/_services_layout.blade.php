@php
  $layout = old("translations.$locale.layout", $tr?->layout ?? []);
  $layout = is_array($layout) ? $layout : [];
  $layoutUrl = function (string $key, ?string $defaultFile = null) use ($layout) {
      return \App\Support\PublicMedia::url($layout[$key] ?? null)
          ?: ($defaultFile ? asset('frontendassets/assets/img/blog/'.$defaultFile) : null);
  };
  $isUploaded = function (string $key) use ($layout) {
      $path = $layout[$key] ?? null;

      return is_string($path) && str_starts_with($path, 'cms/pages/');
  };
  $includePageFields = $includePageFields ?? false;
  $serviceCards = \App\Services\Cms\CmsServicesPageService::cardIndexes($layout);
  $heroPreview = \App\Support\PublicMedia::url($layout['hero_background'] ?? null)
      ?: \App\Support\PublicMedia::url(\App\Support\PublicMedia::aboutDefault('hero-bg.webp'));
  $heroIsUploaded = $isUploaded('hero_background');
@endphp
<div class="border rounded p-3 bg-body-secondary" data-service-cards-root data-locale="{{ $locale }}">
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

  <h6 class="mb-3">{{ __('backend.cms.pages.services_layout') }}</h6>
  <p class="form-text mt-0">{{ __('backend.cms.pages.services_layout_help') }}</p>

  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.hero_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][hero_subtitle]" class="form-control" value="{{ $layout['hero_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.hero_background') }}</label>
    <input type="file" name="translations[{{ $locale }}][hero_background]" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($heroPreview)
      <img src="{{ $heroPreview }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
      @if ($heroIsUploaded)
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_hero_background]" value="1" id="remove_services_hero_{{ $locale }}">
          <label class="form-check-label" for="remove_services_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
        </div>
      @endif
    @endif
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.services_section_subtitle') }}</label>
    <input type="text" name="translations[{{ $locale }}][layout][section_subtitle]" class="form-control" value="{{ $layout['section_subtitle'] ?? '' }}">
  </div>
  <div class="mb-3">
    <label class="form-label">{{ __('backend.cms.pages.services_section_title') }}</label>
    <textarea name="translations[{{ $locale }}][layout][section_title]" class="form-control" rows="3">{{ $layout['section_title'] ?? '' }}</textarea>
  </div>

  <div data-service-card-list>
    @foreach ($serviceCards as $n)
      @php
        $defaultThumb = $n === 2 ? 'blog-3.webp' : 'blog-1.webp';
        $itemImageKey = 'item_'.$n.'_image';
        $itemImagePreview = $layoutUrl($itemImageKey, $defaultThumb);
        $itemBg = $layout['item_'.$n.'_bg'] ?? '';
        $itemIsUploaded = $isUploaded($itemImageKey);
      @endphp
      <div class="border rounded p-3 bg-body mb-3" data-service-card data-index="{{ $n }}">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">{{ __('backend.cms.pages.service_item', ['n' => $n]) }}</h6>
          <button type="button" class="btn btn-sm btn-outline-danger" data-remove-service-card>
            {{ __('backend.cms.pages.remove_service_card') }}
          </button>
        </div>
        <input type="hidden" name="translations[{{ $locale }}][remove_item_{{ $n }}]" value="0" data-remove-flag>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('backend.cms.title') }}</label>
            <input type="text" name="translations[{{ $locale }}][layout][item_{{ $n }}_title]" class="form-control" value="{{ $layout['item_'.$n.'_title'] ?? '' }}">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">{{ __('backend.cms.pages.service_item_icon') }}</label>
            <input type="text" name="translations[{{ $locale }}][layout][item_{{ $n }}_icon]" class="form-control" value="{{ $layout['item_'.$n.'_icon'] ?? '' }}" placeholder="fa-solid fa-brain">
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">{{ __('backend.cms.pages.service_item_bg') }}</label>
            <select name="translations[{{ $locale }}][layout][item_{{ $n }}_bg]" class="form-select">
              <option value="secondary" @selected($itemBg === 'secondary')>secondary</option>
              <option value="primary" @selected($itemBg === 'primary')>primary</option>
            </select>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">{{ __('backend.cms.content') }}</label>
            <textarea name="translations[{{ $locale }}][layout][item_{{ $n }}_desc]" class="form-control" rows="3">{{ $layout['item_'.$n.'_desc'] ?? '' }}</textarea>
          </div>
          <div class="col-12 mb-0">
            <label class="form-label">{{ __('backend.cms.pages.service_item_image') }}</label>
            <input type="file" name="translations[{{ $locale }}][item_{{ $n }}_image]" class="form-control" accept="image/jpeg,image/png,image/webp">
            @if ($itemImagePreview)
              <img src="{{ $itemImagePreview }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
              @if ($itemIsUploaded)
                <div class="form-check mt-1">
                  <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_item_{{ $n }}_image]" value="1" id="remove_item_{{ $n }}_image_{{ $locale }}">
                  <label class="form-check-label" for="remove_item_{{ $n }}_image_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                </div>
              @endif
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <button type="button" class="btn btn-outline-primary" data-add-service-card>
    {{ __('backend.cms.pages.add_service_card') }}
  </button>
</div>
