@extends('frontend.layouts.dashboard')

@section('title', __('frontend.availability.title'))
@section('page_title', __('frontend.availability.title'))

@php
  //$openAdd = $errors->any() && old('form_context') === 'availability_add';
  $openAdd = false;
  $formatTime = function (?string $value): string {
      if (! $value) {
          return '';
      }

      return substr($value, 0, 5);
  };
@endphp

@section('content')
  <div class="fe-cms-card">
    <div class="fe-profile-toolbar">
      <div>
        <h2 style="margin:0;">{{ __('frontend.availability.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ __('frontend.availability.subtitle') }}</p>
      </div>
      <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="availability-add"
        @disabled(count($availableDays) === 0)
        title="{{ count($availableDays) === 0 ? __('frontend.availability.add_disabled_hint') : __('frontend.availability.create') }}">
        {{ __('frontend.availability.create') }}
      </button>
    </div>
    @if (count($availableDays) === 0)
      <p class="fe-pending-hint">{{ __('frontend.availability.add_disabled_hint') }}</p>
    @endif

    @if ($availabilities->isEmpty())
      @include('frontend.partials.ui-empty', [
        'title' => __('frontend.availability.empty'),
        'hint' => __('frontend.availability.empty_hint'),
      ])
    @else
      <div class="fe-table-wrap">
        <table class="fe-table">
          <thead>
            <tr>
              <th>{{ __('frontend.availability.day') }}</th>
              <th>{{ __('frontend.availability.start_time') }}</th>
              <th>{{ __('frontend.availability.end_time') }}</th>
              <th>{{ __('frontend.availability.status') }}</th>
              <th>{{ __('frontend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($availabilities as $slot)
              <tr>
                <td>{{ __('frontend.availability.days.'.$slot->day) }}</td>
                <td>{{ $formatTime($slot->start_time) }}</td>
                <td>{{ $formatTime($slot->end_time) }}</td>
                <td>
                  @if ($slot->is_available)
                    <span class="fe-badge">{{ __('frontend.availability.available') }}</span>
                  @else
                    <span class="fe-muted">{{ __('frontend.availability.unavailable') }}</span>
                  @endif
                </td>
                <td>
                  <div class="fe-table-actions">
                    <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="availability-edit-{{ $slot->id }}">
                      {{ __('frontend.edit') }}
                    </button>
                    <form action="{{ route('frontend.psychologist.availability.destroy', $slot) }}" method="post"
                      onsubmit="return confirm(@json(__('frontend.confirm_delete')))">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="fe-btn fe-btn--danger-outline fe-btn--sm">{{ __('frontend.delete') }}</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  @foreach ($availabilities as $slot)
    @php
      $editId = 'availability-edit-'.$slot->id;
      //$openEdit = $errors->any() && old('form_context') === 'availability_edit_'.$slot->id;
      $openEdit = null;
    @endphp
    <dialog id="{{ $editId }}" class="fe-modal fe-modal--edit" @if ($openEdit) open @endif>
      <div class="fe-modal__panel">
        <div class="fe-modal__head">
          <h3>{{ __('frontend.availability.edit') }}</h3>
          <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
        </div>
        <form action="{{ route('frontend.psychologist.availability.update', $slot) }}" method="post" class="fe-modal__body">
          @csrf
          @method('PUT')
          <input type="hidden" name="form_context" value="availability_edit_{{ $slot->id }}">
          <div class="fe-form-grid">
            <div class="fe-form-group">
              <label>{{ __('frontend.availability.day') }}</label>
              <select name="day" class="fe-select" required>
                @foreach ($days as $day)
                  @php
                    //$isUsedByOther = in_array($day, $usedDays, true) && $day !== $slot->day;
                    $isUsedByOther = null;
                  @endphp
                  <option value="{{ $day }}"
                    @selected(($openEdit ? old('day') : $slot->day) === $day)
                    @disabled($isUsedByOther)>
                    {{ __('frontend.availability.days.'.$day) }}
                    @if ($isUsedByOther)
                      ({{ __('frontend.availability.already_added') }})
                    @endif
                  </option>
                @endforeach
              </select>
            </div>
            <div class="fe-form-group">
              <label>{{ __('frontend.availability.status') }}</label>
              <label class="fe-checkbox-row" style="margin-top:.85rem;">
                <input type="checkbox" name="is_available" value="1"
                  @checked($openEdit ? old('is_available') : $slot->is_available)>
                <span>{{ __('frontend.availability.available') }}</span>
              </label>
            </div>
            <div class="fe-form-group">
              <label>{{ __('frontend.availability.start_time') }}</label>
              <input type="time" name="start_time" class="fe-input" required
                value="{{ $openEdit ? old('start_time') : $formatTime($slot->start_time) }}">
            </div>
            <div class="fe-form-group">
              <label>{{ __('frontend.availability.end_time') }}</label>
              <input type="time" name="end_time" class="fe-input" required
                value="{{ $openEdit ? old('end_time') : $formatTime($slot->end_time) }}">
            </div>
          </div>
          <div class="fe-form-actions fe-form-actions--inline">
            <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
            <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
          </div>
        </form>
      </div>
    </dialog>
  @endforeach

  <dialog id="availability-add" class="fe-modal" @if ($openAdd) open @endif>
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h3>{{ __('frontend.availability.create') }}</h3>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
      </div>
      <form action="{{ route('frontend.psychologist.availability.store') }}" method="post" class="fe-modal__body">
        @csrf
        <input type="hidden" name="form_context" value="availability_add">
        @php
          $selectedDays = collect(old('days', []))->all();
        @endphp
        <div class="fe-form-group" style="margin-bottom:1rem;">
          <label>{{ __('frontend.availability.select_days') }}</label>
          @if (count($availableDays) === 0)
            <p class="fe-muted" style="margin:.55rem 0 0;">{{ __('frontend.availability.all_days_added') }}</p>
          @else
            <div class="fe-checkbox-grid" style="margin-top:.55rem;">
            {{--Previous: allow all days including ones already added--}}
              @foreach ($days as $day)
                <label class="fe-checkbox-row">
                  <input type="checkbox" name="days[]" value="{{ $day }}"
                    @checked(in_array($day, $selectedDays, true))>
                  <span>{{ __('frontend.availability.days.'.$day) }}</span>
                </label>
              @endforeach
              
              {{--@foreach ($days as $day)
                @php $isUsed = in_array($day, $usedDays, true); @endphp
                <label class="fe-checkbox-row {{ $isUsed ? 'is-disabled' : '' }}">
                  <input type="checkbox" name="days[]" value="{{ $day }}"
                    @checked(! $isUsed && in_array($day, $selectedDays, true))
                    @disabled($isUsed)>
                  <span>
                    {{ __('frontend.availability.days.'.$day) }}
                    @if ($isUsed)
                      <span class="fe-muted">({{ __('frontend.availability.already_added') }})</span>
                    @endif
                  </span>
                </label>
              @endforeach--}}
            </div>
          @endif
        </div>
        <div class="fe-form-grid">
          <div class="fe-form-group">
            <label>{{ __('frontend.availability.status') }}</label>
            <label class="fe-checkbox-row" style="margin-top:.85rem;">
              <input type="checkbox" name="is_available" value="1" @checked(! $openAdd || old('is_available', true))>
              <span>{{ __('frontend.availability.available') }}</span>
            </label>
          </div>
          <div class="fe-form-group"></div>
          <div class="fe-form-group">
            <label for="availability_start">{{ __('frontend.availability.start_time') }}</label>
            <input type="time" name="start_time" id="availability_start" class="fe-input" required
              value="{{ $openAdd ? old('start_time') : '09:00' }}">
          </div>
          <div class="fe-form-group">
            <label for="availability_end">{{ __('frontend.availability.end_time') }}</label>
            <input type="time" name="end_time" id="availability_end" class="fe-input" required
              value="{{ $openAdd ? old('end_time') : '17:00' }}">
          </div>
        </div>
    <div class="fe-form-actions fe-form-actions--inline">
          <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.availability.add') }}</button>
          <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
        </div>
      </form>
    </div>
  </dialog>

  {{-- Modal Popup to show overlap/validation errors --}}
  {{--@if ($errors->any())
    <dialog id="availability-error-modal" class="fe-modal" open>
      <div class="fe-modal__panel" style="max-width: 500px; border: 2px solid #ff5252;">
        <div class="fe-modal__head" style="background-color: #ffebee;">
          <h3 style="color: #c62828; margin: 0;">Error</h3>
          <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="Close" onclick="this.closest('dialog').close()">&times;</button>
        </div>
        <div class="fe-modal__body" style="padding: 1.5rem;">
          <ul style="margin: 0; padding-left: 1.2rem; color: #c62828;">
            @foreach ($errors->all() as $error)
              <li style="margin-bottom: 0.5rem;">{{ $error }}</li>
            @endforeach
          </ul>
          <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <button type="button" class="fe-btn fe-btn--outline" onclick="this.closest('dialog').close()">Close</button>
          </div>
        </div>
      </div>
    </dialog>
  @endif--}}
@endsection
