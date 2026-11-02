@php
  $locales = $locales ?? \App\Support\Locales::codes();
  $hasTranslations = (bool) $specialization?->translations?->isNotEmpty();
@endphp

{{-- Legacy single-language name / description:
<div class="mb-3">
  <label class="form-label" for="name">{{ __('backend.name') }}</label>
  <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $specialization->name) }}" required maxlength="255">
</div>
<div class="mb-3">
  <label class="form-label" for="description">{{ __('backend.description') }}</label>
  <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $specialization->description) }}</textarea>
</div>
--}}

<div class="mb-3">
  <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
  <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $specialization->slug) }}" maxlength="255">
  <div class="form-text">{{ __('backend.specializations.slug_help') }}</div>
</div>

<div class="mb-3">
  <label class="form-label">{{ __('backend.specializations.content_translations') }}</label>
  <div class="form-text mb-2">{{ __('backend.specializations.content_translations_help') }}</div>
  <ul class="nav nav-tabs" role="tablist">
    @foreach ($locales as $i => $locale)
      <li class="nav-item" role="presentation">
        <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#specialization-locale-{{ $locale }}" type="button" role="tab">
          {{ \App\Support\Locales::label($locale) }}
        </button>
      </li>
    @endforeach
  </ul>
  <div class="tab-content border border-top-0 p-3">
    @foreach ($locales as $i => $locale)
      @php
        $tr = $specialization?->translations?->firstWhere('locale', $locale);
        $fallbackName = $hasTranslations ? $tr?->name : $specialization?->getRawOriginal('name');
        $fallbackDescription = $hasTranslations ? $tr?->description : $specialization?->getRawOriginal('description');
      @endphp
      <div class="tab-pane fade @if($i === 0) show active @endif" id="specialization-locale-{{ $locale }}" role="tabpanel">
        <div class="mb-3">
          <label class="form-label" for="name_{{ $locale }}">{{ __('backend.name') }}</label>
          <input type="text" name="translations[{{ $locale }}][name]" id="name_{{ $locale }}" class="form-control"
            value="{{ old("translations.$locale.name", $fallbackName) }}" required maxlength="255">
        </div>
        <div class="mb-0">
          <label class="form-label" for="description_{{ $locale }}">{{ __('backend.description') }}</label>
          <textarea name="translations[{{ $locale }}][description]" id="description_{{ $locale }}" class="form-control" rows="4">{{ old("translations.$locale.description", $fallbackDescription) }}</textarea>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" name="status" value="1" id="status"
    @checked(old('status', $specialization->status))
    @disabled($specialization->isArchived())>
  <label class="form-check-label" for="status">{{ __('backend.active') }}</label>
  @if ($specialization->isArchived())
    <div class="form-text">{{ __('backend.specializations.archived_help') }}</div>
  @endif
</div>
