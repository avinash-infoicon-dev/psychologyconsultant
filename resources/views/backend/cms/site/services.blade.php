@extends('backend.layouts.app')
@section('title', __('backend.cms.pages.services_edit'))
@section('page_title', __('backend.cms.pages.services_edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_services') }}</li>
@endsection
@section('content')
  @php
    $activeLocale = in_array(app()->getLocale(), $locales, true) ? app()->getLocale() : ($locales[0] ?? 'en');
  @endphp
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.services.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <p class="text-muted">{{ __('backend.cms.pages.services_help') }}</p>

        <ul class="nav nav-tabs" role="tablist">
          @foreach ($locales as $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link @if($locale === $activeLocale) active @endif" type="button" role="tab"
                data-bs-toggle="tab" data-bs-target="#services-locale-{{ $locale }}">
                {{ \App\Support\Locales::label($locale) }}
              </button>
            </li>
          @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3">
          @foreach ($locales as $locale)
            @php($tr = $page->translations->firstWhere('locale', $locale))
            <div class="tab-pane @if($locale === $activeLocale) show active @endif" id="services-locale-{{ $locale }}" role="tabpanel">
              @include('backend.cms.pages._services_layout', ['locale' => $locale, 'tr' => $tr, 'includePageFields' => true])
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
  <template id="service-card-template">
    <div class="border rounded p-3 bg-body mb-3" data-service-card data-index="__N__">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">{{ __('backend.cms.pages.service_item', ['n' => '__N__']) }}</h6>
        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-service-card>
          {{ __('backend.cms.pages.remove_service_card') }}
        </button>
      </div>
      <input type="hidden" name="translations[__LOCALE__][remove_item___N__]" value="0" data-remove-flag>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.title') }}</label>
          <input type="text" name="translations[__LOCALE__][layout][item___N___title]" class="form-control" value="">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.pages.service_item_icon') }}</label>
          <input type="text" name="translations[__LOCALE__][layout][item___N___icon]" class="form-control" value="fa-solid fa-brain">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.pages.service_item_bg') }}</label>
          <select name="translations[__LOCALE__][layout][item___N___bg]" class="form-select">
            <option value="secondary">secondary</option>
            <option value="primary">primary</option>
          </select>
        </div>
        <div class="col-12 mb-3">
          <label class="form-label">{{ __('backend.cms.content') }}</label>
          <textarea name="translations[__LOCALE__][layout][item___N___desc]" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12 mb-0">
          <label class="form-label">{{ __('backend.cms.pages.service_item_image') }}</label>
          <input type="file" name="translations[__LOCALE__][item___N___image]" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
      </div>
    </div>
  </template>
@endsection
@push('scripts')
<script>
  (function () {
    var maxCards = {{ \App\Services\Cms\CmsServicesPageService::MAX_CARDS }};
    var template = document.getElementById('service-card-template');
    if (!template) return;

    var nextIndex = function (root) {
      var max = 0;
      root.querySelectorAll('[data-service-card]').forEach(function (el) {
        var n = parseInt(el.getAttribute('data-index'), 10);
        if (n > max) max = n;
      });
      return max + 1;
    };

    var visibleCount = function (list) {
      return list.querySelectorAll('[data-service-card]:not([hidden])').length;
    };

    document.querySelectorAll('[data-add-service-card]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var index = 0;
        document.querySelectorAll('[data-service-cards-root] [data-service-card]').forEach(function (el) {
          var n = parseInt(el.getAttribute('data-index'), 10);
          if (n > index) index = n;
        });
        index += 1;
        if (index > maxCards) return;
        document.querySelectorAll('[data-service-cards-root]').forEach(function (root) {
          var list = root.querySelector('[data-service-card-list]');
          if (!list || root.querySelector('[data-service-card][data-index="' + index + '"]')) return;
          var html = template.innerHTML
            .replaceAll('__LOCALE__', root.getAttribute('data-locale'))
            .replaceAll('__N__', String(index));
          list.insertAdjacentHTML('beforeend', html);
        });
      });
    });

    document.addEventListener('click', function (event) {
      var btn = event.target.closest('[data-remove-service-card]');
      if (!btn) return;
      var card = btn.closest('[data-service-card]');
      var list = btn.closest('[data-service-card-list]');
      if (!card || !list) return;
      if (visibleCount(list) < 2) return;
      var flag = card.querySelector('[data-remove-flag]');
      if (flag) flag.value = '1';
      card.hidden = true;
      card.querySelectorAll('input, select, textarea').forEach(function (field) {
        if (field === flag) return;
        field.disabled = true;
      });
    });
  })();
</script>
@endpush
