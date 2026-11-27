@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.posts.edit'))
@section('page_title', __('backend.cms.blog.posts.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.cms.blog.posts.title') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@push('styles')
  @include('backend.cms.blog._modern-styles')
@endpush

@section('content')
  <div class="cms-blog">
    <div class="cms-blog__card">
      <form action="{{ route('admin.blog.posts.update', $post) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="cms-blog__card-head">
          <h2 class="cms-blog__card-title"><i class="bi bi-pencil-square" aria-hidden="true"></i>{{ __('backend.cms.blog.posts.edit') }}</h2>
        </div>
        <div class="cms-blog__card-body cms-blog-form">
          @include('backend.cms.blog.posts._form')
        </div>
        <div class="cms-blog__card-foot">
          <button type="submit" class="cms-blog__btn cms-blog__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.blog.posts.index') }}" class="cms-blog__btn cms-blog__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
