@extends('frontend.layouts.dashboard')

@section('title', __('frontend.psych_profile.title'))
@section('page_title', __('frontend.psych_profile.title'))

@php
  $tabs = [
    'overview' => __('frontend.psych_profile.tabs.overview'),
    'education' => __('frontend.psych_profile.tabs.education'),
    'experience' => __('frontend.psych_profile.tabs.experience'),
    'social' => __('frontend.psych_profile.tabs.social'),
    'gallery' => __('frontend.psych_profile.tabs.gallery'),
    'documents' => __('frontend.psych_profile.tabs.documents'),
    'awards' => __('frontend.psych_profile.tabs.awards'),
    'settings' => 'Settings',
  ];
  $activeTab = array_key_exists($tab, $tabs) ? $tab : 'overview';
@endphp

@section('content')
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ __('frontend.psych_profile.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ __('frontend.psych_profile.subtitle') }}</p>
      </div>
    </div>

    @include('frontend.psychologist.partials.verification-banner')

    <nav class="fe-profile-tabs" aria-label="{{ __('frontend.psych_profile.title') }}">
      @foreach ($tabs as $key => $label)
        <a href="{{ route('frontend.psychologist.profile.show', ['tab' => $key]) }}"
          class="fe-profile-tabs__item {{ $activeTab === $key ? 'is-active' : '' }}">
          {{ $label }}
        </a>
      @endforeach
    </nav>

    <div class="fe-profile-tab-panel">
      @include('frontend.psychologist.profile.tabs.'.$activeTab)
    </div>
  </div>
@endsection
