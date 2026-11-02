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
        @php
          $reasonValue = old('reason', $absence?->reason);
          $reasonKeys = \App\Support\AbsenceReason::KEYS;
        @endphp
        <select name="reason" id="reason" class="fe-select">
          <option value="">{{ __('frontend.absences.no_reason') }}</option>
          @foreach ($reasonKeys as $reasonKey)
            <option value="{{ $reasonKey }}" @selected($reasonValue === $reasonKey)>{{ __('frontend.absences.reasons.'.$reasonKey) }}</option>
          @endforeach
          @if (is_string($reasonValue) && $reasonValue !== '' && ! in_array($reasonValue, $reasonKeys, true))
            <option value="{{ $reasonValue }}" selected>{{ $reasonValue }}</option>
          @endif
        </select>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ __('frontend.absences.reason_hint') }}</p>
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
