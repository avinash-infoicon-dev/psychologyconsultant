@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.posts.edit'))
@section('page_title', __('backend.cms.blog.posts.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.cms.blog.posts.title') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.blog.posts.update', $post) }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">@include('backend.cms.blog.posts._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
