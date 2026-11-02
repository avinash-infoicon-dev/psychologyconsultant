@php
  $heroBg = $page->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'));
  $ctaImg = $page->layoutUrl('cta_image', \App\Support\PublicMedia::aboutDefault('cta-thumb-1.webp'));
@endphp
<div class="tp-hero-2-wrapper">
     {{-- Design: data-background="assets/img/hero/hero-bg.webp" --}}
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         @if ($page->layout('hero_subtitle'))
                           <span class="tp-hero-2-subtitle tp_fade_anim" data-delay=".3">{{ $page->layout('hero_subtitle') }}</span>
                         @endif
                         <h1 class="tp-hero-2-title text-scale-anim font-bold tp_fade_anim" data-delay=".5">{{ $page->title }}</h1>
                         @if ($page->meta_description)
                           <p class="cst-hero-text text-white mb-45 tp_fade_anim">{{ $page->meta_description }}</p>
                         @endif
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>

<main>
    @include('frontend.partials.sections.faq', [
      'faqs' => $faqs ?? collect(),
      'faqSubtitle' => $page->layout('faq_subtitle') ?: __('frontend.home.faq_subtitle'),
      'faqTitle' => $page->layout('faq_title') ?: __('frontend.home.faq_title'),
      'faqButtonText' => $page->layout('faq_button_text') ?: __('frontend.home.faq_btn'),
      'faqButtonUrl' => $page->layout('faq_button_url') ?: route('frontend.auth.register'),
      'accordionId' => 'faqPageAccordion',
    ])

    {{--<div class="cst-cta-ptb">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-cta-wrapper">
                        <div class="cst-cta-thumb">
                            <!-- <img src="assets/img/thumb-1.webp" alt=""> -->
                            <img src="{{ $ctaImg }}" alt="">
                        </div>
                        <div class="cst-cta-content">
                            @if ($page->layout('cta_subtitle'))
                              <span class="cst-section-subtitle mb-10">{{ $page->layout('cta_subtitle') }}</span>
                            @endif
                            @if ($page->layout('cta_title'))
                              <h4 class="cst-section-title mb-15">{{ $page->layout('cta_title') }}</h4>
                            @endif
                            @if ($page->layout('cta_text'))
                              <p class="cst-cta-content-text">{{ $page->layout('cta_text') }}</p>
                            @endif
                            <div class="cst-cta-input-box">
                                <form id="contact-form" action="{{ url()->current() }}" method="POST">
                                    @csrf
                                    <div class="row gx-12">
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_name') }}</label>
                                                <input name="name" type="text" placeholder="{{ __('frontend.home.form_name_placeholder') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_email') }}</label>
                                                <input name="email" type="email" placeholder="clikson@gmail.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_phone') }}</label>
                                                <input name="subject" type="text" placeholder="+225 636 956">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>Consultation Type</label>
                                                <select name="consultation_type">
                                                    <option value="">Select Consultation</option>
                                                    <option>Psychology Consultation</option>
                                                    <option>General Psychology Support</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="tp-contact-form-input mb-20">
                                                <label>{{ __('frontend.home.form_message') }}</label>
                                                <textarea name="message" placeholder="{{ __('frontend.home.form_message') }}"></textarea>
                                            </div>
                                            <div class="tp-contact-form-btn">
                                                <button type="submit">
                                                    <span>
                                                        <span class="text-1">{{ __('frontend.home.book_consultation') }}</span>
                                                        <span class="text-2">{{ __('frontend.home.book_consultation') }}</span>
                                                    </span>
                                                </button>
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
    </div>--}}
</main>
