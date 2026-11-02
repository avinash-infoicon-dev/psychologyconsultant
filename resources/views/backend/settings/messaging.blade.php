@extends('backend.layouts.app')

@section('title', __('backend.settings.messaging.title'))
@section('page_title', __('backend.settings.messaging.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.settings.messaging.title') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.settings.messaging.update') }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        {{--<p class="text-muted">{{ __('backend.settings.messaging.help') }}</p>
        <p class="text-muted small">{{ __('backend.settings.messaging.zero_help') }}</p>

        <h5 class="mt-3 mb-3">{{ __('backend.settings.messaging.patient_heading') }}</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="patient_max_text_chars">
              {{ __('backend.settings.messaging.max_text_chars') }}
            </label>
            <input
              type="number"
              min="0"
              max="100000"
              class="form-control @error('patient_max_text_chars') is-invalid @enderror"
              name="patient_max_text_chars"
              id="patient_max_text_chars"
              value="{{ old('patient_max_text_chars', $limits['patient']['max_text_chars']) }}"
              required
            >
            @error('patient_max_text_chars')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="patient_max_audio_seconds">
              {{ __('backend.settings.messaging.max_audio_seconds') }}
            </label>
            <input
              type="number"
              min="0"
              max="3600"
              class="form-control @error('patient_max_audio_seconds') is-invalid @enderror"
              name="patient_max_audio_seconds"
              id="patient_max_audio_seconds"
              value="{{ old('patient_max_audio_seconds', $limits['patient']['max_audio_seconds']) }}"
              required
            >
            @error('patient_max_audio_seconds')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <h5 class="mt-4 mb-3">{{ __('backend.settings.messaging.psychologist_heading') }}</h5>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="psychologist_max_text_chars">
              {{ __('backend.settings.messaging.max_text_chars') }}
            </label>
            <input
              type="number"
              min="0"
              max="100000"
              class="form-control @error('psychologist_max_text_chars') is-invalid @enderror"
              name="psychologist_max_text_chars"
              id="psychologist_max_text_chars"
              value="{{ old('psychologist_max_text_chars', $limits['psychologist']['max_text_chars']) }}"
              required
            >
            @error('psychologist_max_text_chars')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="psychologist_max_audio_seconds">
              {{ __('backend.settings.messaging.max_audio_seconds') }}
            </label>
            <input
              type="number"
              min="0"
              max="3600"
              class="form-control @error('psychologist_max_audio_seconds') is-invalid @enderror"
              name="psychologist_max_audio_seconds"
              id="psychologist_max_audio_seconds"
              value="{{ old('psychologist_max_audio_seconds', $limits['psychologist']['max_audio_seconds']) }}"
              required
            >
            @error('psychologist_max_audio_seconds')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>--}}

        {{--<h5 class="mt-2 mb-3">{{ __('backend.settings.messaging.messages_per_exchange_heading') }}</h5>
        <p class="text-muted small">{{ __('backend.settings.messaging.messages_per_exchange_help') }}</p>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="patient_messages_per_exchange">
              {{ __('backend.settings.messaging.patient_messages_per_exchange') }}
            </label>
            <input
              type="number"
              min="0"
              class="form-control @error('patient_messages_per_exchange') is-invalid @enderror"
              name="patient_messages_per_exchange"
              id="patient_messages_per_exchange"
              value="{{ old('patient_messages_per_exchange', $limits['patient']['messages_per_exchange']) }}"
              required
            >
            @error('patient_messages_per_exchange')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="psychologist_messages_per_exchange">
              {{ __('backend.settings.messaging.psychologist_messages_per_exchange') }}
            </label>
            <input
              type="number"
              min="0"
              class="form-control @error('psychologist_messages_per_exchange') is-invalid @enderror"
              name="psychologist_messages_per_exchange"
              id="psychologist_messages_per_exchange"
              value="{{ old('psychologist_messages_per_exchange', $limits['psychologist']['messages_per_exchange']) }}"
              required
            >
            @error('psychologist_messages_per_exchange')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>--}}

        <hr class="mt-2">
        <h5 class="mt-4 mb-2">{{ __('backend.settings.messaging.audio_files_heading') }}</h5>
        <p class="text-muted small">{{ __('backend.settings.messaging.audio_files_help') }}</p>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="audio_max_upload_mb">
              {{ __('backend.settings.messaging.max_upload_mb') }}
            </label>
            <input
              type="number"
              min="1"
              max="100"
              class="form-control @error('audio_max_upload_mb') is-invalid @enderror"
              name="audio_max_upload_mb"
              id="audio_max_upload_mb"
              value="{{ old('audio_max_upload_mb', $audio['max_upload_mb']) }}"
              required
            >
            <div class="form-text">{{ __('backend.settings.messaging.max_upload_mb_help') }}</div>
            @error('audio_max_upload_mb')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <span class="form-label d-block">{{ __('backend.settings.messaging.allowed_formats') }}</span>
            @php
              $selectedFormats = old('audio_formats', $audio['selected_formats']);
            @endphp
            <div class="@error('audio_formats') is-invalid @enderror">
              @foreach ($audio['formats'] as $formatKey => $format)
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    name="audio_formats[]"
                    id="audio_format_{{ $formatKey }}"
                    value="{{ $formatKey }}"
                    @checked(in_array($formatKey, $selectedFormats, true))
                  >
                  <label class="form-check-label" for="audio_format_{{ $formatKey }}">
                    {{ $format['label'] }}
                    <span class="text-muted">({{ implode(', ', $format['extensions']) }})</span>
                  </label>
                </div>
              @endforeach
            </div>
            <div class="form-text">{{ __('backend.settings.messaging.allowed_formats_help') }}</div>
            @error('audio_formats')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @error('audio_formats.*')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
