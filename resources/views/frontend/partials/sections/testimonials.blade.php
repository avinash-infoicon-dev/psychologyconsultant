@php
  $testimonials = $testimonials ?? collect();
  $heading = $heading ?? __('frontend.home.testimonial_title');
  $ratingText = $ratingText ?? __('frontend.home.testimonial_subtitle');
@endphp
@if ($testimonials->isNotEmpty())
    <div class="cst-testimonial-ptb pt-110 pb-100" data-bg-color="#F3F1F2">
        <div class="container container-1530">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="cst-testimonial-heading mb-40">
                        <h3 class="cst-section-title">{!! $heading !!}</h3>
                    </div>
                </div>
                {{--<div class="col-lg-4">
                    <div class="tp-testimonial-ratting-box d-flex justify-content-start justify-content-lg-end mb-40">
                        <div class="tp-testimonial-ratting d-inline-flex align-items-center">
                            <span class="mr-15 d-inline-block">
                                <i class="fa-solid fa-award"></i>
                            </span>
                            <div class="tp-testimonial-ratting-info">
                                <div class="tp-testimonial-ratting-icon">
                                    <span>4.9/5</span>
                                    <i><i class="fa-solid fa-star"></i></i>
                                    <i><i class="fa-solid fa-star"></i></i>
                                    <i><i class="fa-solid fa-star"></i></i>
                                    <i><i class="fa-solid fa-star"></i></i>
                                    <i><i class="fa-solid fa-star"></i></i>
                                </div>
                                <p>{{ $ratingText }}</p>
                            </div>
                        </div>
                    </div>
                </div>--}}
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-testimonial-slider-wrapper">
                        <div class="cst-testimonial-slider swiper">
                            <div class="swiper-wrapper">
                                @foreach ($testimonials as $item)
                                  <div class="swiper-slide">
                                    <div class="cst-testimonial-item">
                                        <div class="cst-testimonial-item-shape">
                                            <i class="fa-solid fa-quote-right"></i>
                                        </div>
                                        <div class="cst-testimonial-item-top">
                                            <div class="cst-testimonial-item-icon">
                                                <span><i class="fa-solid fa-quote-left"></i></span>
                                            </div>
                                            <div class="cst-testimonial-item-text">
                                                <p>{{ $item->quote }}</p>
                                            </div>
                                        </div>
                                        <div class="cst-testimonial-item-user">
                                            @if ($item->photo_url)
                                              <div class="cst-testimonial-item-user-thumb">
                                                <img src="{{ $item->photo_url }}" alt="{{ $item->author_name }}">
                                              </div>
                                            @endif
                                            <div class="cst-testimonial-item-user-content">
                                                <h4>{{ $item->author_name }}</h4>
                                                <span>{{ $item->author_role }}</span>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="dgm-testimonial-dot pt-55"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
