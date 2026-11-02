@extends('backend.layouts.app')
@section('title', __('backend.cms.team.edit'))
@section('page_title', __('backend.cms.team.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.team.index') }}">{{ __('backend.menu.cms_team') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection
@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.team.update', $member) }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">@include('backend.cms.team._form')</div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.cms.team.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
