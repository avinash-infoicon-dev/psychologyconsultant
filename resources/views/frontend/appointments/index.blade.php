@extends('frontend.layouts.dashboard')

@section('title', __('frontend.appointments.title'))
@section('page_title', __('frontend.appointments.title'))

@section('content')
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ __('frontend.appointments.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">
          {{ $isPsychologist ? __('frontend.appointments.subtitle_psychologist') : __('frontend.appointments.subtitle_patient') }}
        </p>
      </div>
    </div>

    @if ($slots->isNotEmpty())
      <p class="fe-muted" style="margin:1rem 0 .35rem;">{{ __('frontend.appointments.hours') }}</p>
      <ul style="margin:0 0 1rem; padding-left:1.1rem;">
        @foreach ($slots as $slot)
          <li>
            {{ __('frontend.availability.days.'.$slot->day) }}
            {{ \Illuminate\Support\Str::of($slot->start_time)->substr(0, 5) }}
            –
            {{ \Illuminate\Support\Str::of($slot->end_time)->substr(0, 5) }}
          </li>
        @endforeach
      </ul>
    @endif

    @if ($isPsychologist)
      @if ($patients->isEmpty())
        <p class="fe-muted">{{ __('frontend.appointments.no_patients') }}</p>
      @else
        <form method="post" action="{{ route($routePrefix.'.appointments.store') }}" class="fe-form-grid" style="margin-bottom:1.5rem;">
          @csrf
          @include('frontend.appointments._fields', ['patients' => $patients, 'types' => $types])
          <div class="fe-form-group" style="align-self:end;">
            <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.appointments.schedule') }}</button>
          </div>
        </form>
      @endif
    @elseif (! $relationship)
      <p class="fe-muted">{{ __('frontend.appointments.no_relationship') }}</p>
      <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-btn fe-btn--primary">{{ __('frontend.chat.find_psychologist') }}</a>
    @elseif ($slots->isEmpty())
      <p class="fe-muted">{{ __('frontend.appointments.no_hours') }}</p>
    @else
      <form method="post" action="{{ route($routePrefix.'.appointments.store') }}" class="fe-form-grid" style="margin-bottom:1.5rem;">
        @csrf
        @include('frontend.appointments._fields', ['patients' => collect(), 'types' => $types])
        <div class="fe-form-group" style="align-self:end;">
          <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.appointments.request') }}</button>
        </div>
      </form>
    @endif
  </div>

  <div class="fe-cms-card" style="margin-top:1rem;">
    <h2 style="margin-top:0;">{{ __('frontend.appointments.upcoming') }}</h2>
    @include('frontend.appointments._table', ['rows' => $upcoming, 'isPsychologist' => $isPsychologist, 'routePrefix' => $routePrefix, 'showActions' => true])
  </div>

  <div class="fe-cms-card" style="margin-top:1rem;">
    <h2 style="margin-top:0;">{{ __('frontend.appointments.history') }}</h2>
    @include('frontend.appointments._table', ['rows' => $history, 'isPsychologist' => $isPsychologist, 'routePrefix' => $routePrefix, 'showActions' => false])
  </div>
@endsection
