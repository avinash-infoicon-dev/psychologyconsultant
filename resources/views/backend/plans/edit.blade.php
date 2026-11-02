@extends('backend.layouts.app')

@section('title', __('backend.plans.edit'))
@section('page_title', __('backend.plans.edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.plans.index') }}">{{ __('backend.menu.plans') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.edit') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.plans.update', $plan) }}" method="post">
      @csrf
      @method('PUT')
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
