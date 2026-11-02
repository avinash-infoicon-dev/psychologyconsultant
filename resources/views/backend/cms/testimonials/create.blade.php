@extends('backend.layouts.app')
@section('title', __('backend.cms.testimonials.create'))
@section('page_title', __('backend.cms.testimonials.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.testimonials.index') }}">{{ __('backend.menu.cms_testimonials') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.testimonials.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">@include('backend.cms.testimonials._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.testimonials.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
