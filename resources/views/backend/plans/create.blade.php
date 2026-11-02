@extends('backend.layouts.app')

@section('title', __('backend.plans.create'))
@section('page_title', __('backend.plans.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.plans.index') }}">{{ __('backend.menu.plans') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.plans.store') }}" method="post">
      @csrf
      <div class="card-body">
        @include('backend.plans._form')
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
