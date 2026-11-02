@extends('frontend.layouts.dashboard')

@section('title', $absence ? __('frontend.absences.edit') : __('frontend.absences.create'))
@section('page_title', $absence ? __('frontend.absences.edit') : __('frontend.absences.create'))

@section('content')
  <div class="fe-cms-card">
    <h2 style="margin-top:0;">{{ $absence ? __('frontend.absences.edit') : __('frontend.absences.create') }}</h2>

    <form
      method="post"
      action="{{ $absence
        ? route('frontend.psychologist.absences.update', $absence)
        : route('frontend.psychologist.absences.store') }}"
    >
      @csrf
      @if ($absence)
        @method('PUT')
      @endif

      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="starts_at">{{ __('frontend.absences.starts_at') }}</label>
          <input type="datetime-local" name="starts_at" id="starts_at" class="fe-input" required
            value="{{ old('starts_at', optional($absence?->starts_at)->format('Y-m-d\\TH:i')) }}">
        </div>
        <div class="fe-form-group">
          <label for="ends_at">{{ __('frontend.absences.ends_at') }}</label>
          <input type="datetime-local" name="ends_at" id="ends_at" class="fe-input" required
            value="{{ old('ends_at', optional($absence?->ends_at)->format('Y-m-d\\TH:i')) }}">
        </div>
      </div>
      <div class="fe-form-group">
        <label for="reason">{{ __('frontend.absences.reason') }}</label>
        <input type="text" name="reason" id="reason" class="fe-input" maxlength="255" value="{{ old('reason', $absence?->reason) }}">
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $absence?->is_active ?? true))>
        <label class="form-check-label" for="is_active">{{ __('frontend.active') }}</label>
      </div>

      <div class="fe-form-actions">
        <a href="{{ route('frontend.psychologist.absences.index') }}" class="fe-btn fe-btn--outline">{{ __('frontend.cancel') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
