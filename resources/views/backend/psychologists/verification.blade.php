@extends('backend.layouts.app')

@section('title', __('backend.psychologists.section_verification'))
@section('page_title', __('backend.psychologists.section_verification'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.psychologists.index') }}">{{ __('backend.menu.psychologists') }}</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.psychologists.verifications.index') }}">{{ __('backend.psychologists.section_verification') }}</a></li>
  <li class="breadcrumb-item active">{{ $professional->name }}</li>
@endsection

@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('admin.psychologists.show', $psychologist) }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.psychologists.view_details') }}
    </a>
    <a href="{{ route('admin.psychologists.verifications.index') }}" class="btn btn-outline-primary btn-sm">
      {{ __('backend.psychologists.section_verification') }}
    </a>
  </div>

  <div class="card mb-3 overflow-hidden">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center gap-3">
        <img
          src="{{ $professional->profile_image_url }}"
          alt="{{ $professional->name }}"
          class="rounded-circle border"
          style="width:64px;height:64px;object-fit:cover;background:#fff;"
        >
        <div>
          <h3 class="mb-1">{{ trim(($professional->title ? $professional->title.' ' : '').$professional->name) }}</h3>
          <p class="text-muted mb-0">{{ $professional->specialization ?: $professional->professionalType?->name ?: '—' }}</p>
        </div>
      </div>
    </div>
  </div>

  @include('backend.psychologists._verification_card')
@endsection
