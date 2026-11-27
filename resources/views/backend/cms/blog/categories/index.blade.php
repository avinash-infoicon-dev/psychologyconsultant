@extends('backend.layouts.app')

@section('title', __('backend.cms.blog.categories.title'))
@section('page_title', __('backend.cms.blog.categories.title'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.menu.blog') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.menu.blog_categories') }}</li>
@endsection

@push('styles')
  @include('backend.cms.blog._modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('status');
  @endphp

  <div class="cms-blog">
    {{-- ================= Hero ================= --}}
    <section class="cms-blog__hero">
      <div class="cms-blog__heading">
        <span class="cms-blog__hero-icon" aria-hidden="true"><i class="bi bi-tags"></i></span>
        <h1 class="cms-blog__title">{{ __('backend.cms.blog.categories.title') }}</h1>
      </div>
      <div class="cms-blog__actions">
        @permission('cms.blog.categories.create')
          <a href="{{ route('admin.blog.categories.create') }}" class="cms-blog__btn cms-blog__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            {{ __('backend.cms.blog.categories.create') }}
          </a>
        @endpermission
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="cms-blog__card cms-blog__filters" aria-labelledby="blog-cat-filter-title">
      <div class="cms-blog__filters-head">
        <h2 class="cms-blog__filters-title" id="blog-cat-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          {{ __('backend.search') }}
        </h2>
        @if ($hasFilters)
          <a class="cms-blog__clear" href="{{ route('admin.blog.categories.index') }}">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            {{ __('backend.logs.clear_filters') }}
          </a>
        @endif
      </div>

      <form method="get" class="cms-blog__filter-form" action="{{ route('admin.blog.categories.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
          placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
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
    <section class="cms-blog__card cms-blog__results" aria-labelledby="blog-cat-results-title">
      <div class="cms-blog__results-head">
        <h2 class="cms-blog__card-title" id="blog-cat-results-title">
          <i class="bi bi-list-check" aria-hidden="true"></i>
          {{ __('backend.menu.blog_categories') }}
        </h2>
        <span class="cms-blog__range">
          {{ __('backend.logs.showing', [
            'first' => number_format($categories->firstItem() ?? 0),
            'last' => number_format($categories->lastItem() ?? 0),
            'total' => number_format($categories->total()),
          ]) }}
        </span>
      </div>

      <div class="cms-blog__table-wrap">
        <table class="table cms-blog__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.slug') }}</th>
              <th>{{ __('backend.cms.blog.posts_count') }}</th>
              <th>{{ __('backend.cms.sort_order') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($categories as $category)
              <tr>
                <td><span class="cms-blog__name">{{ $category->name }}</span></td>
                <td><code class="cms-blog__slug">{{ $category->slug }}</code></td>
                <td class="cms-blog__num">{{ $category->posts_count }}</td>
                <td class="cms-blog__num">{{ $category->sort_order }}</td>
                <td>
                  @if ($category->is_active)
                    <span class="cms-blog__status cms-blog__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="cms-blog__status cms-blog__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  <div class="cms-blog__row-actions">
                    @permission('cms.blog.categories.edit')
                      <a href="{{ route('admin.blog.categories.edit', $category) }}" class="cms-blog__btn cms-blog__btn--ghost">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>{{ __('backend.edit') }}
                      </a>
                    @endpermission
                    @permission('cms.blog.categories.delete')
                      <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
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

      @if ($categories->hasPages())
        <div class="cms-blog__pagination">{{ $categories->links() }}</div>
      @endif
    </section>
  </div>
@endsection
