@extends('backend.layouts.app')

@section('title', __('backend.cms.sliders.create'))
@section('page_title', __('backend.cms.sliders.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.sliders.index') }}">{{ __('backend.menu.cms_sliders') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    <div class="adm__card">
      <form action="{{ route('admin.cms.sliders.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="adm__card-head"><h2 class="adm__card-title"><i class="bi bi-image" aria-hidden="true"></i>{{ __('backend.cms.sliders.create') }}</h2></div>
        <div class="adm__card-body adm-form">
          @include('backend.cms.sliders._form')
        </div>
        <div class="adm__card-foot">
          <button type="submit" class="adm__btn adm__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.cms.sliders.index') }}" class="adm__btn adm__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
