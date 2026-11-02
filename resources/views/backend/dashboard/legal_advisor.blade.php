@extends('backend.layouts.app')

@section('title', __('backend.dashboard_pages.legal_title'))
@section('page_title', __('backend.dashboard_pages.legal_title'))

@section('content')
  <div class="alert alert-warning">
    <h5 class="mb-1">{{ __('backend.welcome') }}, {{ $user->name }}</h5>
    <p class="mb-0">{{ __('backend.dashboard_pages.legal_subtitle') }}</p>
  </div>

  <div class="card">
    <div class="card-body">
      <p class="mb-1"><strong>{{ __('backend.dashboard_pages.your_role') }}:</strong> {{ $user->primaryRole()?->name }}</p>
      <p class="text-secondary mb-0">{{ __('backend.dashboard_pages.legal_subtitle') }}</p>
    </div>
  </div>
@endsection
