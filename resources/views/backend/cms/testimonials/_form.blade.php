@php($item = $testimonial ?? null)
<div class="row mb-3">
  <div class="col-md-3 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
  </div>
  <div class="col-md-3 mb-3 d-flex align-items-end">
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="photo">{{ __('backend.cms.testimonials.photo') }}</label>
    <input type="file" name="photo" id="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($item?->photo_url)
      <div class="mt-2 d-flex align-items-center gap-2">
        <img src="{{ $item->photo_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:50%;">
        <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove_photo">
          <label class="form-check-label" for="remove_photo">{{ __('backend.cms.testimonials.remove_photo') }}</label>
        </div>
      </div>
    @endif
  </div>
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#testimonial-locale-{{ $locale }}" type="button">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $item?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="testimonial-locale-{{ $locale }}">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.testimonials.author') }}</label>
          <input type="text" name="translations[{{ $locale }}][author_name]" class="form-control" required
            value="{{ old("translations.$locale.author_name", $tr?->author_name) }}">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.testimonials.role') }}</label>
          <input type="text" name="translations[{{ $locale }}][author_role]" class="form-control"
            value="{{ old("translations.$locale.author_role", $tr?->author_role) }}">
        </div>
      </div>
      <div class="mb-0">
        <label class="form-label">{{ __('backend.cms.testimonials.quote') }}</label>
        <textarea name="translations[{{ $locale }}][quote]" class="form-control" rows="4" required>{{ old("translations.$locale.quote", $tr?->quote) }}</textarea>
      </div>
    </div>
  @endforeach
</div>
