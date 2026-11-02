@extends('backend.layouts.app')
@section('title', __('backend.cms.blog.categories.create'))
@section('page_title', __('backend.cms.blog.categories.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">{{ __('backend.menu.blog_categories') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.blog.categories.store') }}" method="post">
      @csrf
      <div class="card-body">@include('backend.cms.blog.categories._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
