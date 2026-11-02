@extends('backend.layouts.app')

@section('title', __('backend.cms.menus.create'))
@section('page_title', __('backend.cms.menus.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.menus.index') }}">{{ __('backend.menu.cms_menus') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.menus.store') }}" method="post">
      @csrf
      <div class="card-body">
        @include('backend.cms.menus._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.menus.index', ['location' => $location]) }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
