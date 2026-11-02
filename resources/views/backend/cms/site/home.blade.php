@extends('backend.layouts.app')
@section('title', __('backend.cms.pages.home_edit'))
@section('page_title', __('backend.cms.pages.home_edit'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.cms.pages.index') }}">{{ __('backend.menu.cms_pages') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_home') }}</li>
@endsection
@section('content')
  @php
    $activeLocale = in_array(app()->getLocale(), $locales, true) ? app()->getLocale() : ($locales[0] ?? 'en');
  @endphp
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.cms.home.update') }}" method="post" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="card-body">
        <p class="text-muted">{{ __('backend.cms.pages.home_help') }}</p>

        <ul class="nav nav-tabs" role="tablist">
          @foreach ($locales as $locale)
            <li class="nav-item" role="presentation">
              <button class="nav-link @if($locale === $activeLocale) active @endif" type="button" role="tab"
                data-bs-toggle="tab" data-bs-target="#home-locale-{{ $locale }}">
                {{ \App\Support\Locales::label($locale) }}
              </button>
            </li>
          @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3">
          @foreach ($locales as $locale)
            @php($tr = $page->translations->firstWhere('locale', $locale))
            <div class="tab-pane @if($locale === $activeLocale) show active @endif" id="home-locale-{{ $locale }}" role="tabpanel">
              @include('backend.cms.pages._home_layout', ['locale' => $locale, 'tr' => $tr, 'includePageFields' => true])
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
