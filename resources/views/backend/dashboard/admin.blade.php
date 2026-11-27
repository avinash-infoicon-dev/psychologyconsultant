@extends('backend.layouts.app')

@section('title', __('backend.dashboard_pages.admin_title'))
@section('page_title', __('backend.dashboard_pages.admin_title'))

@section('content')
  <div class="admin-dashboard">
    <section class="admin-dashboard__hero">
      <span class="admin-dashboard__hero-icon" aria-hidden="true"><i class="bi bi-grid-1x2"></i></span>
      <div>
        <h1 class="admin-dashboard__welcome">{{ __('backend.welcome') }}, {{ $user->name }}</h1>
        <p class="admin-dashboard__subtitle">{{ __('backend.dashboard_pages.admin_subtitle') }}</p>
      </div>
    </section>
    @include('backend.dashboard.partials.admin-stats')
  </div>
@endsection
