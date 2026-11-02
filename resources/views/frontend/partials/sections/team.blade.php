@php
  $teamMembers = $teamMembers ?? collect();
  $psychologists = $psychologists ?? collect();
  $teamSubtitle = $teamSubtitle ?? __('frontend.home.team_subtitle');
  $teamTitle = $teamTitle ?? __('frontend.home.team_title');
  $teamButtonText = $teamButtonText ?? __('frontend.home.team_subtitle');
  $psychologistsListUrl = route('frontend.meet-psychologists');
  $teamButtonUrl = $psychologistsListUrl;
  // Guests hitting the patient directory are sent to login; patients land on the list.
@endphp
    <div class="cst-team-ptb pt-140 pb-80">
        <div class="container container-1530">
            <div class="row align-items-end">
                <div class="col-lg-6">
                    <div class="cst-team-heading mb-50">
                        @if ($teamSubtitle)
                          <span class="cst-section-subtitle mb-15 tp_fade_anim" data-delay=".3">{{ $teamSubtitle }}</span>
                        @endif
                        <h4 class="cst-section-title tp_fade_anim" data-delay=".5">{!! $teamTitle !!}</h4>
                    </div>
                </div>
                @if ($teamButtonText)
                  <div class="col-lg-6">
                    <div class="cst-team-btn text-lg-end mb-50 tp_fade_anim" data-delay=".7" data-fade-from="top" data-ease="bounce">
                        <a class="cst-btn" href="{{ $teamButtonUrl }}">
                            <span>
                                <span class="text-1">{{ $teamButtonText }} <i class="fa-solid fa-arrow-right"></i></span>
                                <span class="text-2">{{ $teamButtonText }} <i class="fa-solid fa-arrow-right"></i></span>
                            </span>
                        </a>
                    </div>
                  </div>
                @endif
            </div>
            <div class="cst-team-box">
                <div class="row">
                    @foreach ($psychologists as $index => $psychologist)
                      @php
                        $displayName = trim(($psychologist->title ? $psychologist->title.' ' : '').$psychologist->name);
                        $specializationLabel = $psychologist->specializations
                            ->pluck('name')
                            ->filter()
                            ->implode(', ')
                            ?: ($psychologist->specialization ?: $psychologist->professionalType?->name);
                        $profileUrl = route('frontend.meet-psychologists.show', $psychologist);
                      @endphp
                      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".{{ 3 + $index }}">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="{{ $profileUrl }}">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ $psychologist->profile_image_url }}" alt="{{ $displayName }}">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="{{ $profileUrl }}">{{ $displayName }}</a>
                                </h4>
                                @if ($specializationLabel)
                                  <span>{{ $specializationLabel }}</span>
                                @endif
                            </div>
                        </div>
                      </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@if (false)
{{-- Previous CMS team-member cards (kept for reference) --}}
@if ($teamMembers->isNotEmpty())
    <div class="cst-team-ptb pt-140 pb-80">
        <div class="container container-1530">
            <div class="row align-items-end">
                <div class="col-lg-6">
                    <div class="cst-team-heading mb-50">
                        @if ($teamSubtitle)
                          <span class="cst-section-subtitle mb-15 tp_fade_anim" data-delay=".3">{{ $teamSubtitle }}</span>
                        @endif
                        <h4 class="cst-section-title tp_fade_anim" data-delay=".5">{!! $teamTitle !!}</h4>
                    </div>
                </div>
                @if ($teamButtonText)
                  <div class="col-lg-6">
                    <div class="cst-team-btn text-lg-end mb-50 tp_fade_anim" data-delay=".7" data-fade-from="top" data-ease="bounce">
                        <a class="cst-btn" href="{{ $teamButtonUrl ?? '#' }}">
                            <span>
                                <span class="text-1">{{ $teamButtonText }} <i class="fa-solid fa-arrow-right"></i></span>
                                <span class="text-2">{{ $teamButtonText }} <i class="fa-solid fa-arrow-right"></i></span>
                            </span>
                        </a>
                    </div>
                  </div>
                @endif
            </div>
            <div class="cst-team-box">
                <div class="row">
                    @foreach ($teamMembers as $index => $member)
                      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="dgm-team-item mb-40 tp_fade_anim" data-delay=".{{ 3 + $index }}">
                            <div class="dgm-team-thumb tp--hover-item p-relative">
                                <a href="{{ $member->profile_url ?: '#' }}">
                                    <div class="tp--hover-img" data-displacement="{{ asset('frontendassets/assets/img/fluid.webp') }}"
                                        data-intensity="0.6" data-speedin="1" data-speedout="1">
                                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                                    </div>
                                </a>
                            </div>
                            <div class="dgm-team-content">
                                <h4 class="dgm-team-title-sm">
                                    <a class="tp-line-black" href="{{ $member->profile_url ?: '#' }}">{{ $member->name }}</a>
                                </h4>
                                @if ($member->role)
                                  <span>{{ $member->role }}</span>
                                @endif
                                <div class="dgm-team-social">
                                    @if ($member->facebook_url)
                                      <a href="{{ $member->facebook_url }}"><span><i class="fa-brands fa-facebook-f"></i></span></a>
                                    @endif
                                    @if ($member->twitter_url)
                                      <a href="{{ $member->twitter_url }}"><span><i class="fa-brands fa-twitter"></i></span></a>
                                    @endif
                                    @if ($member->linkedin_url)
                                      <a href="{{ $member->linkedin_url }}"><span><i class="fa-brands fa-linkedin-in"></i></span></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                      </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
@endif
