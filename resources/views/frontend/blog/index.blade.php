@extends('frontend.layouts.guest')

@section('title', ($page ?? null)?->meta_title ?: (($page ?? null)?->title ?: __('frontend.blog.title')))

@php
  $heroBg = ($page ?? null)?->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'))
      ?: asset('frontendassets/assets/img/hero/hero-bg.webp');
  $defaultCover = asset('frontendassets/assets/img/blog/blog-1.webp');
  $defaultAuthor = asset('frontendassets/assets/img/blog/blog-1.webp');
@endphp

@section('content')
<div class="tp-hero-2-wrapper">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle tp_fade_anim" data-delay=".3">{{ ($page ?? null)?->layout('hero_subtitle') ?: __('frontend.blog.subtitle') }}</span>
                         <h1 class="tp-hero-2-title text-scale-anim font-bold tp_fade_anim" data-delay=".5">{{ ($activeCategory->name ?? null) ?: (($page ?? null)?->title ?: __('frontend.blog.title')) }}</h1>
                         @if (($page ?? null)?->meta_description)
                           <p class="cst-hero-text text-white mb-45 tp_fade_anim">{{ $page->meta_description }}</p>
                         @else
                           <p class="cst-hero-text text-white mb-45 tp_fade_anim">{{ __('frontend.blog.lead') }}</p>
                         @endif
                     </div>
                 </div>
             </div>
         </div>
     </div>
</div>

<main>
    <section id="postbox" class="postbox-area pt-110 pb-80">
        <div class="container container-1330">
            <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="postbox-wrapper">
                        @forelse ($posts as $item)
                            @php
                              $url = route('frontend.blog.show', $item->slug);
                              $tag = $item->tagList()[0] ?? $item->category?->name;
                            @endphp
                            <article class="postbox-item mb-30">
                                <div class="postbox-author-wrap d-flex align-items-center justify-content-between mb-30">
                                    {{--<div class="postbox-author-box d-flex align-items-center ">
                                        <div class="postbox-author-img">
                                            <img src="{{ $item->author_photo_url ?: $defaultAuthor }}" alt="{{ $item->author_name }}">
                                        </div>
                                        <div class="postbox-author-info">
                                            <h4 class="postbox-author-name">{{ $item->author_name }}</h4>
                                            <span>{{ $item->author_role }}</span>
                                        </div>
                                    </div>--}}
                                    <div class="postbox-meta">
                                        <i>
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 4.19997V8.99997L12.2 10.6M17 9C17 13.4183 13.4183 17 9 17C4.58172 17 1 13.4183 1 9C1 4.58172 4.58172 1 9 1C13.4183 1 17 4.58172 17 9Z" stroke="black" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                        <span>{{ optional($item->published_at)->translatedFormat('F j, Y') }}</span>
                                    </div>
                                </div>
                                @if ($item->cover_url || $item->video_url)
                                <div class="postbox-thumb @if($item->video_url) postbox-thumb-overlay p-relative @endif mb-35">
                                    <a href="{{ $url }}">
                                        <img class="w-100" src="{{ $item->cover_url ?: $defaultCover }}" alt="{{ $item->title }}">
                                    </a>
                                    @if ($item->video_url)
                                        <div class="postbox-play-btn z-index-1">
                                            <a class="popup-video" href="{{ $item->video_url }}">
                                                <span>
                                                    <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15 9L0 17.6603L0 0.339746L15 9Z" fill="black" />
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                @endif
                                <div class="postbox-content">
                                    @if ($tag)
                                    <span class="postbox-tag">
                                        <i>
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.39101 4.39135H4.39936M13.6089 8.73899L8.74578 13.6021C8.61979 13.7283 8.47018 13.8283 8.3055 13.8966C8.14082 13.9649 7.9643 14 7.78603 14C7.60777 14 7.43124 13.9649 7.26656 13.8966C7.10188 13.8283 6.95228 13.7283 6.82629 13.6021L1 7.78264V1H7.78264L13.6089 6.82629C13.8616 7.08045 14.0034 7.42427 14.0034 7.78264C14.0034 8.14102 13.8616 8.48483 13.6089 8.73899Z" stroke="black" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                        {{ $tag }}
                                    </span>
                                    @endif
                                    <h3 class="postbox-title"><a href="{{ $url }}">{{ $item->title }}</a></h3>
                                    @if ($item->excerpt)
                                      <p>{{ $item->excerpt }}</p>
                                    @endif
                                    <a class="tp-btn-yellow-border postbox-btn" href="{{ $url }}">
                                        <span>
                                            <span class="text-1">{{ __('frontend.blog.view_more') }}</span>
                                            <span class="text-2">{{ __('frontend.blog.view_more') }}</span>
                                        </span>
                                        <i>
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 11L11 1M11 1V11M11 1H1" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 11L11 1M11 1V11M11 1H1" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                    </a>
                                </div>
                            </article>
                        @empty
                            <p>{{ __('frontend.blog.empty') }}</p>
                        @endforelse

                        @if ($posts->hasPages())
                        <div class="basic-pagination-wrap">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="basic-pagination mb-0">
                                        <nav>
                                            <ul>
                                                @if ($posts->onFirstPage())
                                                    <li><span><i class="fa-regular fa-angle-left"></i></span></li>
                                                @else
                                                    <li><a href="{{ $posts->previousPageUrl() }}"><i class="fa-regular fa-angle-left"></i></a></li>
                                                @endif
                                                @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $pageNum => $pageUrl)
                                                    <li>
                                                        @if ($pageNum === $posts->currentPage())
                                                            <span class="current">{{ $pageNum }}</span>
                                                        @else
                                                            <a href="{{ $pageUrl }}">{{ $pageNum }}</a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                                @if ($posts->hasMorePages())
                                                    <li><a href="{{ $posts->nextPageUrl() }}"><i class="fa-regular fa-angle-right"></i></a></li>
                                                @else
                                                    <li><span><i class="fa-regular fa-angle-right"></i></span></li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @include('frontend.blog.partials.sidebar')
            </div>
        </div>
    </section>

    {{--@include('frontend.blog.partials.cta')--}}
</main>
@endsection
