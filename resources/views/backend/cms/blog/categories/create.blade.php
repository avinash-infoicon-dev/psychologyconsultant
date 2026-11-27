@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.categories.create'))
@section('page_title', __('backend.cms.blog.categories.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">{{ __('backend.menu.blog_categories') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@push('styles')
  @include('backend.cms.blog._modern-styles')
@endpush

@section('content')
  <div class="cms-blog">
    <div class="cms-blog__card">
      <form action="{{ route('admin.blog.categories.store') }}" method="post">
        @csrf
        <div class="cms-blog__card-head">
          <h2 class="cms-blog__card-title"><i class="bi bi-tag" aria-hidden="true"></i>{{ __('backend.cms.blog.categories.create') }}</h2>
        </div>
        <div class="cms-blog__card-body cms-blog-form">
          @include('backend.cms.blog.categories._form')
        </div>
        <div class="cms-blog__card-foot">
          <button type="submit" class="cms-blog__btn cms-blog__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.blog.categories.index') }}" class="cms-blog__btn cms-blog__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
