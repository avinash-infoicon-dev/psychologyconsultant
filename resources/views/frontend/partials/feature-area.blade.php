@php
  $homePage = $homePage ?? null;
  $featureBg = $homePage?->layout('feature_bg_color') ?: '#F3F1F2';
  $videoImg = $homePage?->layoutUrl('video_image')
      ?: asset('frontendassets/assets/img/video/video-thumb-1.webp');
  $defaultIcons = [
    1 => 'fa-solid fa-user-secret',
    2 => 'fa-solid fa-user-doctor',
    3 => 'fa-solid fa-comments',
  ];
  $defaultBgs = [
    1 => 'secondary',
    2 => 'black',
    3 => 'primary',
  ];
  $defaultDelays = [
    1 => '.3',
    2 => '.5',
    3 => '.7',
  ];
  $features = [];
  foreach ([1, 2, 3] as $n) {
      $features[] = [
          'title' => $homePage?->layout('feature_'.$n.'_title') ?: __('frontend.home.feature_'.$n.'_title'),
          'desc' => $homePage?->layout('feature_'.$n.'_desc') ?: __('frontend.home.feature_'.$n.'_desc'),
          'icon' => $homePage?->layout('feature_'.$n.'_icon') ?: $defaultIcons[$n],
          'bg' => $homePage?->layout('feature_'.$n.'_bg') ?: $defaultBgs[$n],
          'delay' => $defaultDelays[$n],
      ];
  }
@endphp
<!-- feature area start -->
    <div class="cst-feature-ptb pb-120" data-bg-color="{{ $featureBg }}">
        <div class="container container-1530">
            <div class="cst-feature-top">
                <div class="row">
                    @foreach ($features as $feature)
                    <div class="col-lg-4 col-md-6">
                        <div class="cst-feature-item mb-30 tp_fade_anim" data-delay="{{ $feature['delay'] }}">
                            <div class="cst-feature-item-icon">
                                <span data-bg-color="{{ $feature['bg'] }}">
                                    <i class="{{ $feature['icon'] }}"></i>
                                </span>
                            </div>
                            <div class="cst-feature-item-content">
                                <h5 class="cst-feature-item-title">{{ $feature['title'] }}</h5>

                                <p class="color-g mb-0">
                                    {{ $feature['desc'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="cst-feature-bottom pt-40">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h4 class="cst-feature-text text-center tp_fade_anim" data-delay=".9">
                            {!! $homePage?->layout('feature_bottom_text') ?: __('frontend.home.feature_bottom_text') !!}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- feature area end -->


    <!-- video area start -->
    <div class="cst-video-ptb p-relative pb-160">
        <div class="container container-1530">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cst-video-thumb text-center">
                        <img class="tp-gsap-image w-100" src="{{ $videoImg }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- video area end -->
