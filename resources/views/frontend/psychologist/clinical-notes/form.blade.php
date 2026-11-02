@extends('frontend.layouts.dashboard')

@section('title', $note ? __('frontend.clinical_notes.edit') : __('frontend.clinical_notes.create'))
@section('page_title', $note ? __('frontend.clinical_notes.edit') : __('frontend.clinical_notes.create'))

@section('content')
  <div class="fe-cms-card">
    <h2 style="margin-top:0;">{{ $note ? __('frontend.clinical_notes.edit') : __('frontend.clinical_notes.create') }}</h2>
    <p class="fe-muted">{{ $patient->user?->name }}</p>

    <form
      method="post"
      action="{{ $note
        ? route('frontend.psychologist.clinical-notes.update', $note)
        : route('frontend.psychologist.patients.clinical-notes.store', $patient) }}"
    >
      @csrf
      @if ($note)
        @method('PUT')
      @endif

      @if ($conversationId)
        <input type="hidden" name="conversation_id" value="{{ $conversationId }}">
      @endif

      <div class="fe-form-group">
        <label for="content">{{ __('frontend.clinical_notes.content') }}</label>
        <textarea name="content" id="content" class="fe-input" rows="10" required maxlength="20000">{{ old('content', $note?->content) }}</textarea>
        @error('content') <div class="fe-alert fe-alert--error">{{ $message }}</div> @enderror
      </div>

      <div class="fe-form-actions">
        <a href="{{ route('frontend.psychologist.patients.clinical-notes.index', $patient) }}" class="fe-btn fe-btn--outline">{{ __('frontend.cancel') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
