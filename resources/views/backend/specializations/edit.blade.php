@extends('backend.layouts.app')

@section('title', __('backend.specializations.edit'))
@section('page_title', __('backend.specializations.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.specializations.index') }}">{{ __('backend.menu.specializations') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.specializations.update', $specialization) }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        @include('backend.specializations._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.specializations.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
