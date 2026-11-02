@php($menuModel = $menu ?? null)
<div class="row mb-3">
  <div class="col-md-3 mb-3">
    <label class="form-label" for="location">{{ __('backend.cms.location') }}</label>
    <select name="location" id="location" class="form-select" required>
      @foreach ($locations as $key => $label)
        <option value="{{ $key }}" @selected(old('location', $menuModel?->location ?? $location) === $key)>
          {{ __('backend.cms.locations.'.$key) }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label" for="parent_id">{{ __('backend.cms.parent') }}</label>
    <select name="parent_id" id="parent_id" class="form-select">
      <option value="">{{ __('backend.cms.no_parent') }}</option>
      @foreach ($parents as $parent)
        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $menuModel?->parent_id) === (string) $parent->id)>{{ $parent->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label" for="type">{{ __('backend.cms.type') }}</label>
    <select name="type" id="type" class="form-select" required>
      @foreach ($types as $key => $label)
        <option value="{{ $key }}" @selected(old('type', $menuModel?->type ?? 'page') === $key)>
          {{ __('backend.cms.types.'.$key) }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $menuModel?->sort_order ?? 0) }}">
  </div>

  <div class="col-md-4 mb-3" data-menu-field="page">
    <label class="form-label" for="reference_page">{{ __('backend.cms.link_page') }}</label>
    <select name="reference_id" id="reference_page" class="form-select">
      <option value="">{{ __('backend.cms.select_page') }}</option>
      @foreach ($pages as $page)
        <option value="{{ $page->id }}" @selected((string) old('reference_id', $menuModel?->type === 'page' ? $menuModel->reference_id : null) === (string) $page->id)>
          {{ $page->title }} ({{ $page->slug }})
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4 mb-3 d-none" data-menu-field="category">
    <label class="form-label" for="reference_category">{{ __('backend.cms.link_category') }}</label>
    <select id="reference_category" class="form-select">
      <option value="">{{ __('backend.cms.select_category') }}</option>
      @foreach ($categories as $cat)
        <option value="{{ $cat->id }}" @selected((string) old('reference_id', $menuModel?->type === 'category' ? $menuModel->reference_id : null) === (string) $cat->id)>
          {{ $cat->name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4 mb-3 d-none" data-menu-field="route">
    <label class="form-label" for="route_name">{{ __('backend.cms.route_name') }}</label>
    <input type="text" name="route_name" id="route_name" class="form-control" value="{{ old('route_name', $menuModel?->route_name) }}" placeholder="frontend.patient.profile.show">
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label" for="url">{{ __('backend.cms.fallback_url') }}</label>
    <input type="text" name="url" id="url" class="form-control" value="{{ old('url', $menuModel?->url) }}" placeholder="/">
    <div class="form-text">{{ __('backend.cms.fallback_url_help') }}</div>
  </div>

  <div class="col-md-2 mb-3">
    <label class="form-label" for="target">{{ __('backend.cms.target') }}</label>
    <select name="target" id="target" class="form-select">
      <option value="_self" @selected(old('target', $menuModel?->target ?? '_self') === '_self')>_self</option>
      <option value="_blank" @selected(old('target', $menuModel?->target) === '_blank')>_blank</option>
    </select>
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="icon">{{ __('backend.cms.icon') }}</label>
    <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', $menuModel?->icon) }}" placeholder="bi bi-house">
  </div>
  <div class="col-md-4 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $menuModel?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#menu-locale-{{ $locale }}" type="button" role="tab">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3 mb-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $menuModel?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="menu-locale-{{ $locale }}" role="tabpanel">
      <label class="form-label" for="menu_title_{{ $locale }}">{{ __('backend.cms.title') }}</label>
      <input type="text" name="translations[{{ $locale }}][title]" id="menu_title_{{ $locale }}" class="form-control"
        value="{{ old("translations.$locale.title", $tr?->title) }}" required>
    </div>
  @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const typeSelect = document.getElementById('type');
  const pageSelect = document.getElementById('reference_page');
  const categorySelect = document.getElementById('reference_category');

  function syncFields() {
    const type = typeSelect.value;
    document.querySelectorAll('[data-menu-field]').forEach(el => {
      el.classList.toggle('d-none', el.getAttribute('data-menu-field') !== type);
    });

    if (type === 'page') {
      pageSelect.setAttribute('name', 'reference_id');
      categorySelect.removeAttribute('name');
    } else if (type === 'category') {
      categorySelect.setAttribute('name', 'reference_id');
      pageSelect.removeAttribute('name');
    } else {
      pageSelect.removeAttribute('name');
      categorySelect.removeAttribute('name');
    }
  }

  typeSelect.addEventListener('change', syncFields);
  syncFields();
});
</script>
@endpush
