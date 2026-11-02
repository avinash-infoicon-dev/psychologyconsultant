@php
  $heroBg = $page->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'))
    ?: asset('frontendassets/assets/img/hero/hero-bg.webp');
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
    @include('frontend.partials.service-area', ['servicesPage' => $page])
</main>
