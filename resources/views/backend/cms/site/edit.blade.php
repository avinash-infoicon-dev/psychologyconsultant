@extends('backend.layouts.app')
@section('title', __('backend.cms.site.edit'))
@section('page_title', __('backend.cms.site.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_site') }}</li>
@endsection
@section('content')
  @php
    $activeLocale = in_array(app()->getLocale(), $locales, true) ? app()->getLocale() : ($locales[0] ?? 'en');
  @endphp
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.site.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <p class="text-muted">{{ __('backend.cms.site.help') }}</p>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="phone">{{ __('backend.cms.pages.phone') }}</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $site->phone) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="email">{{ __('backend.cms.pages.email') }}</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $site->email) }}">
          </div>
        </div>

        <ul class="nav nav-tabs" role="tablist">
          @foreach ($locales as $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link @if($locale === $activeLocale) active @endif" id="site-tab-{{ $locale }}"
                data-bs-toggle="tab" data-bs-target="#site-locale-{{ $locale }}" type="button" role="tab">
                {{ \App\Support\Locales::label($locale) }}
              </button>
            </li>
          @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3">
          @foreach ($locales as $locale)
            @php
              $tr = $site->translations->firstWhere('locale', $locale);
              $logoPreview = \App\Support\PublicMedia::url($tr?->logo_path ?: $site->logo_path);
              $logoDarkPreview = \App\Support\PublicMedia::url($tr?->logo_dark_path ?: $site->logo_dark_path);
              $footerLogoPreview = \App\Support\PublicMedia::url($tr?->footer_logo_path ?: $site->footer_logo_path ?: $tr?->logo_path ?: $site->logo_path);
              $footerBgPreview = \App\Support\PublicMedia::url($tr?->footer_background_path ?: $site->footer_background_path);
            @endphp
            <div class="tab-pane @if($locale === $activeLocale) show active @endif" id="site-locale-{{ $locale }}" role="tabpanel">
              <h6 class="mb-3">{{ __('backend.cms.site.header_section') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="logo_{{ $locale }}">{{ __('backend.cms.site.logo') }}</label>
                  <input type="file" name="translations[{{ $locale }}][logo]" id="logo_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  <div class="form-text">{{ __('backend.cms.site.logo_help') }}</div>
                  @if ($logoPreview)
                    <div class="mt-2 d-flex align-items-center gap-2">
                      <img src="{{ $logoPreview }}" alt="" style="max-height:48px;background:#111;padding:6px;border-radius:6px;">
                      @if ($tr?->logo_path)
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_logo]" value="1" id="remove_logo_{{ $locale }}">
                          <label class="form-check-label" for="remove_logo_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="logo_dark_{{ $locale }}">{{ __('backend.cms.site.logo_dark') }}</label>
                  <input type="file" name="translations[{{ $locale }}][logo_dark]" id="logo_dark_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  <div class="form-text">{{ __('backend.cms.site.logo_dark_help') }}</div>
                  @if ($logoDarkPreview)
                    <div class="mt-2 d-flex align-items-center gap-2">
                      <img src="{{ $logoDarkPreview }}" alt="" style="max-height:48px;background:#f5f5f5;padding:6px;border-radius:6px;">
                      @if ($tr?->logo_dark_path)
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_logo_dark]" value="1" id="remove_logo_dark_{{ $locale }}">
                          <label class="form-check-label" for="remove_logo_dark_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
              </div>

              <h6 class="mb-3 mt-2">{{ __('backend.cms.site.footer_section') }}</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="footer_logo_{{ $locale }}">{{ __('backend.cms.site.footer_logo') }}</label>
                  <input type="file" name="translations[{{ $locale }}][footer_logo]" id="footer_logo_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  <div class="form-text">{{ __('backend.cms.site.footer_logo_help') }}</div>
                  @if ($footerLogoPreview)
                    <div class="mt-2 d-flex align-items-center gap-2">
                      <img src="{{ $footerLogoPreview }}" alt="" style="max-height:48px;background:#111;padding:6px;border-radius:6px;">
                      @if ($tr?->footer_logo_path)
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_footer_logo]" value="1" id="remove_footer_logo_{{ $locale }}">
                          <label class="form-check-label" for="remove_footer_logo_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="footer_background_{{ $locale }}">{{ __('backend.cms.site.footer_background') }}</label>
                  <input type="file" name="translations[{{ $locale }}][footer_background]" id="footer_background_{{ $locale }}" class="form-control" accept="image/jpeg,image/png,image/webp">
                  @if ($footerBgPreview)
                    <div class="mt-2 d-flex align-items-center gap-2">
                      <img src="{{ $footerBgPreview }}" alt="" style="max-height:48px;object-fit:cover;border-radius:6px;">
                      @if ($tr?->footer_background_path)
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" name="translations[{{ $locale }}][remove_footer_background]" value="1" id="remove_footer_background_{{ $locale }}">
                          <label class="form-check-label" for="remove_footer_background_{{ $locale }}">{{ __('backend.cms.pages.remove_image') }}</label>
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="brand_{{ $locale }}">{{ __('backend.cms.site.brand') }}</label>
                  <input type="text" name="translations[{{ $locale }}][brand]" id="brand_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.brand", $tr?->brand) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="tagline_{{ $locale }}">{{ __('backend.cms.site.tagline') }}</label>
                  <input type="text" name="translations[{{ $locale }}][tagline]" id="tagline_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.tagline", $tr?->tagline) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="offcanvas_info_title_{{ $locale }}">{{ __('backend.cms.site.offcanvas_info_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][offcanvas_info_title]" id="offcanvas_info_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.offcanvas_info_title", $tr?->offcanvas_info_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="availability_{{ $locale }}">{{ __('backend.cms.site.availability') }}</label>
                  <input type="text" name="translations[{{ $locale }}][availability]" id="availability_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.availability", $tr?->availability) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="footer_links_title_{{ $locale }}">{{ __('backend.cms.site.footer_links_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][footer_links_title]" id="footer_links_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.footer_links_title", $tr?->footer_links_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="help_title_{{ $locale }}">{{ __('backend.cms.site.help_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][help_title]" id="help_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.help_title", $tr?->help_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="help_phone_label_{{ $locale }}">{{ __('backend.cms.site.help_phone_label') }}</label>
                  <input type="text" name="translations[{{ $locale }}][help_phone_label]" id="help_phone_label_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.help_phone_label", $tr?->help_phone_label) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="help_email_label_{{ $locale }}">{{ __('backend.cms.site.help_email_label') }}</label>
                  <input type="text" name="translations[{{ $locale }}][help_email_label]" id="help_email_label_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.help_email_label", $tr?->help_email_label) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="newsletter_title_{{ $locale }}">{{ __('backend.cms.site.newsletter_title') }}</label>
                  <input type="text" name="translations[{{ $locale }}][newsletter_title]" id="newsletter_title_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.newsletter_title", $tr?->newsletter_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label" for="copyright_{{ $locale }}">{{ __('backend.cms.site.copyright') }}</label>
                  <input type="text" name="translations[{{ $locale }}][copyright]" id="copyright_{{ $locale }}" class="form-control"
                    value="{{ old("translations.$locale.copyright", $tr?->copyright) }}">
                  <div class="form-text">{{ __('backend.cms.site.copyright_help') }}</div>
                </div>
                <div class="col-12 mb-0">
                  <label class="form-label" for="newsletter_text_{{ $locale }}">{{ __('backend.cms.site.newsletter_text') }}</label>
                  <textarea name="translations[{{ $locale }}][newsletter_text]" id="newsletter_text_{{ $locale }}" class="form-control" rows="2">{{ old("translations.$locale.newsletter_text", $tr?->newsletter_text) }}</textarea>
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
