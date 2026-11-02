@php
  $notificationService = app(\App\Services\NotificationService::class);
  $prefRows = $preferenceRows ?? $notificationService->userPreferenceRows(auth()->user());
  $prefLabels = [
      'new_message' => __('frontend.notifications.pref_messages'),
      'subscription' => __('frontend.notifications.pref_subscription'),
      'exchange' => __('frontend.notifications.pref_exchange'),
      'account' => __('frontend.notifications.pref_account'),
      'marketing' => __('frontend.notifications.pref_marketing'),
  ];
  $prefCanSave = collect($prefRows)->contains(fn (array $row) => ! $row['applied']);
  $prefFormId = $prefFormId ?? 'fe-notif-pref';
  $prefSectionClass = ! empty($prefNested)
    ? 'fe-profile-section-block fe-notification-prefs'
    : 'fe-cms-card fe-notification-prefs';
@endphp

{{-- Old: always listed every group from NotificationPreference defaults.
  $prefs = $preferences ?? app(\App\Services\NotificationService::class)->preferencesFor(auth()->user());
  $rows = [
      ['group' => 'new_message', 'label' => __('frontend.notifications.pref_messages')],
      ...
  ];
--}}

<section class="{{ $prefSectionClass }}" style="margin-top: 1.5rem;">
  <h3 class="h6 fw-semibold mb-2">{{ __('frontend.notifications.preferences_title') }}</h3>
  <p class="fe-muted mb-3">{{ __('frontend.notifications.preferences_hint') }}</p>

  @if (count($prefRows) === 0)
    <p class="fe-muted mb-0">{{ __('frontend.notifications.preferences_admin_only') }}</p>
  @else
    <form action="{{ route('frontend.notifications.preferences.update') }}" method="post">
      @csrf
      @method('PUT')

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>{{ __('frontend.notifications.pref_type') }}</th>
              <th>{{ __('frontend.notifications.pref_in_app') }}</th>
              <th>{{ __('frontend.notifications.pref_email') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($prefRows as $row)
              @php
                $inApp = $row['group'].'_in_app';
                $email = $row['group'].'_email';
                $inAppId = $prefFormId.'-'.$inApp;
                $emailId = $prefFormId.'-'.$email;
              @endphp
              <tr>
                <td>
                  {{ $prefLabels[$row['group']] ?? $row['group'] }}
                  @if ($row['applied'])
                    <div class="fe-muted" style="font-size: 0.8rem;">{{ __('frontend.notifications.pref_managed') }}</div>
                  @endif
                </td>
                <td>
                  @if ($row['in_app_locked'])
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" @checked($row['in_app']) disabled>
                      <label class="form-check-label">{{ __('frontend.notifications.pref_yes') }}</label>
                    </div>
                  @else
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $inApp }}"
                        id="{{ $inAppId }}"
                        value="1"
                        @checked($row['in_app'])
                      >
                      <label class="form-check-label" for="{{ $inAppId }}">{{ __('frontend.notifications.pref_yes') }}</label>
                    </div>
                  @endif
                </td>
                <td>
                  @if ($row['email_locked'])
                    @if ($row['group'] === 'account')
                      <input type="hidden" name="{{ $email }}" value="1">
                    @endif
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" @checked($row['email']) disabled>
                      <label class="form-check-label">
                        {{ $row['group'] === 'account' ? __('frontend.notifications.pref_always_on') : __('frontend.notifications.pref_yes') }}
                      </label>
                    </div>
                  @else
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $email }}"
                        id="{{ $emailId }}"
                        value="1"
                        @checked($row['email'])
                      >
                      <label class="form-check-label" for="{{ $emailId }}">{{ __('frontend.notifications.pref_yes') }}</label>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if ($prefCanSave)
        <div class="fe-form-actions" style="margin-top: 1rem;">
          <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.notifications.preferences_save') }}</button>
        </div>
      @endif
    </form>
  @endif
</section>
