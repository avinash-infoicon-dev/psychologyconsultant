@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.edit'))
@section('page_title', __('backend.cms.pages.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.pages.update', $page) }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        @include('backend.cms.pages._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
  <div class="card mt-3">
      <div class="card-header">
        <h3 class="card-title mb-0">{{ __('backend.cms.pages.page_sections') }}</h3>
      </div>
      <div class="card-body">
        <p class="text-muted mb-3">{{ __('backend.cms.pages.sections_help') }}</p>
        <a href="{{ route('admin.cms.pages.sections', $page) }}" class="btn btn-outline-primary">{{ __('backend.cms.pages.sections') }}</a>
      </div>
    </div>
@endsection
