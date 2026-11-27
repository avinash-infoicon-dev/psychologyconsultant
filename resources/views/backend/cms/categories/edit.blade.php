@extends('backend.layouts.app')

@section('title', __('backend.cms.categories.edit'))
@section('page_title', __('backend.cms.categories.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.categories.index') }}">{{ __('backend.menu.cms_categories') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    <div class="adm__card">
      <form action="{{ route('admin.cms.categories.update', $category) }}" method="post">
        @csrf
        @method('PUT')
        <div class="adm__card-head"><h2 class="adm__card-title"><i class="bi bi-folder2-open" aria-hidden="true"></i>{{ __('backend.cms.categories.edit') }}</h2></div>
        <div class="adm__card-body adm-form">
          @include('backend.cms.categories._form')
        </div>
        <div class="adm__card-foot">
          <button type="submit" class="adm__btn adm__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.cms.categories.index') }}" class="adm__btn adm__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
@endsection
