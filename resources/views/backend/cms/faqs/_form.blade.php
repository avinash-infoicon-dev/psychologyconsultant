@php($item = $faq ?? null)
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
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#faq-locale-{{ $locale }}" type="button">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $item?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="faq-locale-{{ $locale }}">
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.faqs.question') }}</label>
        <input type="text" name="translations[{{ $locale }}][question]" class="form-control" required
          value="{{ old("translations.$locale.question", $tr?->question) }}">
      </div>
      <div class="mb-0">
        <label class="form-label">{{ __('backend.cms.faqs.answer') }}</label>
        <textarea name="translations[{{ $locale }}][answer]" class="form-control" rows="4" required>{{ old("translations.$locale.answer", $tr?->answer) }}</textarea>
      </div>
    </div>
  @endforeach
</div>
