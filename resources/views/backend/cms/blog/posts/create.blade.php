@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.posts.create'))
@section('page_title', __('backend.cms.blog.posts.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">{{ __('backend.cms.blog.posts.title') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.blog.posts.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">@include('backend.cms.blog.posts._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
