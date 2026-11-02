@extends('frontend.layouts.guest')

@section('title', $post->title)

@php
  $heroBg = ($page ?? null)?->layoutUrl('hero_background', \App\Support\PublicMedia::aboutDefault('hero-bg.webp'))
      ?: asset('frontendassets/assets/img/hero/hero-bg.webp');
  $defaultCover = asset('frontendassets/assets/img/blog/blog-1.webp');
  $defaultAuthor = asset('frontendassets/assets/img/hero/slider-5.webp');
  $tag = $post->tagList()[0] ?? $post->category?->name;
@endphp

@section('content')
<div class="tp-hero-2-wrapper">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle tp_fade_anim" data-delay=".3">{{ __('frontend.blog.title') }}</span>
                         <h1 class="tp-hero-2-title text-scale-anim font-bold tp_fade_anim" data-delay=".5">{{ $post->title }}</h1>
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
                    <div class="postbox-wrapper mb-115">
                        <article class="postbox-details-item item-border mb-20">
                            <div class="postbox-details-info-wrap">
                                <div class="postbox-tag postbox-details-tag">
                                    @if ($tag)
                                    <span>
                                        <i>
                                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.39101 4.39135H4.39936M13.6089 8.73899L8.74578 13.6021C8.61979 13.7283 8.47018 13.8283 8.3055 13.8966C8.14082 13.9649 7.9643 14 7.78603 14C7.60777 14 7.43124 13.9649 7.26656 13.8966C7.10188 13.8283 6.95228 13.7283 6.82629 13.6021L1 7.78264V1H7.78264L13.6089 6.82629C13.8616 7.08045 14.0034 7.42427 14.0034 7.78264C14.0034 8.14102 13.8616 8.48483 13.6089 8.73899Z" stroke="black" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                        {{ $tag }}
                                    </span>
                                    @endif
                                    @if ($post->read_time)
                                      <span>{{ $post->read_time }}</span>
                                    @endif
                                </div>
                                <h4 class="postbox-title fs-54">{{ $post->title }}</h4>
                                <div class="postbox-details-meta d-flex align-items-center">
                                    {{--<div class="postbox-author-box d-flex align-items-center ">
                                        <div class="postbox-author-img">
                                            <img src="{{ $post->author_photo_url ?: $defaultAuthor }}" alt="{{ $post->author_name }}">
                                        </div>
                                        <div class="postbox-author-info">
                                            <h4 class="postbox-author-name">{{ $post->author_name }}</h4>
                                        </div>
                                    </div>--}}
                                    <div class="postbox-meta">
                                        <i>
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 4.19997V8.99997L12.2 10.6M17 9C17 13.4183 13.4183 17 9 17C4.58172 17 1 13.4183 1 9C1 4.58172 4.58172 1 9 1C13.4183 1 17 4.58172 17 9Z" stroke="black" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                        <span>{{ optional($post->published_at)->translatedFormat('j F Y') }}</span>
                                    </div>
                                    {{--<div class="postbox-meta">
                                        <i>
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17 8.55557C17.003 9.72878 16.7289 10.8861 16.2 11.9333C15.5728 13.1882 14.6086 14.2437 13.4155 14.9816C12.2223 15.7195 10.8473 16.1106 9.44443 16.1111C8.27122 16.1142 7.11387 15.8401 6.06666 15.3111L1 17L2.68889 11.9333C2.15994 10.8861 1.88583 9.72878 1.88889 8.55557C1.88943 7.15269 2.28054 5.77766 3.01841 4.58451C3.75629 3.39135 4.81178 2.42719 6.06666 1.80002C7.11387 1.27107 8.27122 0.996966 9.44443 1.00003H9.88887C11.7416 1.10224 13.4916 1.88426 14.8037 3.19634C16.1157 4.50843 16.8978 6.25837 17 8.11113V8.55557Z" stroke="black" stroke-opacity="0.6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </i>
                                        <span>{{ __('frontend.blog.comments_count', ['count' => 0]) }}</span>
                                    </div>--}}
                                </div>
                            </div>
                        </article>
                        <div class="postbox-details-thumb mb-45">
                            <img class="w-100" src="{{ $post->cover_url ?: $defaultCover }}" alt="{{ $post->title }}">
                        </div>
                        @if ($post->content)
                          <div class="postbox-details-text mb-45">
                              {!! nl2br(e($post->content)) !!}
                          </div>
                        @endif
                        @if ($post->quote)
                        <div class="postbox-details-quote-box mb-45">
                            <blockquote>
                                <div class="postbox-details-quote-box d-flex align-items-start">
                                    <i>
                                        <svg width="63" height="50" viewBox="0 0 63 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M36.7657 50V39.1608C42.0105 38.4615 45.507 36.7133 47.2552 33.9161C49.0035 31.0023 49.8776 26.9231 49.8776 21.6783L58.4441 23.4266H37.1154V0H62.6399V17.4825C62.6399 25.8741 60.542 33.042 56.3461 38.986C52.2669 44.9301 45.7401 48.6014 36.7657 50ZM0 50V39.1608C5.24476 38.4615 8.74126 36.7133 10.4895 33.9161C12.2378 31.0023 13.1119 26.9231 13.1119 21.6783L21.6783 23.4266H0.34965V0H25.8741V17.4825C25.8741 25.8741 23.7762 33.042 19.5804 38.986C15.5012 44.9301 8.97436 48.6014 0 50Z" fill="black" fill-opacity="0.1" />
                                        </svg>
                                    </i>
                                    <div class="postbox-details-quote">
                                        <p>“{{ $post->quote }}”</p>
                                        {{--@if ($post->quote_author)
                                          <span>{{ $post->quote_author }}</span>
                                        @endif--}}
                                    </div>
                                </div>
                            </blockquote>
                        </div>
                        @endif

                        <div class="postbox-details-tag-wrap d-flex justify-content-between align-items-center">
                            <div class="postbox-details-tag d-flex align-items-center mb-0">
                                <span>{{ __('frontend.blog.tags') }}:</span>
                                <div class="tagcloud">
                                    @forelse ($post->tagList() as $itemTag)
                                      <a href="{{ route('frontend.blog', ['q' => $itemTag]) }}">{{ $itemTag }}</a>
                                    @empty
                                      @if ($post->category)
                                        <a href="{{ route('frontend.blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                                      @endif
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @if ($post->author_name)
                        {{--<div class="postbox-details-author mt-30">
                            <div class="sidebar-widget-author d-flex align-items-start">
                                <div class="sidebar-widget-author-img">
                                    <img src="{{ $post->author_photo_url ?: $defaultAuthor }}" alt="{{ $post->author_name }}">
                                </div>
                                <div class="postbox-details-content">
                                    <div class="sidebar-widget-author-content">
                                        <span>{{ __('frontend.blog.about_author') }}</span>
                                        <h4 class="sidebar-widget-author-name">{{ $post->author_name }}</h4>
                                        @if ($post->author_bio)
                                          <p>{{ $post->author_bio }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                        @endif
                    </div>
                </div>
                @include('frontend.blog.partials.sidebar')
            </div>
        </div>

        @if ($next)
        <div class="container container-1750">
            <div class="row">
                <div class="col-xl-12">
                    <a href="{{ route('frontend.blog.show', $next->slug) }}">
                        <div class="postbox-details-nevigation-wrap p-relative">
                            <div class="postbox-details-nevigation-thumb-bg">
                                <div class="postbox-details-nevigation-thumb">
                                    <img data-speed=".8" src="{{ $next->cover_url ?: $defaultCover }}" alt="{{ $next->title }}">
                                </div>
                            </div>
                            <div class="postbox-details-nevigation-content">
                                <span>{{ __('frontend.blog.next_article') }}</span>
                                <h4 class="postbox-details-nevigation-title">{{ $next->title }}</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{--<div class="container container-1330">
            <div class="row">
                <div class="col-xl-8">
                    <div class="postbox__comment pt-115 pb-50">
                        <h3 class="postbox__comment-title">{{ __('frontend.blog.comments_heading', ['count' => '00']) }}</h3>
                    </div>
                    <div class="postbox-details-form">
                        <h3 class="postbox-details-form-title">{{ __('frontend.blog.leave_comment') }}</h3>
                        <p>{{ __('frontend.blog.comment_note') }}</p>
                        <div class="postbox-details-form-wrapper">
                            <div class="postbox-details-form-inner">
                                <form action="#" method="get" onsubmit="return false;">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="postbox-details-input-box">
                                                <div class="postbox-details-input">
                                                    <label>{{ __('frontend.blog.comment_name') }} *</label>
                                                    <input type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="postbox-details-input-box">
                                                <div class="postbox-details-input">
                                                    <label>{{ __('frontend.blog.comment_email') }} *</label>
                                                    <input type="email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="postbox-details-input-box">
                                                <div class="postbox-details-input">
                                                    <label>{{ __('frontend.blog.comment_website') }}</label>
                                                    <input type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="postbox-details-input-box">
                                                <div class="postbox-details-input">
                                                    <label>{{ __('frontend.blog.comment_body') }} *</label>
                                                    <textarea id="msg"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="postbox-details-input-box">
                                <button class="tp-btn-yellow-green sidebar-btn-style sidebar-btn" type="button">
                                    <span>
                                        <span class="text-1">{{ __('frontend.blog.post_comment') }}</span>
                                        <span class="text-2">{{ __('frontend.blog.post_comment') }}</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}
    </section>

    {{--@include('frontend.blog.partials.cta')--}}
</main>
@endsection
