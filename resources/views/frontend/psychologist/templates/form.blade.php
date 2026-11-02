@extends('frontend.layouts.dashboard')

@section('title', $template ? __('frontend.templates.edit') : __('frontend.templates.create'))
@section('page_title', $template ? __('frontend.templates.edit') : __('frontend.templates.create'))

@section('content')
  <div class="fe-cms-card">
    <h2 style="margin-top:0;">{{ $template ? __('frontend.templates.edit') : __('frontend.templates.create') }}</h2>

    <form
      method="post"
      action="{{ $template
        ? route('frontend.psychologist.templates.update', $template)
        : route('frontend.psychologist.templates.store') }}"
    >
      @csrf
      @if ($template)
        @method('PUT')
      @endif

      <div class="fe-form-group">
        <label for="title">{{ __('frontend.templates.name') }}</label>
        <input type="text" name="title" id="title" class="fe-input" value="{{ old('title', $template?->title) }}" required maxlength="255">
      </div>
      <div class="fe-form-group">
        <label for="body">{{ __('frontend.templates.body') }}</label>
        <textarea name="body" id="body" class="fe-input" rows="8" required maxlength="5000">{{ old('body', $template?->body) }}</textarea>
      </div>
      <div class="fe-form-group">
        <label for="sort_order">{{ __('frontend.templates.sort_order') }}</label>
        <input type="number" min="0" name="sort_order" id="sort_order" class="fe-input" value="{{ old('sort_order', $template?->sort_order ?? 0) }}">
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $template?->is_active ?? true))>
        <label class="form-check-label" for="is_active">{{ __('frontend.active') }}</label>
      </div>

      <div class="fe-form-actions">
        <a href="{{ route('frontend.psychologist.templates.index') }}" class="fe-btn fe-btn--outline">{{ __('frontend.cancel') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
