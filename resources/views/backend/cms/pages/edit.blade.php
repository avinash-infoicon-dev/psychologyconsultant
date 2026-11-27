@extends('backend.layouts.app')

@section('title', __('backend.cms.pages.edit'))
@section('page_title', __('backend.cms.pages.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  <div class="adm">
    <div class="adm__card">
      <form action="{{ route('admin.cms.pages.update', $page) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="adm__card-head"><h2 class="adm__card-title"><i class="bi bi-file-earmark-text" aria-hidden="true"></i>{{ __('backend.cms.pages.edit') }}</h2></div>
        <div class="adm__card-body adm-form">
          @include('backend.cms.pages._form')
        </div>
        <div class="adm__card-foot">
          <button type="submit" class="adm__btn adm__btn--primary"><i class="bi bi-check-lg" aria-hidden="true"></i>{{ __('backend.save') }}</button>
          <a href="{{ route('admin.cms.pages.index') }}" class="adm__btn adm__btn--secondary">{{ __('backend.cancel') }}</a>
        </div>
      </form>
    </div>

    <div class="adm__card adm__section">
      <div class="adm__card-head"><h3 class="adm__card-title"><i class="bi bi-stack" aria-hidden="true"></i>{{ __('backend.cms.pages.page_sections') }}</h3></div>
      <div class="adm__card-body">
        <p class="adm__subtitle mb-3">{{ __('backend.cms.pages.sections_help') }}</p>
        <a href="{{ route('admin.cms.pages.sections', $page) }}" class="adm__btn adm__btn--ghost"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.cms.pages.sections') }}</a>
      </div>
    </div>
  </div>
@endsection
