@extends('backend.layouts.app')

@section('title', __('backend.cms.sliders.create'))
@section('page_title', __('backend.cms.sliders.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.sliders.index') }}">{{ __('backend.menu.cms_sliders') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.sliders.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
        @include('backend.cms.sliders._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.sliders.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
