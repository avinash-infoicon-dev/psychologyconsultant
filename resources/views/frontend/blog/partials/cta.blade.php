@php
  $ctaImg = ($page ?? null)?->layoutUrl('cta_image', \App\Support\PublicMedia::aboutDefault('cta-thumb-1.webp'))
      ?: asset('frontendassets/assets/img/thumb-1.webp');
  $ctaSubtitle = ($page ?? null)?->layout('cta_subtitle') ?: __('frontend.home.cta_subtitle');
  $ctaTitle = ($page ?? null)?->layout('cta_title') ?: __('frontend.home.cta_title');
  $ctaText = ($page ?? null)?->layout('cta_text') ?: __('frontend.home.cta_desc');
@endphp
<div class="cst-cta-ptb">
    <div class="container container-1530">
        <div class="row">
            <div class="col-lg-12">
                <div class="cst-cta-wrapper">
                    <div class="cst-cta-thumb">
                        <img src="{{ $ctaImg }}" alt="">
                    </div>
                    <div class="cst-cta-content">
                        @if ($ctaSubtitle)
                          <span class="cst-section-subtitle mb-10">{{ $ctaSubtitle }}</span>
                        @endif
                        @if ($ctaTitle)
                          <h4 class="cst-section-title mb-15">{{ $ctaTitle }}</h4>
                        @endif
                        @if ($ctaText)
                          <p class="cst-cta-content-text">{{ $ctaText }}</p>
                        @endif
                        <div class="cst-cta-input-box">
                            {{-- Previous form posted to the current page and did not persist (kept for reference)
                            <form id="contact-form" action="{{ url()->current() }}" method="POST">
                                @csrf
                            --}}
                            <form id="contact-form" action="{{ route('frontend.contact-inquiries.store') }}" method="POST" data-native-submit="1">
                                @csrf
                                <input type="hidden" name="source" value="cta">
                                <input type="hidden" name="page_url" value="{{ url()->current() }}">
                                @if (session('contact_inquiry_status'))
                                  <p class="mb-20" style="color:#0a7a32;">{{ session('contact_inquiry_status') }}</p>
                                @endif
                                @if ($errors->any())
                                  <p class="mb-20" style="color:#c0392b;">{{ $errors->first() }}</p>
                                @endif
                                <div class="row gx-12">
                                    <div class="col-md-6">
                                        <div class="tp-contact-form-input mb-20">
                                            <label>{{ __('frontend.home.form_name') }}</label>
                                            <input name="name" type="text" value="{{ old('name') }}" placeholder="{{ __('frontend.home.form_name_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="tp-contact-form-input mb-20">
                                            <label>{{ __('frontend.home.form_email') }}</label>
                                            <input name="email" type="email" value="{{ old('email') }}" placeholder="clikson@gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="tp-contact-form-input mb-20">
                                            <label>{{ __('frontend.home.form_phone') }}</label>
                                            {{-- <input name="subject" type="text" placeholder="+225 636 956"> --}}
                                            <input name="phone" type="text" value="{{ old('phone') }}" placeholder="+225 636 956">
                                        </div>
                                    </div>
                                    {{--<div class="col-md-6">
                                        <div class="tp-contact-form-input mb-20">
                                            <label>{{ __('frontend.blog.consultation_type') }}</label>
                                            <select name="consultation_type">
                                                
                                                <option value="">{{ __('frontend.blog.select_consultation') }}</option>
                                                <option>{{ __('frontend.blog.psychology_consultation') }}</option>
                                                <option>{{ __('frontend.blog.legal_consultation') }}</option>
                                               
                                                <option value="">{{ __('frontend.blog.select_consultation') }}</option>
                                                <option value="psychology" @selected(old('consultation_type') === 'psychology')>{{ __('frontend.blog.psychology_consultation') }}</option>
                                                <option value="legal" @selected(old('consultation_type') === 'legal')>{{ __('frontend.blog.legal_consultation') }}</option>
                                            </select>
                                        </div>
                                    </div>--}}
                                    <div class="col-md-12">
                                        <div class="tp-contact-form-input mb-20">
                                            <label>{{ __('frontend.home.form_message') }}</label>
                                            <textarea name="message" placeholder="{{ __('frontend.blog.message_placeholder') }}">{{ old('message') }}</textarea>
                                        </div>
                                        <div class="tp-contact-form-btn">
                                            <button type="submit">
                                                <span>
                                                    {{-- <span class="text-1">{{ __('frontend.home.book_consultation') }}</span>
                                                    <span class="text-2">{{ __('frontend.home.book_consultation') }}</span> --}}
                                                    <span class="text-1">{{ __('frontend.home.contact_submit') }}</span>
                                                    <span class="text-2">{{ __('frontend.home.contact_submit') }}</span>
                                                </span>
                                            </button>
                                            <a class="onboarding-start-btn" href="{{ route('frontend.onboarding.plans') }}">
                                                 <span>
                                                <span class="text-1">{{ __('frontend.onboarding.start_button') }}</span>
                                                <span class="text-2">{{ __('frontend.onboarding.start_button') }}</span>
                                                </span>
                                            </a>
                                            <p class="ajax-response mt-5 mb-0"></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
