@php($categoryModel = $category ?? null)
<div class="row mb-3">
  <div class="col-md-6 mb-3">
    <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $categoryModel?->slug) }}">
    <div class="form-text">{{ __('backend.cms.slug_help') }}</div>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $categoryModel?->sort_order ?? 0) }}">
  </div>
  <div class="col-md-3 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $categoryModel?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#blog-cat-locale-{{ $locale }}" type="button" role="tab">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3 mb-0">
  @foreach ($locales as $i => $locale)
    @php($tr = $categoryModel?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="blog-cat-locale-{{ $locale }}" role="tabpanel">
      <div class="mb-3">
        <label class="form-label">{{ __('backend.name') }}</label>
        <input type="text" name="translations[{{ $locale }}][name]" class="form-control"
          value="{{ old("translations.$locale.name", $tr?->name) }}" required>
      </div>
      <div class="mb-0">
        <label class="form-label">{{ __('backend.description') }}</label>
        <textarea name="translations[{{ $locale }}][description]" class="form-control" rows="3">{{ old("translations.$locale.description", $tr?->description) }}</textarea>
      </div>
    </div>
  @endforeach
</div>
