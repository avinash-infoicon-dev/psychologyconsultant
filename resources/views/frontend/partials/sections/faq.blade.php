@php
  $faqs = $faqs ?? collect();
  $faqSubtitle = $faqSubtitle ?? __('frontend.home.faq_subtitle');
  $faqTitle = $faqTitle ?? __('frontend.home.faq_title');
  $faqButtonText = $faqButtonText ?? __('frontend.home.faq_btn');
  $faqButtonUrl = $faqButtonUrl ?? '#';
  $accordionId = $accordionId ?? 'cmsFaqAccordion';
@endphp
<div class="cst-faq-ptb p-relative pt-140 pb-120" data-bg-color="#F3F1F2">
    <div class="container container-1530">
        <div class="row">
            <div class="col-lg-5">
                <div class="cst-faq-heading p-relative mb-50">
                    @if ($faqSubtitle)
                      <span class="cst-section-subtitle mb-10 tp_fade_anim" data-delay=".3">{{ $faqSubtitle }}</span>
                    @endif
                    <h4 class="cst-section-title mb-50 tp_fade_anim" data-delay=".5">{!! $faqTitle !!}</h4>
                    @if ($faqButtonText)
                      <div class="cst-faq-btn tp_fade_anim" data-delay=".7">
                        <a class="cst-btn" href="{{ $faqButtonUrl }}">
                            <span>
                                <span class="text-1">
                                    {{ $faqButtonText }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <span class="text-2">
                                    {{ $faqButtonText }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </span>
                        </a>
                      </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="cst-faq-wrap pl-70">
                    <div class="ai-faq-accordion-wrap tp_fade_anim" data-delay=".3" data-fade-from="right" data-ease="bounce">
                        <div class="accordion" id="{{ $accordionId }}">
                            @foreach ($faqs as $index => $item)
                              @php
                                $collapseId = $accordionId.'-'.$item->id;
                                $isFirst = $index === 0;
                              @endphp
                              <div class="accordion-items">
                                <h2 class="accordion-header">
                                    <button class="accordion-buttons{{ $isFirst ? '' : ' collapsed' }}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                        aria-controls="{{ $collapseId }}">
                                        {{ $item->question }}
                                        <span class="accordion-icon"></span>
                                    </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse{{ $isFirst ? ' show' : '' }}"
                                    data-bs-parent="#{{ $accordionId }}">
                                    <div class="accordion-body">
                                        <p>{{ $item->answer }}</p>
                                    </div>
                                </div>
                              </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
