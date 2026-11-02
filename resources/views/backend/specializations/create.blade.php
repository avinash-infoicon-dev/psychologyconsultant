@extends('backend.layouts.app')

@section('title', __('backend.specializations.create'))
@section('page_title', __('backend.specializations.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.specializations.index') }}">{{ __('backend.menu.specializations') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.specializations.store') }}" method="post">
      @csrf
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
