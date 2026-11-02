@extends('frontend.layouts.dashboard')

{{-- Legacy view variable was $psychologist; directory records now use $professional. --}}
@section('title', $professional->user?->name ?? trim($professional->first_name.' '.$professional->last_name))
@section('page_title', $professional->user?->name ?? trim($professional->first_name.' '.$professional->last_name))

@section('content')
  <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-profile-back">
    <i class="bi bi-arrow-left"></i> {{ __('frontend.professionals.back') }}
  </a>

  @php
    $careStatus = $careStatus ?? \App\Support\PsychologistCareStatus::for($professional);
    $profileMode = $profileMode ?? 'full';
    $canMessage = $careStatus === \App\Support\PsychologistCareStatus::ACTIVE;
  @endphp

  @if ($careStatus !== \App\Support\PsychologistCareStatus::ACTIVE)
    <div class="fe-quota-block" style="margin-bottom:1rem;">
      <p class="mb-0">{{ __('frontend.chat.psychologist_unavailable') }}</p>
    </div>
  @endif

  <article class="fe-profile">
    <div class="fe-profile-cover">
      <img src="{{ $professional->cover_image_url }}" alt="">
    </div>

    <header class="fe-profile-header">
      <img class="fe-profile-avatar" src="{{ $professional->profile_image_url }}" alt="{{ $professional->name }}">

      <div class="fe-profile-header__content">
        <div>
          <div class="fe-profile-name">
            <h1>{{ trim(($professional->title ? $professional->title.' ' : '').$professional->name) }}</h1>
            @if ($professional->is_verified)
              <span class="fe-verified-badge"><i class="bi bi-patch-check-fill"></i> {{ __('frontend.professionals.verified') }}</span>
            @endif
          </div>
          <p class="fe-profile-specialty">{{ $professional->specialization ?: $professional->professionalType?->name }}</p>
          <div class="fe-profile-summary">
            {{--<span class="fe-rating"><i class="bi bi-star-fill"></i> <strong>{{ number_format((float) $professional->rating, 1) }}</strong> ({{ __('frontend.professionals.reviews_count', ['count' => $professional->total_reviews]) }})</span>--}}
            @if ($professional->experience_years)
              <span><i class="bi bi-briefcase"></i> {{ __('frontend.professionals.years_experience', ['count' => $professional->experience_years]) }}</span>
            @endif
            @if ($professional->city || $professional->country)
              <span><i class="bi bi-geo-alt"></i> {{ implode(', ', array_filter([$professional->city, $professional->country])) }}</span>
            @endif
          </div>
        </div>

        <div class="fe-profile-header__actions">
          @if ($canMessage)
          {{--<a href="#availability" class="fe-btn fe-btn--outline">
            <i class="bi bi-calendar2-check"></i> {{ __('frontend.professionals.book_now') }}
          </a>--}}
          @if (!$relationship || in_array($relationship->status, ['rejected', 'closed']))
            <form action="{{ route('frontend.patient.relationships.store') }}" method="post">
              @csrf
              <input type="hidden" name="psychologist_id" value="{{ $professional->id }}">
              <button type="submit" class="fe-btn fe-btn--primary">
                <i class="bi bi-person-plus"></i> {{ __('relationship.request_consultation') }}
              </button>
            </form>
          @elseif ($relationship->status === 'pending')
            <div>
              <button type="button" class="fe-btn fe-btn--outline" disabled>
                <i class="bi bi-hourglass-split"></i> {{ __('relationship.status_pending') }}
              </button>
              <span class="fe-pending-hint">{{ __('ui.pending_relationship_hint') }}</span>
            </div>
          @elseif ($relationship->status === 'accepted')
            <form action="{{ route('frontend.patient.relationships.activate', $relationship) }}" method="post">
              @csrf
              <button type="submit" class="fe-btn fe-btn--outline fe-btn--success" title="Activate to start chatting">
                <i class="bi bi-check-circle"></i> {{ __('relationship.status_accepted_pay') }}
              </button>
            </form>
          @elseif ($relationship->status === 'active')
            <form action="{{ route('frontend.patient.conversations.store') }}" method="post">
              @csrf
              <input type="hidden" name="professional_uuid" value="{{ $professional->uuid }}">
              <button type="submit" class="fe-btn fe-btn--primary">
                <i class="bi bi-chat-dots"></i> {{ __('frontend.professionals.message') }}
              </button>
            </form>
          @endif
          @endif
        </div>
      </div>
    </header>

    @if ($profileMode === 'limited')
      <p class="fe-muted">{{ __('frontend.chat.psychologist_unavailable') }}</p>
    @else
    <div class="fe-profile-layout">
      <main class="fe-profile-main">
        @if ($professional->bio)
          <section class="fe-profile-section">
            <h2><i class="bi bi-person-lines-fill"></i> {{ __('frontend.professionals.about') }}</h2>
            <div class="fe-profile-copy">{!! nl2br(e($professional->bio)) !!}</div>
          </section>
        @endif

        @if ($professional->specializations->isNotEmpty())
          <section class="fe-profile-section">
            <h2><i class="bi bi-check2-circle"></i> {{ __('frontend.professionals.specializations') }}</h2>
            <div class="fe-chip-row fe-chip-row--large">
              @foreach ($professional->specializations as $specialization)
                <span class="fe-chip"><i class="bi bi-check-lg"></i> {{ $specialization->name }}</span>
              @endforeach
            </div>
          </section>
        @endif

        @if ($professional->educations->isNotEmpty())
          <section class="fe-profile-section">
            <h2><i class="bi bi-mortarboard"></i> {{ __('frontend.professionals.education') }}</h2>
            <div class="fe-timeline">
              @foreach ($professional->educations as $education)
                <div class="fe-timeline-item">
                  <span class="fe-timeline-icon"><i class="bi bi-mortarboard-fill"></i></span>
                  <div>
                    <h3>{{ $education->degree }}</h3>
                    <p>{{ $education->institution }}@if ($education->field_of_study) · {{ $education->field_of_study }}@endif</p>
                    @if ($education->year)<small>{{ $education->year }}</small>@endif
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endif

        @if ($professional->experiences->isNotEmpty())
          <section class="fe-profile-section">
            <h2><i class="bi bi-briefcase"></i> {{ __('frontend.professionals.experience') }}</h2>
            <div class="fe-timeline">
              @foreach ($professional->experiences as $experience)
                <div class="fe-timeline-item">
                  <span class="fe-timeline-icon"><i class="bi bi-building"></i></span>
                  <div>
                    <h3>{{ $experience->designation }}</h3>
                    <p>{{ $experience->organization }}</p>
                    <small>
                      {{ $experience->start_date?->format('Y') }}
                      –
                      {{ $experience->currently_working ? __('frontend.professionals.present') : $experience->end_date?->format('Y') }}
                    </small>
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @endif

        {{--@if ($professional->reviews->isNotEmpty())
          <section class="fe-profile-section">
            <h2><i class="bi bi-chat-quote"></i> {{ __('frontend.professionals.reviews') }}</h2>
            <div class="fe-review-grid">
              @foreach ($professional->reviews as $review)
                <article class="fe-review">
                  <div class="fe-review__stars">
                    @for ($star = 1; $star <= 5; $star++)
                      <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                  </div>
                  <p>“{{ $review->review }}”</p>
                  <strong>{{ $review->reviewer_name }}</strong>
                </article>
              @endforeach
            </div>
          </section>
        @endif--}}
      </main>

      <aside class="fe-profile-sidebar">
        {{--<section class="fe-profile-side-card">
          <h2>{{ __('frontend.professionals.services') }}</h2>
          <div class="fe-service-list">
            @forelse ($professional->services as $service)
              <div class="fe-service-item">
                <div>
                  <strong><i class="bi bi-check-circle-fill"></i> {{ $service->name }}</strong>
                  @if ($service->duration)
                    <small>{{ __('frontend.professionals.minutes', ['count' => $service->duration]) }}</small>
                  @endif
                </div>
                @if ($service->price)
                  <span>CHF {{ number_format((float) $service->price, 0) }}</span>
                @endif
              </div>
            @empty
              <p class="fe-muted">—</p>
            @endforelse
          </div>
        </section>--}}

        <section class="fe-profile-side-card" id="availability">
          <h2>{{ __('frontend.professionals.availability') }}</h2>

          @if ($professional->currentAbsences->isNotEmpty())
            @php $absence = $professional->currentAbsences->first(); @endphp
            <div class="fe-absence-banner fe-absence-banner--profile">
              <i class="bi bi-calendar-x"></i>
              <div>
                <strong>{{ __('frontend.professionals.currently_away') }}</strong>
                <span>{{ __('frontend.professionals.away_until', ['until' => \App\Support\AbsenceReason::format($absence->ends_at)]) }}</span>
                @if ($absence->reason)
                  <span class="fe-muted">{{ \App\Support\AbsenceReason::label($absence->reason) }}</span>
                @endif
              </div>
            </div>
          @endif

          @php
            $orderedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $availabilitiesByDay = $professional->availabilities->groupBy('day');
            $availableDays = [];
            $unavailableDays = [];
            foreach ($orderedDays as $d) {
                if (isset($availabilitiesByDay[$d]) && $availabilitiesByDay[$d]->isNotEmpty()) {
                    $availableDays[] = $d;
                } else {
                    $unavailableDays[] = $d;
                }
            }
          @endphp

          <div class="fe-availability-summary" style="margin-bottom: 1rem;">
            @if(count($availableDays) > 0)
              <div style="font-weight: 600; color: #2e7d32; display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #4caf50;"></span>
                {{ implode(', ', array_map(fn($day) => __('frontend.availability.days.'.$day), $availableDays)) }}
              </div>
            @endif
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('full-availability-modal').showModal();" style="font-size: 0.9rem; color: var(--fe-primary); text-decoration: underline; font-weight: 500; display: inline-block; margin-bottom: 0.75rem;">
              [ View full availability ]
            </a>

            @if(count($unavailableDays) > 0)
              <div style="font-size: 0.85rem; color: var(--fe-muted); display: flex; align-items: center; gap: 0.35rem;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #757575;"></span>
                {{ implode(', ', array_map(fn($day) => __('frontend.availability.days.'.$day), $unavailableDays)) }} ({{ __('frontend.availability.unavailable') }})
              </div>
            @endif
          </div>

          {{-- Availability Modal dialog popup --}}
          <dialog id="full-availability-modal" class="fe-modal fe-avail-modal">
            <div class="fe-modal__panel fe-avail-modal__panel">
              <div class="fe-avail-modal__head">
                <div>
                  <span class="fe-avail-modal__eyebrow"><i class="bi bi-calendar2-week"></i> {{ __('frontend.professionals.availability') }}</span>
                  <h2>{{ $professional->name }}</h2>
                </div>
                <button type="button" class="fe-avail-modal__close" onclick="document.getElementById('full-availability-modal').close()" aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
              </div>
              <div class="fe-avail-modal__body">
                <div class="fe-avail-modal__tz">
                  <span>{{ __('frontend.psych_profile.timezone') }}</span>
                  <strong><i class="bi bi-globe2"></i> {{ $professional->settings?->timezone ?? 'Europe/Zurich' }}</strong>
                </div>

                <h3>{{ __('frontend.availability.title') }}</h3>
                <div class="fe-avail-modal__days">
                  @foreach ($orderedDays as $d)
                    @php
                      $daySlots = $availabilitiesByDay->get($d) ?? collect();
                      $hasSlots = $daySlots->isNotEmpty();
                    @endphp
                    <div class="fe-avail-modal__day {{ $hasSlots ? 'is-on' : 'is-off' }}">
                      <strong>{{ __('frontend.availability.days.'.$d) }}</strong>
                      <div class="fe-avail-modal__slots">
                        @forelse ($daySlots as $slot)
                          <span class="fe-avail-modal__time">
                            {{ date('H:i', strtotime($slot->start_time)) }} – {{ date('H:i', strtotime($slot->end_time)) }}
                          </span>
                        @empty
                          <span class="fe-avail-modal__none">{{ __('frontend.availability.unavailable') }}</span>
                        @endforelse
                      </div>
                    </div>
                  @endforeach
                </div>

                @if ($professional->absences->isNotEmpty())
                  <h3>{{ __('frontend.absences.title') }}</h3>
                  <div class="fe-avail-modal__absences">
                    @foreach ($professional->absences as $abs)
                      <div class="fe-avail-modal__away">
                        <strong>{{ \App\Support\AbsenceReason::format($abs->starts_at, 'd M') }} – {{ \App\Support\AbsenceReason::format($abs->ends_at, 'd M Y') }}</strong>
                        @if($abs->reason)
                          <span>{{ \App\Support\AbsenceReason::label($abs->reason) }}</span>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
              <div class="fe-avail-modal__foot">
                <button type="button" class="fe-avail-modal__done" onclick="document.getElementById('full-availability-modal').close()">{{ __('frontend.psych_profile.close') }}</button>
              </div>
            </div>
          </dialog>
        </section>

        <section class="fe-profile-side-card">
          <h2>{{ __('frontend.professionals.languages') }}</h2>
          <div class="fe-chip-row">
            {{--@foreach ($professional->languages ?? [] as $language)
              <span class="fe-chip">{{ $language }}</span>
            @endforeach--}}
            @if ($professional->languages)
               <span class="fe-chip"> {{ collect($professional->languages)->map(fn ($code) => \App\Support\Locales::label($code))->implode(', ') }}</span>
            @endif
          </div>

          <div class="fe-consultation-options">
            @if ($professional->online_consultation)
              <span><i class="bi bi-camera-video-fill"></i> {{ __('frontend.professionals.online') }}</span>
            @endif
            @if ($professional->offline_consultation)
              <span><i class="bi bi-building-check"></i> {{ __('frontend.professionals.clinic') }}</span>
            @endif
          </div>

          {{--@if ($professional->consultation_fee)
            <div class="fe-profile-price">
              <strong>CHF {{ number_format((float) $professional->consultation_fee, 0) }}</strong>
              <span>{{ __('frontend.professionals.per_session') }}</span>
            </div>
          @endif--}}
        </section>
      </aside>
    </div>
    @endif
  </article>
@endsection
