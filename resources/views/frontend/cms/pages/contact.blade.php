@php
  $heroBg = $page->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'));
  $contactBg = $page->layoutUrl('contact_background');
  if ($contactBg && $contactBg === $heroBg) {
      $contactBg = null;
  }
  $funfactBg = $page->layoutUrl('funfact_background', 'frontendassets/assets/img/funfact-bg-shape-2.webp');
  $funfactCircle = $page->layoutUrl('funfact_circle');
  $socialBg = $page->layoutUrl('social_background', 'frontendassets/assets/img/contact-us-shape.webp');
  $phone = $page->layout('phone');
  $email = $page->layout('email');
  $hours = $page->layout('hours');
  $phoneHref = $phone ? 'tel:'.preg_replace('/\s+/', '', $phone) : '#';
  $emailHref = $email ? 'mailto:'.$email : '#';
  $mapSrc = $page->layout('map_embed') ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2888.7253730629127!2d1.4224655999999998!3d43.6122594!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebb3f0f4c760d%3A0xdbc3771a58a2eb3d!2sPorte%20du%20Barrio%2C%204%20Rue%20C%C3%A9cile%20Brunschvicg%2C%2031200%20Toulouse%2C%20France!5e0!3m2!1sen!2sin!4v1790236558637!5m2!1sen!2sin';
@endphp
<div class="tp-hero-2-wrapper">
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
    {{-- Design: data-background="assets/img/team/team-bg.png" (light). Do not reuse the hero banner image. --}}
    <div class="tp-contact-main-ptb p-relative" @if($contactBg) data-background="{{ $contactBg }}" @endif>
        <div class="tp-career-shape-1">
            <span><svg xmlns="http://www.w3.org/2000/svg" width="84" height="84" viewBox="0 0 84 84" fill="none">
                    <path
                        d="M36.3761 0.5993C40.3065 8.98556 47.3237 34.898 32.8824 44.3691C25.3614 49.0997 9.4871 52.826 1.7513 31.3747C-1.16691 23.2826 5.38982 15.9009 20.5227 20.0332C29.2536 22.4173 50.3517 27.8744 60.9 44.2751C66.4672 52.9311 71.833 71.0287 69.4175 82.9721M69.4175 82.9721C70.1596 77.2054 74.079 66.0171 83.8204 67.3978M69.4175 82.9721C69.8033 79.1875 67.076 70.1737 53.0797 64.3958M49.1371 20.8349C52.611 22.1801 63.742 28.4916 67.9921 39.9344"
                        stroke="#030303" stroke-width="1.5" />
                </svg></span>
        </div>
        <div class="container container-1230">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="ar-hero-title-box tp_fade_anim" data-delay=".3">
                        <div class="ar-about-us-4-title-box d-flex align-items-center mb-20">
                            <span class="tp-section-subtitle pre tp_fade_anim">{{ $page->layout('contact_kicker') ?: __('frontend.nav.contact') }}</span>
                            <div class="ar-about-us-4-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="81" height="9" viewBox="0 0 81 9"
                                    fill="none">
                                    <rect y="4" width="80" height="1" fill="#111013" />
                                    <path d="M77 7.96366L80.5 4.48183L77 1" stroke="#111013" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="tp-career-title fs-100 pb-30">{{ $page->layout('heading') ?: $page->title }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="tp-contact-main-bottom">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-9 col-lg-8"></div>

                    <div class="col-xl-3 col-lg-4">
                        <div class="tp-contact-main-funfact-wrap">
                            <div class="crp-hero-funfact-wrap crp-hero-funfact-bg z-index-1 p-relative tp_fade_anim ar-about-funfact-color"
                                data-delay=".5" data-background="{{ $funfactBg }}">
                                <div class="crp-hero-funfact-line d-none d-xl-inline-block"></div>
                                @if ($funfactCircle)
                                <div class="crp-hero-funfact-img">
                                    <img src="{{ $funfactCircle }}" alt="">
                                </div>
                                @endif
                                <div class="ar-about-funfact-top-content">
                                    <h4>{!! nl2br(e($page->layout('side_heading') ?: __('frontend.contact.get_in_touch'))) !!}</h4>
                                </div>
                                <div class="ar-about-funfact-item-box mb-35">
                                    @if ($phone)
                                    <div class="ar-about-funfact-item d-flex align-items-center">
                                        <div class="ar-about-funfact-item-icon">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M15.6212 8.75008C15.1911 8.75008 14.851 8.40009 14.851 7.98009C14.851 7.61009 14.4809 6.84009 13.8607 6.17009C13.2505 5.52009 12.5804 5.14009 12.0202 5.14009C11.5901 5.14009 11.25 4.79009 11.25 4.3701C11.25 3.9501 11.6001 3.6001 12.0202 3.6001C13.0205 3.6001 14.0708 4.1401 14.991 5.11009C15.8513 6.02009 16.4014 7.15009 16.4014 7.97009C16.4014 8.40009 16.0513 8.75008 15.6212 8.75008Z" fill="white" />
                                                    <path d="M19.23 8.74998C18.7999 8.74998 18.4598 8.39998 18.4598 7.97998C18.4598 4.42999 15.569 1.55 12.028 1.55C11.5979 1.55 11.2578 1.2 11.2578 0.779998C11.2578 0.359999 11.5979 0 12.018 0C16.4192 0 20.0002 3.57999 20.0002 7.97998C20.0002 8.39998 19.6501 8.74998 19.23 8.74998Z" fill="white" />
                                                    <path opacity="0.4" d="M9.79269 12.21L6.52179 15.48C6.1617 15.16 5.8116 14.83 5.4715 14.49C4.44122 13.45 3.51097 12.36 2.68074 11.22C1.86051 10.08 1.20033 8.93998 0.720198 7.80998C0.240066 6.66998 0 5.57999 0 4.53999C0 3.85999 0.120033 3.20999 0.360099 2.60999C0.600165 2 0.98027 1.44 1.51042 0.939998C2.15059 0.309999 2.85078 0 3.59099 0C3.87106 0 4.15114 0.0599999 4.40121 0.18C4.66128 0.299999 4.89135 0.479999 5.07139 0.739998L7.39203 4.00999C7.57208 4.25999 7.70212 4.48999 7.79214 4.70999C7.88217 4.91999 7.93218 5.12999 7.93218 5.31999C7.93218 5.55999 7.86216 5.79999 7.72212 6.02999C7.59209 6.25998 7.40204 6.49998 7.16197 6.73998L6.40176 7.52998C6.29173 7.63998 6.24172 7.76998 6.24172 7.92998C6.24172 8.00998 6.25172 8.07998 6.27173 8.15998C6.30173 8.23998 6.33174 8.29998 6.35175 8.35998C6.5318 8.68998 6.84188 9.11998 7.282 9.63998C7.73213 10.16 8.21226 10.69 8.7324 11.22C9.0925 11.57 9.4426 11.91 9.79269 12.21Z" fill="white" />
                                                    <path d="M19.975 16.33C19.975 16.61 19.9249 16.9 19.8249 17.18C19.7949 17.26 19.7649 17.34 19.7249 17.42C19.5548 17.78 19.3348 18.12 19.0447 18.44C18.5546 18.98 18.0144 19.37 17.4043 19.62C17.3943 19.62 17.3843 19.63 17.3742 19.63C16.7841 19.87 16.1439 20 15.4537 20C14.4334 20 13.3431 19.76 12.1928 19.27C11.0425 18.78 9.89219 18.12 8.75188 17.29C8.36177 17 7.97166 16.71 7.60156 16.4L10.8725 13.13C11.1525 13.34 11.4026 13.5 11.6127 13.61C11.6627 13.63 11.7227 13.66 11.7927 13.69C11.8727 13.72 11.9528 13.73 12.0428 13.73C12.2128 13.73 12.3429 13.67 12.4529 13.56L13.2131 12.81C13.4632 12.56 13.7032 12.37 13.9333 12.25C14.1634 12.11 14.3934 12.04 14.6435 12.04C14.8335 12.04 15.0336 12.08 15.2537 12.17C15.4737 12.26 15.7038 12.39 15.9539 12.56L19.2648 14.91C19.5248 15.09 19.7049 15.3 19.8149 15.55C19.9149 15.8 19.975 16.05 19.975 16.33Z" fill="white" />
                                                </svg></span>
                                        </div>
                                        <div class="ar-about-funfact-item-content">
                                            <p>{{ $page->layout('phone_label') }}</p>
                                            <a class="tp-line-white" href="{{ $phoneHref }}">{{ $phone }}</a>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($email)
                                    <div class="ar-about-funfact-item d-flex align-items-center">
                                        <div class="ar-about-funfact-item-icon">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="21" height="18" viewBox="0 0 21 18" fill="none">
                                                    <path d="M17.9879 5.13992C19.4073 5.13992 20.5579 3.98931 20.5579 2.56996C20.5579 1.15061 19.4073 0 17.9879 0C16.5686 0 15.418 1.15061 15.418 2.56996C15.418 3.98931 16.5686 5.13992 17.9879 5.13992Z" fill="white" />
                                                    <path opacity="0.4" d="M19.2439 6.48673C19.8915 6.28113 20.5597 6.78485 20.5597 7.4736V12.8602C20.5597 16.4582 18.5037 18.0001 15.4198 18.0001H5.13992C2.05597 18.0001 0 16.4582 0 12.8602V5.66434C0 2.06639 2.05597 0.524414 5.13992 0.524414H12.9629C13.6311 0.524414 14.0834 1.14122 13.96 1.78885C13.8367 2.39536 13.8572 3.04298 14.0423 3.71117C14.4226 5.08867 15.5431 6.18861 16.9206 6.5484C17.7327 6.754 18.5243 6.71288 19.2439 6.48673Z" fill="white" />
                                                    <path d="M10.2809 10.1461C9.41737 10.1461 8.54359 9.87884 7.8754 9.33401L4.65781 6.76405C4.32885 6.49677 4.26718 6.01362 4.53445 5.68466C4.80173 5.35571 5.28487 5.29403 5.61382 5.56131L8.83142 8.13127C9.61269 8.75834 10.9388 8.75834 11.7201 8.13127L12.9331 7.16496C13.262 6.89768 13.7555 6.94908 14.0125 7.28831C14.2797 7.61727 14.2284 8.1107 13.8891 8.3677L12.6761 9.33401C12.0182 9.87884 11.1444 10.1461 10.2809 10.1461Z" fill="white" />
                                                </svg></span>
                                        </div>
                                        <div class="ar-about-funfact-item-content">
                                            <p>{{ $page->layout('email_label') }}</p>
                                            <a class="tp-line-white" href="{{ $emailHref }}">{{ $email }}</a>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($hours)
                                    <div class="ar-about-funfact-item d-flex align-items-center">
                                        <div class="ar-about-funfact-item-icon">
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path opacity="0.4" d="M16.4698 14.83L16.8598 17.99C16.9598 18.82 16.0698 19.4 15.3598 18.97L11.1698 16.48C10.7098 16.48 10.2599 16.45 9.81985 16.39C10.5599 15.52 10.9998 14.42 10.9998 13.23C10.9998 10.39 8.53985 8.09003 5.49985 8.09003C4.33985 8.09003 3.26985 8.42 2.37985 9C2.34985 8.75 2.33984 8.49999 2.33984 8.23999C2.33984 3.68999 6.28985 0 11.1698 0C16.0498 0 19.9998 3.68999 19.9998 8.23999C19.9998 10.94 18.6098 13.33 16.4698 14.83Z" fill="white" />
                                                    <path d="M11 13.2298C11 14.4198 10.56 15.5198 9.82001 16.3898C8.83001 17.5898 7.26 18.3598 5.5 18.3598L2.89 19.9098C2.45 20.1798 1.89 19.8098 1.95 19.2998L2.2 17.3298C0.859997 16.3998 0 14.9098 0 13.2298C0 11.4698 0.940005 9.91982 2.38 8.99982C3.27 8.41982 4.34 8.08984 5.5 8.08984C8.54 8.08984 11 10.3898 11 13.2298Z" fill="white" />
                                                </svg></span>
                                        </div>
                                        <div class="ar-about-funfact-item-content">
                                            <p>{{ $page->layout('hours_label') }}</p>
                                            <a class="tp-line-white" href="#">{{ $hours }}</a>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tp-contact-map-ptb p-relative pb-120">
        <div class="tp-contact-map-wrapper p-relative">
            <div class="tp-contact-map-icon-box">
                <div class="tp-contact-map-icon">
                    {{-- Design: assets/img/contact/map-icon.svg --}}
                    <span><i class="fa-solid fa-location-dot"></i></span>
                </div>
            </div>
            <iframe
                src="{{ $mapSrc }}"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    </div>

    <div class="cn-contactform-support-area mb-140">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="cn-contactform-support-bg d-flex flex-column align-items-center justify-content-center"
                        data-background="{{ $socialBg }}">
                        <div class="cn-contactform-support-text text-center">
                            <span>{{ $page->layout('social_subtitle') }}</span>
                        </div>
                        @if ($page->layout('social_text'))
                          <p>{{ $page->layout('social_text') }}</p>
                        @endif
                        <ul class="social-links">
                            <li>
                                <a href="{{ $page->layout('social_facebook') ?: '#' }}" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ $page->layout('social_twitter') ?: '#' }}" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ $page->layout('social_instagram') ?: '#' }}" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.blog.partials.cta')
</main>
