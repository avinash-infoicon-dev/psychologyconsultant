@extends('backend.layouts.app')

@section('title', __('backend.cms.categories.edit'))
@section('page_title', __('backend.cms.categories.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.categories.index') }}">{{ __('backend.menu.cms_categories') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.categories.update', $category) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        @include('backend.cms.categories._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.categories.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
