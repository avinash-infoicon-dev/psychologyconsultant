@extends('backend.layouts.app')
@section('title', __('backend.cms.site.register_edit'))
@section('page_title', __('backend.cms.site.register_edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.site.edit') }}">{{ __('backend.menu.cms_site') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_register') }}</li>
@endsection
@section('content')
  @php
    $activeLocale = in_array(app()->getLocale(), $locales, true) ? app()->getLocale() : ($locales[0] ?? 'en');
  @endphp
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.register.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <p class="text-muted">{{ __('backend.cms.site.register_help') }}</p>

        <ul class="nav nav-tabs" role="tablist">
          @foreach ($locales as $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link @if($locale === $activeLocale) active @endif" type="button" role="tab"
                data-bs-toggle="tab" data-bs-target="#register-locale-{{ $locale }}">
                {{ \App\Support\Locales::label($locale) }}
              </button>
            </li>
          @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3">
          @foreach ($locales as $locale)
            @php($tr = $site->translations->firstWhere('locale', $locale))
            <div class="tab-pane @if($locale === $activeLocale) show active @endif" id="register-locale-{{ $locale }}" role="tabpanel">
              <h6 class="mb-3">{{ __('backend.cms.site.register_banner') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_hero_subtitle_{{ $locale }}">{{ __('backend.cms.site.register_hero_subtitle') }}</label>
                  <input type="text" name="translations[{{ $locale }}][register_hero_subtitle]" id="register_hero_subtitle_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.register_hero_subtitle", $tr?->register_hero_subtitle) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_hero_title_{{ $locale }}">{{ __('backend.cms.site.register_hero_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][register_hero_title]" id="register_hero_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.register_hero_title", $tr?->register_hero_title) }}">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="register_hero_text_{{ $locale }}">{{ __('backend.cms.site.register_hero_text') }}</label>
                  <textarea name="translations[{{ $locale }}][register_hero_text]" id="register_hero_text_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.register_hero_text", $tr?->register_hero_text) }}</textarea>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="register_hero_{{ $locale }}">{{ __('backend.cms.pages.hero_background') }}</label>
                  <input type="file" name="translations[{{ $locale }}][register_hero]" id="register_hero_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  @php($heroPreview = \App\Support\PublicMedia::url($tr?->register_hero_path) ?: \App\Support\PublicMedia::url(\App\Support\PublicMedia::aboutDefault('hero-bg.webp')))
                  @if ($heroPreview)
                    <img src="{{ $heroPreview }}" alt="" class="mt-2" style="max-width:100%;height:70px;object-fit:cover;">
                    @if ($tr?->register_hero_path)
                      <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_register_hero]" value="1" id="remove_register_hero_{{ $locale }}">
                        <label class="form-check-label" for="remove_register_hero_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                      </div>
                    @endif
                  @endif
                </div>
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.register_section') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_kicker_{{ $locale }}">{{ __('backend.cms.site.register_kicker') }}</label>
                  <input type="text" name="translations[{{ $locale }}][register_kicker]" id="register_kicker_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.register_kicker", $tr?->register_kicker) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_heading_{{ $locale }}">{{ __('backend.cms.site.register_heading') }}</label>
                  <input type="text" name="translations[{{ $locale }}][register_heading]" id="register_heading_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.register_heading", $tr?->register_heading) }}">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label" for="register_intro_{{ $locale }}">{{ __('backend.cms.site.register_intro') }}</label>
                  <textarea name="translations[{{ $locale }}][register_intro]" id="register_intro_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.register_intro", $tr?->register_intro) }}</textarea>
                </div>
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.register_form') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_form_title_{{ $locale }}">{{ __('backend.cms.site.register_form_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][register_form_title]" id="register_form_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.register_form_title", $tr?->register_form_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="register_form_subtitle_{{ $locale }}">{{ __('backend.cms.site.register_form_subtitle') }}</label>
                  <textarea name="translations[{{ $locale }}][register_form_subtitle]" id="register_form_subtitle_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.register_form_subtitle", $tr?->register_form_subtitle) }}</textarea>
                </div>
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.psych_wizard') }}</h6>
              <p class="text-muted small">{{ __('backend.cms.site.psych_wizard_help') }}</p>
              @php($psych = old("translations.$locale.psych", $tr?->register_psychologist_copy ?? []))
              <div class="row">
                @foreach ([1, 2, 3, 4, 5, 6, 7] as $n)
                  <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('backend.cms.site.psych_step', ['n' => $n]) }}</label>
                    <input type="text" name="translations[{{ $locale }}][psych][step_{{ $n }}]" class="form-control"
                      value="{{ $psych['step_'.$n] ?? '' }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('backend.cms.site.psych_panel_title', ['n' => $n]) }}</label>
                    <input type="text" name="translations[{{ $locale }}][psych][panel_{{ $n }}_title]" class="form-control"
                      value="{{ $psych['panel_'.$n.'_title'] ?? '' }}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('backend.cms.site.psych_panel_text', ['n' => $n]) }}</label>
                    <input type="text" name="translations[{{ $locale }}][psych][panel_{{ $n }}_text]" class="form-control"
                      value="{{ $psych['panel_'.$n.'_text'] ?? '' }}">
                  </div>
                @endforeach
                @foreach ([
                    'add_education' => 'psych_add_education',
                    'add_experience' => 'psych_add_experience',
                    'add_social' => 'psych_add_social',
                    'add_gallery' => 'psych_add_gallery',
                    'add_document' => 'psych_add_document',
                    'add_award' => 'psych_add_award',
                    'btn_previous' => 'psych_btn_previous',
                    'btn_continue' => 'psych_btn_continue',
                    'btn_save_draft' => 'psych_btn_save_draft',
                    'btn_submit_review' => 'psych_btn_submit_review',
                    'btn_remove' => 'psych_btn_remove',
                ] as $key => $langKey)
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="psych_{{ $key }}_{{ $locale }}">{{ __('backend.cms.site.'.$langKey) }}</label>
                    <input type="text" name="translations[{{ $locale }}][psych][{{ $key }}]" id="psych_{{ $key }}_{{ $locale }}" class="form-control"
                      value="{{ $psych[$key] ?? '' }}">
                  </div>
                @endforeach
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
