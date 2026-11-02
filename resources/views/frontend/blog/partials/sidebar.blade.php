@php
  $defaultThumb = asset('frontendassets/assets/img/blog/blog-1.webp');
@endphp
<div class="col-xxl-4 col-xl-4 col-lg-4">
    <div class="sidebar-wrapper">
        <div class="sidebar-widget mb-30">
            <div class="sidebar-search">
                <form action="{{ route('frontend.blog') }}" method="get">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('frontend.blog.search_placeholder') }}">
                    <button type="submit">
                        <i class="fa-regular fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="sidebar-widget mb-55">
            <h3 class="sidebar-widget-title">{{ __('frontend.blog.recent_posts') }}</h3>
            <div class="rc-post-wrap">
                @forelse ($recentPosts as $recent)
                    <div class="rc-post d-flex align-items-center">
                        <div class="rc-post-thumb">
                            <a href="{{ route('frontend.blog.show', $recent->slug) }}">
                                <img src="{{ $recent->cover_url ?: $defaultThumb }}" alt="{{ $recent->title }}">
                            </a>
                        </div>
                        <div class="rc-post-content">
                            @if ($recent->category)
                                <div class="rc-post-category">
                                    <a href="{{ route('frontend.blog.category', $recent->category->slug) }}">{{ $recent->category->name }}</a>
                                </div>
                            @endif
                            <h3 class="rc-post-title">
                                <a href="{{ route('frontend.blog.show', $recent->slug) }}">{{ $recent->title }}</a>
                            </h3>
                            <div class="rc-post-meta">
                                <span>{{ optional($recent->published_at)->translatedFormat('F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="mb-0">{{ __('frontend.blog.empty') }}</p>
                @endforelse
            </div>
        </div>
        <div class="sidebar-widget mb-55">
            <h3 class="sidebar-widget-title">{{ __('frontend.blog.categories') }}</h3>
            <div class="sidebar-widget-category">
                <ul>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('frontend.blog.category', $category->slug) }}" class="d-flex justify-content-between">
                                <span>{{ $category->name }}</span>
                                <span>{{ str_pad((string) $category->posts_count, 2, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
