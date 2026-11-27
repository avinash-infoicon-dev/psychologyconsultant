@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.posts.title'))
@section('page_title', __('backend.cms.blog.posts.title'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.menu.blog') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.cms.blog.posts.title') }}</li>
@endsection

@push('styles')
  @include('backend.cms.blog._modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('category') || request()->filled('status');
  @endphp

  <div class="cms-blog">
    {{-- ================= Hero ================= --}}
    <section class="cms-blog__hero">
      <div class="cms-blog__heading">
        <span class="cms-blog__hero-icon" aria-hidden="true"><i class="bi bi-newspaper"></i></span>
        <h1 class="cms-blog__title">{{ __('backend.cms.blog.posts.title') }}</h1>
      </div>
      <div class="cms-blog__actions">
        @permission('cms.blog.posts.create')
          <a href="{{ route('admin.blog.posts.create') }}" class="cms-blog__btn cms-blog__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            {{ __('backend.cms.blog.posts.create') }}
          </a>
        @endpermission
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="cms-blog__card cms-blog__filters" aria-labelledby="blog-post-filter-title">
      <div class="cms-blog__filters-head">
        <h2 class="cms-blog__filters-title" id="blog-post-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </h2>
        @if ($hasFilters)
          <a class="cms-blog__clear" href="{{ route('admin.blog.posts.index') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="cms-blog__filter-form" action="{{ route('admin.blog.posts.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="category" class="form-select" aria-label="{{ __('backend.cms.blog.category') }}">
          <option value="">{{ __('backend.cms.blog.category') }}</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string) request('category') === (string) $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
        </select>
        <button class="cms-blog__btn cms-blog__btn--primary" type="submit">
          <i class="bi bi-search" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="cms-blog__card cms-blog__results" aria-labelledby="blog-post-results-title">
      <div class="cms-blog__results-head">
        <h2 class="cms-blog__card-title" id="blog-post-results-title">
          <i class="bi bi-list-check" aria-hidden="true"></i>
          {{ __('backend.cms.blog.posts.title') }}
        </h2>
        <span class="cms-blog__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($posts->firstItem() ?? 0),
            'last' => number_format($posts->lastItem() ?? 0),
            'total' => number_format($posts->total()),
          ]) }}
        </span>
      </div>

      <div class="cms-blog__table-wrap">
        <table class="table cms-blog__table">
          <thead>
            <tr>
              <th>{{ __('backend.cms.blog.posts.cover') }}</th>
              <th>{{ __('backend.cms.title') }}</th>
              <th>{{ __('backend.cms.blog.category') }}</th>
              <th>{{ __('backend.cms.published_at') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($posts as $item)
              <tr>
                <td>
                  @if ($item->cover_url)
                    <img src="{{ $item->cover_url }}" alt="" class="cms-blog__thumb">
                  @else
                    <span class="cms-blog__thumb-placeholder" aria-hidden="true"><i class="bi bi-image"></i></span>
                  @endif
                </td>
                <td>
                  <span class="cms-blog__name">{{ $item->title }}</span>
                  @if ($item->author_name)
                    <span class="cms-blog__sub">{{ $item->author_name }}</span>
                  @endif
                </td>
                <td>{{ $item->category?->name ?? '—' }}</td>
                <td class="cms-blog__num">{{ optional($item->published_at)->format('Y-m-d') ?: '—' }}</td>
                <td>
                  @if ($item->is_active)
                    <span class="cms-blog__status cms-blog__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="cms-blog__status cms-blog__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="cms-blog__row-actions">
                    @permission('cms.blog.posts.edit')
                      <a href="{{ route('admin.blog.posts.edit', $item) }}" class="cms-blog__btn cms-blog__btn--ghost">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>{{ __('backend.edit') }}
                      </a>
                    @endpermission
                    @permission('cms.blog.posts.delete')
                      <form action="{{ route('admin.blog.posts.destroy', $item) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cms-blog__btn cms-blog__btn--danger">
                          <i class="bi bi-trash3" aria-hidden="true"></i>{{ __('backend.delete') }}
                        </button>
                      </form>
                    @endpermission
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="cms-blog__empty">
                  <span class="cms-blog__empty-icon" aria-hidden="true"><i class="bi bi-journal-x"></i></span>
                  {{ __('backend.no_records') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($posts->hasPages())
        <div class="cms-blog__pagination">{{ $posts->links() }}</div>
      @endif
    </section>
  </div>
@endsection
