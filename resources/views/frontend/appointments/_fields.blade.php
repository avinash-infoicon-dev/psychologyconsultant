@if ($patients->isNotEmpty())
  <div class="fe-form-group">
    <label for="patient_id">{{ __('frontend.appointments.patient') }}</label>
    <select name="patient_id" id="patient_id" class="fe-select" required>
      <option value="">{{ __('frontend.appointments.choose_patient') }}</option>
      @foreach ($patients as $item)
        <option value="{{ $item->patient_id }}" @selected((string) old('patient_id') === (string) $item->patient_id)>
          {{ $item->patient?->user?->name ?: __('frontend.chat.unknown') }}
        </option>
      @endforeach
    </select>
  </div>
@endif
<div class="fe-form-group">
  <label for="appointment_date">{{ __('frontend.appointments.date') }}</label>
  <input type="date" name="appointment_date" id="appointment_date" class="fe-input" required
    min="{{ now()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}"
    value="{{ old('appointment_date') }}">
</div>
<div class="fe-form-group">
  <label for="appointment_time">{{ __('frontend.appointments.time') }}</label>
  <input type="time" name="appointment_time" id="appointment_time" class="fe-input" required value="{{ old('appointment_time') }}">
</div>
<div class="fe-form-group">
  <label for="duration_minutes">{{ __('frontend.appointments.duration') }}</label>
  <select name="duration_minutes" id="duration_minutes" class="fe-select" required>
    @foreach (\App\Models\Appointment::DURATIONS as $minutes)
      <option value="{{ $minutes }}" @selected((string) old('duration_minutes', '45') === (string) $minutes)>
        {{ __('frontend.appointments.minutes', ['count' => $minutes]) }}
      </option>
    @endforeach
  </select>
</div>
<div class="fe-form-group">
  <label for="consultation_type">{{ __('frontend.appointments.type') }}</label>
  <select name="consultation_type" id="consultation_type" class="fe-select" required>
    @foreach ($types as $type)
      <option value="{{ $type }}" @selected(old('consultation_type', $types[0] ?? 'online') === $type)>
        {{ __('frontend.appointments.types.'.$type) }}
      </option>
    @endforeach
  </select>
</div>
<div class="fe-form-group">
  <label for="notes">{{ __('frontend.appointments.notes') }}</label>
  <input type="text" name="notes" id="notes" class="fe-input" maxlength="500" value="{{ old('notes') }}">
</div>
