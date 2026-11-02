@php($pageModel = $page ?? null)
<div class="row mb-3">
  <div class="col-md-4 mb-3">
    <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $pageModel?->slug) }}">
    <div class="form-text">{{ __('backend.cms.slug_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="cms_category_id">{{ __('backend.cms.category') }}</label>
    <select name="cms_category_id" id="cms_category_id" class="form-select">
      <option value="">{{ __('backend.cms.no_category') }}</option>
      @foreach ($categories as $cat)
        <option value="{{ $cat->id }}" @selected((string) old('cms_category_id', $pageModel?->cms_category_id) === (string) $cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="template">{{ __('backend.cms.template') }}</label>
    <select name="template" id="template" class="form-select">
      <option value="default" @selected(old('template', $pageModel?->template ?? 'default') === 'default')>{{ __('backend.cms.templates.default') }}</option>
      <option value="home" @selected(old('template', $pageModel?->template) === 'home')>{{ __('backend.cms.templates.home') }}</option>
      <option value="about" @selected(old('template', $pageModel?->template) === 'about')>{{ __('backend.cms.templates.about') }}</option>
      <option value="faq" @selected(old('template', $pageModel?->template) === 'faq')>{{ __('backend.cms.templates.faq') }}</option>
      <option value="blog" @selected(old('template', $pageModel?->template) === 'blog')>{{ __('backend.cms.templates.blog') }}</option>
      <option value="contact" @selected(old('template', $pageModel?->template) === 'contact')>{{ __('backend.cms.templates.contact') }}</option>
      <option value="services" @selected(old('template', $pageModel?->template) === 'services')>{{ __('backend.cms.templates.services') }}</option>
    </select>
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $pageModel?->sort_order ?? 0) }}">
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="published_at">{{ __('backend.cms.published_at') }}</label>
    <input type="datetime-local" name="published_at" id="published_at" class="form-control"
      value="{{ old('published_at', optional($pageModel?->published_at)->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="col-12 mb-0">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $pageModel?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#page-locale-{{ $locale }}" type="button" role="tab">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $pageModel?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="page-locale-{{ $locale }}" role="tabpanel">
      <div class="mb-3">
        <label class="form-label" for="title_{{ $locale }}">{{ __('backend.cms.title') }}</label>
        <input type="text" name="translations[{{ $locale }}][title]" id="title_{{ $locale }}" class="form-control"
          value="{{ old("translations.$locale.title", $tr?->title) }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="content_{{ $locale }}">{{ __('backend.cms.content') }}</label>
        <textarea name="translations[{{ $locale }}][content]" id="content_{{ $locale }}" class="form-control" rows="8">{{ old("translations.$locale.content", $tr?->content) }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="pdf_{{ $locale }}">{{ __('backend.cms.pages.pdf') }}</label>
        <input type="file" name="translations[{{ $locale }}][pdf]" id="pdf_{{ $locale }}" class="form-control" accept="application/pdf,.pdf">
        <div class="form-text">{{ __('backend.cms.pages.pdf_help') }}</div>
        @if ($tr?->pdf_path)
          <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
            <a href="{{ $tr->pdf_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
              {{ $tr->pdf_original_name ?: __('backend.cms.pages.current_pdf') }}
            </a>
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_pdf]" value="1" id="remove_pdf_{{ $locale }}">
              <label class="form-check-label" for="remove_pdf_{{ $locale }}">{{ __('backend.cms.pages.remove_pdf') }}</label>
            </div>
          </div>
        @endif
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
      <div class="cms-template-panel" data-template="default">
        @include('backend.cms.pages._default_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="home">
        @include('backend.cms.pages._home_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="about">
        @include('backend.cms.pages._about_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="faq">
        @include('backend.cms.pages._faq_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="blog">
        @include('backend.cms.pages._blog_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="contact">
        @include('backend.cms.pages._contact_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
      <div class="cms-template-panel" data-template="services">
        @include('backend.cms.pages._services_layout', ['locale' => $locale, 'tr' => $tr])
      </div>
    </div>
  @endforeach
</div>
<script>
  (function () {
    var select = document.getElementById('template');
    if (!select) return;
    var sync = function () {
      document.querySelectorAll('.cms-template-panel').forEach(function (el) {
        el.style.display = el.getAttribute('data-template') === select.value ? '' : 'none';
      });
    };
    select.addEventListener('change', sync);
    sync();
  })();
</script>
