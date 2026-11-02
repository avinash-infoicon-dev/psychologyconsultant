@extends('frontend.layouts.guest')

@section('title', $category->name)

@section('content')
  <section class="fe-page-hero">
    <div class="fe-container">
      <nav class="fe-breadcrumb">
        <a href="{{ route('frontend.home') }}">{{ __('frontend.nav.home') }}</a>
        <span>/</span>
        <span>{{ $category->name }}</span>
      </nav>
      <h1>{{ $category->name }}</h1>
      @if ($category->description)
        <p class="fe-page-lead">{{ $category->description }}</p>
      @endif
    </div>
  </section>

  <section class="fe-section" style="padding-top: 0;">
    <div class="fe-container">
      <div class="fe-cms-list">
        @forelse ($pages as $page)
          <a href="{{ route('frontend.cms.pages.show', $page->slug) }}" class="fe-cms-list__item">
            <div>
              <h3>{{ $page->title }}</h3>
              @if ($page->meta_description)
                <p>{{ \Illuminate\Support\Str::limit($page->meta_description, 140) }}</p>
              @endif
            </div>
            <i class="bi bi-arrow-right"></i>
          </a>
        @empty
          <div class="fe-cms-card">
            <p style="margin:0;color:var(--fe-muted);">{{ __('backend.no_records') }}</p>
          </div>
        @endforelse
      </div>

      @if ($pages->hasPages())
        <div class="fe-pagination">{{ $pages->links() }}</div>
      @endif
    </div>
  </section>
@endsection
