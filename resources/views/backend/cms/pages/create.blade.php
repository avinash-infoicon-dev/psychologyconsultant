@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.create'))
@section('page_title', __('backend.cms.pages.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.pages.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        @include('backend.cms.pages._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
