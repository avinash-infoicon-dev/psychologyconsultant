@if ($rows->isEmpty())
  <p class="fe-muted" style="margin:0;">{{ __('frontend.appointments.empty') }}</p>
@else
  <div class="fe-table-wrap">
    <table class="fe-table">
      <thead>
        <tr>
          <th>{{ $isPsychologist ? __('frontend.appointments.patient') : __('frontend.appointments.psychologist') }}</th>
          <th>{{ __('frontend.appointments.when') }}</th>
          <th>{{ __('frontend.appointments.type') }}</th>
          <th>{{ __('frontend.appointments.status') }}</th>
          @if ($showActions)
            <th>{{ __('frontend.actions') }}</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach ($rows as $appointment)
          <tr>
            <td>
              {{ $isPsychologist
                ? ($appointment->patient?->user?->name ?: $appointment->client_name)
                : ($appointment->professional?->user?->name ?: __('frontend.chat.unknown')) }}
            </td>
            <td>
              {{ $appointment->startsAt()->format('d M Y H:i') }}
              · {{ $appointment->duration_minutes }} {{ __('frontend.appointments.min') }}
            </td>
            <td>{{ __('frontend.appointments.types.'.$appointment->consultation_type) }}</td>
            <td>
              {{ __('frontend.appointments.statuses.'.$appointment->status) }}
              @if ($appointment->cancel_reason)
                <div class="fe-muted">{{ $appointment->cancel_reason }}</div>
              @endif
            </td>
            @if ($showActions)
              <td>
                @if ($isPsychologist && $appointment->status === \App\Models\Appointment::STATUS_PENDING)
                  <form method="post" action="{{ route($routePrefix.'.appointments.confirm', $appointment) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="fe-link-btn">{{ __('frontend.appointments.confirm') }}</button>
                  </form>
                  <form method="post" action="{{ route($routePrefix.'.appointments.decline', $appointment) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="fe-link-btn">{{ __('frontend.appointments.decline') }}</button>
                  </form>
                @endif
                @if ($isPsychologist && $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED && $appointment->startsAt()->lte(now()))
                  <form method="post" action="{{ route($routePrefix.'.appointments.complete', $appointment) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="fe-link-btn">{{ __('frontend.appointments.complete') }}</button>
                  </form>
                @endif
                @if ($appointment->isOpen() && $appointment->startsAt()->isFuture())
                  <form method="post" action="{{ route($routePrefix.'.appointments.cancel', $appointment) }}" class="d-inline" onsubmit="return confirm(@json(__('frontend.appointments.confirm_cancel')))">
                    @csrf
                    <button type="submit" class="fe-link-btn">{{ __('frontend.cancel') }}</button>
                  </form>
                @endif
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
