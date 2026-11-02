@php
  $servicesPage = $servicesPage ?? null;
  $subtitle = $servicesPage?->layout('section_subtitle') ?: __('frontend.home.service_subtitle');
  $title = $servicesPage?->layout('section_title') ?: __('frontend.home.service_title');
  $items = \App\Services\Cms\CmsServicesPageService::frontendCards($servicesPage);
@endphp
<!-- service area start -->
    <div class="cst-service-ptb pb-120" data-bg-color="#F3F1F2">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-service-heading text-center mb-60">
                        <span class="cst-section-subtitle mb-15 tp_fade_anim" data-delay=".3">
                            {{ $subtitle }}
                        </span>
                        <h4 class="cst-section-title tp_fade_anim" data-delay=".5">
                            {!! $title !!}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach ($items as $item)
                <div class="col-xl-4 col-md-6">
                    <div class="cst-service-item mb-30 tp_fade_anim" data-delay="{{ $item['delay'] }}" data-bg-color="{{ $item['bg'] }}">
                        <div class="cst-service-item-content">
                            <div class="cst-service-item-icon">
                                <span class="mb-25">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                            </div>
                            <h4 class="cst-service-item-title">
                                <a class="tp-line-black" href="{{ route('frontend.services') }}">{{ $item['title'] }}</a>
                            </h4>
                            <p>{{ $item['desc'] }}</p>
                        </div>
                        <div class="cst-service-item-thumb">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- service area end -->
