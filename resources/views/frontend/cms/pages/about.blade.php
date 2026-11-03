@php
  $heroBg = $page->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'));
  $shapeImg = $page->layoutUrl('shape_image', \App\Support\PublicMedia::aboutDefault('about-shape-1.webp'));
  $bannerImg = $page->layoutUrl('banner_image', \App\Support\PublicMedia::aboutDefault('video-thumb-1.webp'));
  $aboutImg = $page->layoutUrl('about_image', \App\Support\PublicMedia::aboutDefault('thumb-1.webp'));
  $ctaImg = $page->layoutUrl('cta_image', \App\Support\PublicMedia::aboutDefault('cta-thumb-1.webp'));
@endphp
<div class="tp-hero-2-wrapper">
     {{-- <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ asset('frontendassets/assets/img/hero/hero-bg.webp') }}"> --}}
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
     <div class="des-project-area">
        <div class="des-project-banner">
            <img class="w-100" data-speed=".7" src="{{ $bannerImg }}" alt="">
            {{-- previous fallback: asset('frontendassets/assets/img/video/video-thumb-1.webp') --}}
        </div>
    </div>

    <div class="cst-about-ptb p-relative pt-170 pb-120">
        <div class="cst-about-shape d-none d-xxl-block" data-speed=".9">
            {{-- <img src="{{ asset('frontendassets/assets/img/about/about-shape-1.webp') }}" alt=""> --}}
            <img src="{{ $shapeImg }}" alt="">
        </div>
        <div class="container container-1530">
            <div class="row">
                <div class="col-xxl-2 d-none d-xxl-block"></div>
                <div class="col-xxl-6 col-lg-6">
                    <div class="cst-about-heading mb-40">
                        @if ($page->layout('heading'))
                          <h3 class="cst-section-title fs-32 mb-25 tp_fade_anim" data-delay=".3">
                            {!! $page->layout('heading') !!}
                          </h3>
                        @endif
                        @if ($page->layout('desc_1'))
                          <div class="cst-about-text tp_fade_anim" data-delay=".5">
                            <p class="color-g mb-45">{{ $page->layout('desc_1') }}</p>
                          </div>
                        @endif
                        <div class="cst-about-list d-block mb-50 tp_fade_anim" data-delay=".7">
                            <ul>
                                @foreach ([1,2,3,4] as $n)
                                  @if ($page->layout('list_'.$n))
                                    <li>
                                      <span><i class="fa-solid fa-check"></i></span>
                                      {{ $page->layout('list_'.$n) }}
                                    </li>
                                  @endif
                                @endforeach
                            </ul>
                        </div>
                        @if ($page->layout('button_text'))
                          <div class="cst-about-btn tp_fade_anim" data-delay=".7" data-fade-from="top" data-ease="bounce">
                            <a class="cst-btn" href="{{ route('frontend.auth.login') }}">
                                <span>
                                    <span class="text-1">{{ $page->layout('button_text') }} <i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="text-2">{{ $page->layout('button_text') }} <i class="fa-solid fa-arrow-right"></i></span>
                                </span>
                            </a>
                          </div>
                        @endif
                    </div>
                </div>
                <div class="col-xxl-4 col-lg-6">
                    <div class="cst-about-right">
                        @if ($page->layout('desc_2'))
                          <div class="cst-about-text tp_fade_anim" data-delay=".5">
                            <p class="color-g mb-45">{{ $page->layout('desc_2') }}</p>
                          </div>
                        @endif
                        @if ($aboutImg)
                          <div class="cst-about-thumb fix">
                            <div class="tp_img_reveal">
                                <img src="{{ $aboutImg }}" alt="">
                            </div>
                          </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.partials.sections.testimonials', [
      'testimonials' => $testimonials,
      'heading' => $page->layout('testimonials_title') ?: __('frontend.home.testimonial_title'),
      'ratingText' => $page->layout('testimonials_rating') ?: __('frontend.home.testimonial_subtitle'),
    ])

    @include('frontend.partials.sections.team', [
      'teamMembers' => $teamMembers,
      'psychologists' => $psychologists ?? collect(),
      'teamSubtitle' => $page->layout('team_subtitle') ?: __('frontend.home.team_subtitle'),
      'teamTitle' => $page->layout('team_title') ?: __('frontend.home.team_title'),
      'teamButtonText' => $page->layout('team_button_text') ?: __('frontend.home.team_subtitle'),
    ])

    <div class="cst-cta-ptb">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    {{--<div class="cst-cta-wrapper">
                        <div class="cst-cta-thumb">
                            <img src="{{ $ctaImg }}" alt="">
                            <!-- previous fallback: asset('frontendassets/assets/img/thumb-1.webp') -->
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
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</main>
