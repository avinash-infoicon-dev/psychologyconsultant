@extends('backend.layouts.app')

@section('title', __('backend.dashboard_pages.superadmin_title'))
@section('page_title', __('backend.dashboard_pages.superadmin_title'))

@section('content')
  <div class="alert alert-primary">
    <h5 class="mb-1">{{ __('backend.welcome') }}, {{ $user->name }}</h5>
    {{--<p class="mb-0">{{ __('backend.dashboard_pages.superadmin_subtitle') }}</p>--}}
  </div>

  @include('backend.dashboard.partials.admin-stats')

  {{-- Previous dashboard cards
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box text-bg-primary">
        <div class="inner">
          <h3>{{ $stats['patients'] }}</h3>
          <p>{{ __('backend.dashboard_pages.total_users') }}</p>
        </div>
      </div>
    </div>
  </div>
  --}}
@endsection
