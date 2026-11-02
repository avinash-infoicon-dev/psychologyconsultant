@extends('backend.layouts.app')

@section('title', __('backend.cms.menus.edit'))
@section('page_title', __('backend.cms.menus.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.menus.index') }}">{{ __('backend.menu.cms_menus') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.menus.update', $menu) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        @include('backend.cms.menus._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.menus.index', ['location' => $menu->location]) }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
