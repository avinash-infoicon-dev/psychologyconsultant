@php
  $filterId = $filterId ?? ('homeSliderFilter'.$loop->iteration);
  $href = $href ?: '#contact-form';
@endphp
<a href="{{ $href }}" class="tp-btn-black btn-green-light-bg">
    <span class="tp-btn-black-filter-blur">
        <svg width="0" height="0">
            <defs>
                <filter id="{{ $filterId }}">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="5"
                        result="blur"></feGaussianBlur>
                    <feColorMatrix in="blur"
                        values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9">
                    </feColorMatrix>
                    <feComposite in="SourceGraphic" in2="{{ $filterId }}"
                        operator="atop"></feComposite>
                    <feBlend in="SourceGraphic" in2="{{ $filterId }}"></feBlend>
                </filter>
            </defs>
        </svg>
    </span>
    <span class="tp-btn-black-filter d-inline-flex align-items-center"
        style="filter: url(#{{ $filterId }})">
        <span class="tp-btn-black-text">{{ $label }}</span>
        <span class="tp-btn-black-circle">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M1 9L9 1M9 1H1M9 1V9" stroke="currentcolor"
                    stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </span>
    </span>
</a>
