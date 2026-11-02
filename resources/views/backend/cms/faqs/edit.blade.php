@extends('backend.layouts.app')
@section('title', __('backend.cms.faqs.edit'))
@section('page_title', __('backend.cms.faqs.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.faqs.index') }}">{{ __('backend.menu.cms_faqs') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.faqs.update', $faq) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">@include('backend.cms.faqs._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.faqs.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
