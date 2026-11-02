@extends('backend.layouts.app')

@section('title', __('backend.dashboard_pages.admin_title'))
@section('page_title', __('backend.dashboard_pages.admin_title'))

@section('content')
  <div class="alert alert-success">
    <h5 class="mb-1">{{ __('backend.welcome') }}, {{ $user->name }}</h5>
    {{--<p class="mb-0">{{ __('backend.dashboard_pages.admin_subtitle') }}</p>--}}
  </div>

  {{--<div class="alert alert-success alert-dismissible fade show" role="alert">
    <h5 class="mb-1">{{ __('backend.welcome') }}, {{ $user->name }}</h5>
    <p class="mb-0">{{ __('backend.dashboard_pages.admin_subtitle') }}</p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>--}}

  @include('backend.dashboard.partials.admin-stats')

  {{-- Previous dashboard cards
  <div class="row">
    <div class="col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary"><i class="bi bi-people"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ __('backend.dashboard_pages.total_users') }}</span>
          <span class="info-box-number">{{ $stats['patients'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary"><i class="bi bi-people"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ __('backend.dashboard_pages.total_psychologists') }}</span>
          <span class="info-box-number">{{ $stats['Psychologists'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-primary"><i class="bi bi-people"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ __('backend.dashboard_pages.total_legal_advisors') }}</span>
          <span class="info-box-number">{{ $stats['Legal Advisors'] }}</span>
        </div>
      </div>
    </div>
  
    <div class="col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-success"><i class="bi bi-shield-check"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ __('backend.dashboard_pages.total_roles') }}</span>
          <span class="info-box-number">{{ $stats['roles'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="info-box">
        <span class="info-box-icon text-bg-warning"><i class="bi bi-person-check"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ __('backend.dashboard_pages.active_users') }}</span>
          <span class="info-box-number">{{ $stats['active_users'] }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-outline card-success">
    <div class="card-header">
      <h3 class="card-title">{{ __('backend.dashboard_pages.your_role') }}: Admin</h3>
    </div>
    <div class="card-body">
      <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">{{ __('backend.menu.users') }}</a>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">{{ __('backend.menu.roles') }}</a>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">{{ __('backend.menu.permissions') }}</a>
      </div>
    </div>
  </div>
  --}}
@endsection
