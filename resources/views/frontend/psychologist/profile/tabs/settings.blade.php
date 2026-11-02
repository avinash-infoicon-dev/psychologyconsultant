@php
  $timezones = \DateTimeZone::listIdentifiers();
  $currentTimezone = $professional->settings?->timezone ?? 'Europe/Zurich';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.settings_title') }}</h3>
  </div>

  <form action="{{ route('frontend.psychologist.profile.settings.update') }}" method="post" class="fe-form">
    @csrf
    @method('PUT')

    <div class="fe-form-grid" style="grid-template-columns: 1fr; max-width: 600px;">
      <div class="fe-form-group">
        <label for="profile_timezone" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">{{ __('frontend.psych_profile.timezone') }}</label>
        
        {{-- Searchable Select --}}
        <select name="timezone" id="profile_timezone" class="fe-select select2-timezone" required style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--fe-border);">
          @foreach ($timezones as $tz)
            <option value="{{ $tz }}" @selected($currentTimezone === $tz)>{{ $tz }}</option>
          @endforeach
        </select>
        
        <p class="fe-muted" style="margin-top: 0.35rem; font-size: 0.8rem;">{{ __('frontend.psych_profile.timezone_hint') }}</p>
      </div>
    </div>

    <div class="fe-form-actions" style="margin-top: 1.5rem;">
      <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.save_settings') }}</button>
    </div>
  </form>
</section>

{{-- Preferences moved to admin Settings → Notifications. --}}
{{-- Restored: users can set types the admin has not applied globally. --}}
{{--@include('frontend.partials.notification-preferences', ['prefFormId' => 'fe-notif-pref-psych', 'prefNested' => true])--}}

{{-- Push scripts for searchable select (Select2 or simple select styling) --}}
@push('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2-timezone').select2({
        placeholder: @json(__('frontend.psych_profile.select_timezone')),
        allowClear: false
      });
    });
  </script>
@endpush
