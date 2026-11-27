@extends('backend.layouts.app')

@section('title', __('backend.cms.menus.edit'))
@section('page_title', __('backend.cms.menus.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.menus.index') }}">{{ __('backend.menu.cms_menus') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    <div class="adm__card">
      <form action="{{ route('admin.cms.menus.update', $menu) }}" method="post">
        @csrf
        @method('PUT')
        <div class="adm__card-head"><h2 class="adm__card-title"><i class="bi bi-list" aria-hidden="true"></i>{{ __('backend.cms.menus.edit') }}</h2></div>
        <div class="adm__card-body adm-form">
          @include('backend.cms.menus._form')
        </div>
        <div class="adm__card-foot">
          <button type="submit" class="adm__btn adm__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.cms.menus.index', ['location' => $menu->location]) }}" class="adm__btn adm__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
