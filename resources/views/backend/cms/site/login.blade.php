@extends('backend.layouts.app')
@section('title', __('backend.cms.site.login_edit'))
@section('page_title', __('backend.cms.site.login_edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.site.edit') }}">{{ __('backend.menu.cms_site') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_login') }}</li>
@endsection
@section('content')
  @php
    $activeLocale = in_array(app()->getLocale(), $locales, true) ? app()->getLocale() : ($locales[0] ?? 'en');
  @endphp
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.login.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <p class="text-muted">{{ __('backend.cms.site.login_help') }}</p>

        <ul class="nav nav-tabs" role="tablist">
          @foreach ($locales as $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link @if($locale === $activeLocale) active @endif" type="button" role="tab"
                data-bs-toggle="tab" data-bs-target="#login-locale-{{ $locale }}">
                {{ \App\Support\Locales::label($locale) }}
              </button>
            </li>
          @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3">
          @foreach ($locales as $locale)
            @php($tr = $site->translations->firstWhere('locale', $locale))
            <div class="tab-pane @if($locale === $activeLocale) show active @endif" id="login-locale-{{ $locale }}" role="tabpanel">
              <h6 class="mb-3">{{ __('backend.cms.site.login_banner') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_hero_subtitle_{{ $locale }}">{{ __('backend.cms.site.login_hero_subtitle') }}</label>
                  <input type="text" name="translations[{{ $locale }}][login_hero_subtitle]" id="login_hero_subtitle_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.login_hero_subtitle", $tr?->login_hero_subtitle) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_hero_title_{{ $locale }}">{{ __('backend.cms.site.login_hero_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][login_hero_title]" id="login_hero_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.login_hero_title", $tr?->login_hero_title) }}">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="login_hero_text_{{ $locale }}">{{ __('backend.cms.site.login_hero_text') }}</label>
                  <textarea name="translations[{{ $locale }}][login_hero_text]" id="login_hero_text_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.login_hero_text", $tr?->login_hero_text) }}</textarea>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="login_hero_{{ $locale }}">{{ __('backend.cms.pages.hero_background') }}</label>
                  <input type="file" name="translations[{{ $locale }}][login_hero]" id="login_hero_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  @php($heroPreview = \App\Support\PublicMedia::url($tr?->login_hero_path) ?: \App\Support\PublicMedia::url(\App\Support\PublicMedia::aboutDefault('hero-bg.webp')))
                  @if ($heroPreview)
                    <img src="{{ $heroPreview }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
                    @if ($tr?->login_hero_path)
                      <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_login_hero]" value="1" id="remove_login_hero_{{ $locale }}">
                        <label class="form-check-label" for="remove_login_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                      </div>
                    @endif
                  @endif
                </div>
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.login_visual') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_kicker_{{ $locale }}">{{ __('backend.cms.site.login_kicker') }}</label>
                  <input type="text" name="translations[{{ $locale }}][login_kicker]" id="login_kicker_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.login_kicker", $tr?->login_kicker) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_visual_title_{{ $locale }}">{{ __('backend.cms.site.login_visual_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][login_visual_title]" id="login_visual_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.login_visual_title", $tr?->login_visual_title) }}">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="login_visual_text_{{ $locale }}">{{ __('backend.cms.site.login_visual_text') }}</label>
                  <textarea name="translations[{{ $locale }}][login_visual_text]" id="login_visual_text_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.login_visual_text", $tr?->login_visual_text) }}</textarea>
                </div>
                @foreach ([1, 2, 3, 4] as $n)
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="login_point_{{ $n }}_{{ $locale }}">{{ __('backend.cms.site.login_point', ['n' => $n]) }}</label>
                    <input type="text" name="translations[{{ $locale }}][login_point_{{ $n }}]" id="login_point_{{ $n }}_{{ $locale }}" class="form-control"
                      value="{{ old("translations.$locale.login_point_$n", $tr?->{'login_point_'.$n}) }}">
                  </div>
                @endforeach
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.login_form') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_form_title_{{ $locale }}">{{ __('backend.cms.site.login_form_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][login_form_title]" id="login_form_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.login_form_title", $tr?->login_form_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="login_form_subtitle_{{ $locale }}">{{ __('backend.cms.site.login_form_subtitle') }}</label>
                  <textarea name="translations[{{ $locale }}][login_form_subtitle]" id="login_form_subtitle_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.login_form_subtitle", $tr?->login_form_subtitle) }}</textarea>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
