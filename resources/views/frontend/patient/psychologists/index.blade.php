
@extends('frontend.layouts.dashboard')

@section('title', __('frontend.dashboard.quick_psychologists'))
@section('page_title', __('frontend.dashboard.quick_psychologists'))

@section('content')
  <div class="fe-directory-head">
    <div>
      <span class="fe-directory-eyebrow"><i class="bi bi-shield-check"></i> {{ __('frontend.professionals.verified') }}</span>
      <h1>{{ __('frontend.professionals.title') }}</h1>
      <p>{{ __('frontend.professionals.subtitle') }}</p>
    </div>

    {{--
    Old compact filters (checkbox was invisible: theme sets appearance:none on all inputs).
    <form method="get" class="fe-directory-filters">
      <label class="fe-directory-search">
        <i class="bi bi-search"></i>
        <input type="search" name="specialization" placeholder="specialization">
      </label>
      <label class="fe-check">
        <input type="checkbox" name="accepting_only" value="1">
        <span>accepting patients only</span>
      </label>
      <button type="submit" class="fe-btn fe-btn--primary">Search</button>
    </form>
    --}}
    {{--
    Previous stacked directory filters (kept for restore):
    <form method="get" class="fe-directory-filters" role="search">
      <label class="fe-directory-field">
        <span class="fe-directory-field__label">{{ __('frontend.professionals.specializations') }}</span>
        <span class="fe-directory-search">
          <i class="bi bi-search"></i>
          <input type="search" name="specialization" value="{{ request('specialization') }}"
            placeholder="{{ __('psychologist_directory_api.validation.attributes.specialization') }}">
        </span>
      </label>
      <label class="fe-directory-field">
        <span class="fe-directory-field__label">{{ __('frontend.professionals.filter_language') }}</span>
        <select name="language" class="fe-select fe-directory-select">
          <option value="">{{ __('frontend.professionals.filter_language_any') }}</option>
          @foreach (\App\Support\Locales::all() as $code => $meta)
            <option value="{{ $code }}" @selected(request('language') === $code)>{{ $meta['label'] }}</option>
          @endforeach
        </select>
      </label>
      <label class="fe-directory-field">
        <span class="fe-directory-field__label">{{ __('frontend.professionals.filter_availability') }}</span>
        <select name="availability" class="fe-select fe-directory-select">
          <option value="">{{ __('frontend.professionals.filter_availability_any') }}</option>
          <option value="today" @selected(request('availability') === 'today')>{{ __('frontend.professionals.filter_availability_today') }}</option>
          @foreach (\App\Models\Availability::DAYS as $day)
            <option value="{{ $day }}" @selected(request('availability') === $day)>{{ __('frontend.availability.days.'.$day) }}</option>
          @endforeach
        </select>
      </label>
      <label class="fe-directory-check">
        <input type="checkbox" name="accepting_only" value="1" @checked(request()->boolean('accepting_only'))>
        <span>{{ __('frontend.professionals.accepting') }}</span>
      </label>
      <div class="fe-directory-filter-actions">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('backend.search') }}</button>
        @if (request()->filled('specialization') || request()->filled('language') || request()->filled('availability') || request()->boolean('accepting_only'))
          <a href="{{ route('frontend.patient.psychologists.index') }}" class="fe-btn fe-btn--outline">{{ __('ui.clear_filters') }}</a>
        @endif
      </div>
    </form>
    --}}
  </div>

  @php
    $perPage = (int) (request('per_page') ?: 20);
    $hasDirectoryFilters = request()->filled('specialization')
      || request()->filled('language')
      || request()->filled('availability')
      || request()->boolean('accepting_only');
  @endphp

  <div class="card h-100 p-0 radius-12">
    <form method="get" role="search">
      <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
        <div class="d-flex align-items-center flex-wrap gap-3">
          {{--<span class="text-md fw-medium text-secondary-light mb-0">Show</span>
          <select name="per_page" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" onchange="this.form.submit()">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 20] as $n)
              <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
            @endforeach
          </select>--}}

          <div class="navbar-search">
            <input type="text" class="bg-base h-40-px w-auto border" name="specialization"
              value="{{ request('specialization') }}"
              placeholder="{{ __('psychologist_directory_api.validation.attributes.specialization') }}">
           {{--<iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>--}}
          </div>

          <select name="language" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" aria-label="{{ __('frontend.professionals.filter_language') }}">
            <option value="">{{ __('frontend.professionals.filter_language_any') }}</option>
            @foreach (\App\Support\Locales::all() as $code => $meta)
              <option value="{{ $code }}" @selected(request('language') === $code)>{{ $meta['label'] }}</option>
            @endforeach
          </select>

          <select name="availability" class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px" aria-label="{{ __('frontend.professionals.filter_availability') }}">
            <option value="">{{ __('frontend.professionals.filter_availability_any') }}</option>
            <option value="today" @selected(request('availability') === 'today')>{{ __('frontend.professionals.filter_availability_today') }}</option>
            @foreach (\App\Models\Availability::DAYS as $day)
              <option value="{{ $day }}" @selected(request('availability') === $day)>{{ __('frontend.availability.days.'.$day) }}</option>
            @endforeach
          </select>

          <label class="fe-directory-check mb-0">
            <input type="checkbox" name="accepting_only" value="1" @checked(request()->boolean('accepting_only'))>
            <span>{{ __('frontend.professionals.accepting') }}</span>
          </label>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
          @if ($hasDirectoryFilters)
            <a href="{{ route('frontend.patient.psychologists.index') }}"
              class="border border-neutral-400 text-secondary-light text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2 text-decoration-none">
              {{ __('ui.clear_filters') }}
            </a>
          @endif
          <button type="submit"
            class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
            <iconify-icon icon="ion:search-outline" class="icon text-xl line-height-1"></iconify-icon>
            {{ __('backend.search') }}
          </button>
        </div>
      </div>
    </form>

  {{--
  Legacy horizontal list cards (kept for restore):
  <div class="fe-professional-list"> ... fe-professional-card ... </div>
  --}}

  {{-- Template card design (psychology-final/psychology.php), kept for restore:
  <div class="col-xxl-3 col-md-6 user-grid-card">
                    <div class="position-relative border radius-16 overflow-hidden">
                        <img src="assets/images/user-grid/user-grid-bg<?= (int) $psy['bg'] ?>.png" alt="Image" class="w-100 object-fit-cover">
                        <div class="dropdown position-absolute top-0 end-0 me-16 mt-16">
                            <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                class="bg-white-gradient-light w-32-px h-32-px radius-8 border border-light-white d-flex justify-content-center align-items-center text-white">
                                <iconify-icon icon="entypo:dots-three-vertical" class="icon "></iconify-icon>
                            </button>
                            <ul class="dropdown-menu p-12 border bg-base shadow">
                                <li>
                                    <a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                                        href="add-psychologist.php?id=<?= htmlspecialchars($psy['id']) ?>">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <button type="button"
                                        class="delete-btn dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-danger-100 text-hover-danger-600 d-flex align-items-center gap-10">
                                        Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="ps-16 pb-16 pe-16 text-center mt--50">
                            <img src="assets/images/user-grid/user-grid-img<?= (int) $psy['img'] ?>.png" alt="<?= htmlspecialchars($psy['name']) ?>"
                                class="border br-white border-width-2-px w-100-px h-100-px rounded-circle object-fit-cover">
                            <h2 class="h6 text-lg mb-0 mt-4"><?= htmlspecialchars($psy['name']) ?></h2>
                            <span class="text-secondary-light mb-16 d-block"><?= htmlspecialchars($psy['email']) ?></span>
                            <div class="center-border position-relative bg-danger-gradient-light radius-8 p-12 d-flex align-items-center gap-4">
                                <div class="text-center w-50">
                                    <h2 class="h6 text-md mb-0"><?= htmlspecialchars($psy['license']) ?></h2>
                                    <span class="text-secondary-light text-sm mb-0">License</span>
                                </div>
                                <div class="text-center w-50">
                                    <h2 class="h6 text-md mb-0"><?= htmlspecialchars($psy['specialization']) ?></h2>
                                    <span class="text-secondary-light text-sm mb-0">Specialization</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-8 mt-12 flex-wrap">
                                <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-xs">Email Verified</span>
                                <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-xs">Admin Verified</span>
                            </div>
                            <div class="d-flex align-items-center gap-8 mt-16">
                                <a href="psychologist-details.php" class="bg-primary-50 text-primary-600 p-10 text-sm btn-sm px-12 py-10 radius-8 d-flex align-items-center justify-content-center fw-medium gap-2 w-100">View</a>
                            </div>
                        </div>
                    </div>
                </div>
  --}}
  <div class="card-body p-24">
  <div class="row gy-20-px">
    @forelse ($professionals as $professional)
      @php
        $bannerIndex = ($loop->index % 12) + 1;
        //$bannerUrl = asset('commonassets/images/user-grid/user-grid-bg'.$bannerIndex.'.png');
        $bannerUrl = $professional->cover_image_url;
        $profileUrl = route('frontend.patient.psychologists.show', $professional->uuid);
        $displayName = trim(($professional->title ? $professional->title.' ' : '').$professional->name);
        $specializationLabel = $professional->specialization ?: $professional->professionalType?->name ?: '—';

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
        $modalId = 'avail-modal-' . $professional->id;
      @endphp

      <div class="col-xxl-3 col-md-6 user-grid-card">
        <div class="position-relative border radius-16 overflow-hidden bg-base h-100">
          <img src="{{ $bannerUrl }}" alt="" class="w-100 object-fit-cover">

          <div class="dropdown position-absolute top-0 end-0 me-16 mt-16 z-2">
            <button type="button" data-bs-toggle="dropdown" aria-expanded="false"
              class="bg-white-gradient-light w-32-px h-32-px radius-8 border border-light-white d-flex justify-content-center align-items-center text-white">
              <iconify-icon icon="entypo:dots-three-vertical" class="icon"></iconify-icon>
            </button>
            <ul class="dropdown-menu p-12 border bg-base shadow">
              <li>
                <a class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                  href="{{ $profileUrl }}">
                  {{ __('frontend.professionals.view_profile') }}
                </a>
              </li>
              <li>
                <button type="button"
                  class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10"
                  onclick="document.getElementById('{{ $modalId }}').showModal();">
                  {{ __('frontend.professionals.availability') }}
                </button>
              </li>
            </ul>
          </div>

          <div class="ps-16 pb-16 pe-16 text-center mt--50">
            <a href="{{ $profileUrl }}">
              <img src="{{ $professional->profile_image_url }}" alt="{{ $professional->name }}"
                class="border br-white border-width-2-px w-100-px h-100-px rounded-circle object-fit-cover">
            </a>
            <h2 class="h6 text-lg mb-0 mt-4">
              <a href="{{ $profileUrl }}" class="text-decoration-none text-primary-light">{{ $displayName }}</a>
            </h2>
            <span class="text-secondary-light mb-16 d-block">{{ $specializationLabel }}</span>

            <div class="center-border position-relative bg-danger-gradient-light radius-8 p-12 d-flex align-items-center gap-4">
              <div class="text-center w-50">
                <h2 class="h6 text-md mb-0">{{ $professional->license_number ?: '—' }}</h2>
                <span class="text-secondary-light text-sm mb-0">{{ __('backend.psychologists.license') }}</span>
              </div>
              <div class="text-center w-50">
                <h2 class="h6 text-md mb-0 text-truncate" title="{{ $specializationLabel }}">{{ $specializationLabel }}</h2>
                <span class="text-secondary-light text-sm mb-0">{{ __('backend.psychologists.specialization') }}</span>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-center gap-8 mt-12 flex-wrap">
              <span class="{{ $professional->is_accepting_patients ? 'bg-success-focus text-success-600 border border-success-main' : 'bg-neutral-200 text-neutral-600 border border-neutral-400' }} px-12 py-4 radius-4 fw-medium text-xs d-inline-flex align-items-center gap-4">
                <iconify-icon icon="{{ $professional->is_accepting_patients ? 'solar:check-circle-bold' : 'solar:danger-circle-bold' }}" class="icon text-sm"></iconify-icon>
                {{ $professional->is_accepting_patients ? __('frontend.professionals.accepting') : __('frontend.professionals.not_accepting') }}
              </span>
              @if ($professional->currentAbsences->isNotEmpty())
                @php $absence = $professional->currentAbsences->first(); @endphp
                <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-4 radius-4 fw-medium text-xs d-inline-flex align-items-center gap-4">
                  <iconify-icon icon="solar:calendar-bold" class="icon text-sm"></iconify-icon>
                  {{ __('frontend.professionals.away_until', ['until' => \App\Support\AbsenceReason::format($absence->ends_at, 'd M Y')]) }}
                </span>
              @endif
            </div>

            <div class="text-start mt-12 text-secondary-light text-sm">
              @if ($professional->experience_years)
                <div class="mb-4"><i class="bi bi-briefcase me-4"></i>{{ __('frontend.professionals.years_experience', ['count' => $professional->experience_years]) }}</div>
              @endif
              @if ($professional->languages)
                <div class="mb-4">
                  <i class="bi bi-translate me-4"></i>
                  {{ collect($professional->languages)->map(fn ($code) => \App\Support\Locales::label($code))->implode(', ') }}
                </div>
              @endif
              @if ($professional->city || $professional->country)
                <div class="mb-4"><i class="bi bi-geo-alt me-4"></i>{{ implode(', ', array_filter([$professional->city, $professional->country])) }}</div>
              @endif
            </div>

            @if ($professional->specializations->isNotEmpty())
              <div class="d-flex flex-wrap justify-content-center gap-4 mt-8">
                @foreach ($professional->specializations->take(4) as $specialization)
                  <span class="bg-primary-50 text-primary-600 px-8 py-4 radius-4 text-xs">{{ $specialization->name }}</span>
                @endforeach
              </div>
            @endif

            <div class="text-start mt-12 text-sm">
              @if (count($availableDays) > 0)
                <div class="text-success-600 mb-4">
                  <span class="d-inline-block w-8-px h-8-px rounded-circle bg-success-600 me-4"></span>
                  {{ implode(', ', array_map(fn ($day) => __('frontend.availability.days.'.$day), $availableDays)) }}
                </div>
              @endif
              <button type="button" class="p-0 border-0 bg-transparent text-primary-600 text-sm" onclick="document.getElementById('{{ $modalId }}').showModal();">
                {{ __('frontend.professionals.availability') }}
              </button>
              @if (count($unavailableDays) > 0)
                <div class="text-secondary-light mt-4">
                  <span class="d-inline-block w-8-px h-8-px rounded-circle bg-neutral-400 me-4"></span>
                  {{ implode(', ', array_map(fn ($day) => __('frontend.availability.days.'.$day), $unavailableDays)) }} ({{ __('frontend.availability.unavailable') }})
                </div>
              @endif
            </div>

            <dialog id="{{ $modalId }}" class="fe-modal fe-avail-modal">
              <div class="fe-modal__panel fe-avail-modal__panel">
                <div class="fe-avail-modal__head">
                  <div>
                    <span class="fe-avail-modal__eyebrow"><i class="bi bi-calendar2-week"></i> {{ __('frontend.professionals.availability') }}</span>
                    <h2>{{ $professional->name }}</h2>
                  </div>
                  <button type="button" class="fe-avail-modal__close" onclick="document.getElementById('{{ $modalId }}').close()" aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
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
                </div>
                <div class="fe-avail-modal__foot">
                  <button type="button" class="fe-avail-modal__done" onclick="document.getElementById('{{ $modalId }}').close()">{{ __('frontend.psych_profile.close') }}</button>
                </div>
              </div>
            </dialog>

            <div class="d-flex align-items-center gap-8 mt-16">
              <a href="{{ $profileUrl }}"
                class="bg-primary-50 text-primary-600 bg-hover-primary-600 hover-text-white p-10 text-sm btn-sm px-12 py-10 radius-8 d-flex align-items-center justify-content-center fw-medium gap-2 w-100 text-decoration-none">
                {{ __('frontend.professionals.view_profile') }}
              </a>
              <button type="button"
                class="bg-success-focus text-success-600 bg-hover-success-200 p-10 text-sm btn-sm px-12 py-10 radius-8 d-flex align-items-center justify-content-center fw-medium"
                onclick="document.getElementById('{{ $modalId }}').showModal();"
                aria-label="{{ __('frontend.professionals.availability') }}"
                title="{{ __('frontend.professionals.availability') }}">
                <iconify-icon icon="solar:calendar-bold" class="icon text-lg line-height-1"></iconify-icon>
              </button>
              <a href="{{ $profileUrl }}"
                class="bg-danger-focus text-danger-600 bg-hover-danger-200 p-10 text-sm btn-sm px-12 py-10 radius-8 d-flex align-items-center justify-content-center fw-medium"
                aria-label="{{ __('frontend.professionals.view_profile') }}"
                title="{{ __('frontend.professionals.view_profile') }}">
                <iconify-icon icon="solar:export-bold" class="icon text-lg line-height-1"></iconify-icon>
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      @php
        $hasFilters = request()->filled('specialization')
          || request()->filled('language')
          || request()->filled('availability')
          || request()->boolean('accepting_only');
      @endphp
      <div class="col-12">
        @include('frontend.partials.ui-empty', [
          'title' => $hasFilters ? __('frontend.professionals.empty_filtered') : __('frontend.professionals.empty'),
          'hint' => $hasFilters ? __('frontend.professionals.empty_filtered_hint') : __('frontend.professionals.empty_hint'),
          'actionUrl' => $hasFilters ? route('frontend.patient.psychologists.index') : null,
          'actionLabel' => $hasFilters ? __('ui.clear_filters') : null,
        ])
      </div>
    @endforelse
  </div>

  @if ($professionals->total() > 0)
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
      <span class="text-secondary-light">{{ __('frontend.notifications.history_showing', ['from' => $professionals->firstItem() ?? 0, 'to' => $professionals->lastItem() ?? 0, 'total' => $professionals->total()]) }}</span>
      @if ($professionals->hasPages())
        <div class="fe-pagination mb-0">{{ $professionals->links() }}</div>
      @endif
    </div>
  @endif
  </div>
  </div>

  @if (false)
  <div class="fe-psych-grid">
    @forelse ($professionals as $professional)
      @php
        $bannerIndex = ($loop->index % 12) + 1;
        $bannerUrl = asset('commonassets/images/user-grid/user-grid-bg'.$bannerIndex.'.png');
        $profileUrl = route('frontend.patient.psychologists.show', $professional->uuid);
        $displayName = trim(($professional->title ? $professional->title.' ' : '').$professional->name);

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
        $modalId = 'avail-modal-' . $professional->id;
      @endphp

      <article class="fe-dash-profile-card fe-psych-card">
        <div class="fe-dash-profile-card__banner" style="background-image: url('{{ $bannerUrl }}');">
          <a href="{{ $profileUrl }}" class="fe-dash-profile-card__menu" aria-label="{{ __('frontend.professionals.view_profile') }}">
            <i class="bi bi-three-dots-vertical"></i>
          </a>
        </div>

        <div class="fe-dash-profile-card__body">
          <a href="{{ $profileUrl }}">
            <img class="fe-dash-profile-card__avatar" src="{{ $professional->profile_image_url }}" alt="{{ $professional->name }}">
          </a>

          <h2 class="fe-dash-profile-card__name">
            <a href="{{ $profileUrl }}">{{ $displayName }}</a>
          </h2>
          {{--
          @if ($email)
            <p class="fe-dash-profile-card__email">{{ $email }}</p>
          @endif
          --}}
          <p class="fe-dash-profile-card__email">{{ $professional->specialization ?: $professional->professionalType?->name }}</p>

          {{--
          <div class="fe-dash-profile-card__split">
            <div class="fe-dash-profile-card__split-item is-lilac">
              <strong>{{ $professional->license_number ?: '—' }}</strong>
              <span>{{ __('backend.psychologists.license') }}</span>
            </div>
            <div class="fe-dash-profile-card__split-item is-rose">
              <strong>{{ $professional->specialization ?: $professional->professionalType?->name ?: '—' }}</strong>
              <span>{{ __('backend.psychologists.specialization') }}</span>
            </div>
          </div>

          <div class="fe-dash-profile-card__badges">
            <span class="fe-dash-pill {{ $emailVerified ? '' : 'is-pending' }}">
              <i class="bi {{ $emailVerified ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}"></i>
              {{ $emailVerified ? __('backend.psychologists.email_verified') : __('backend.psychologists.pending') }}
            </span>
            <span class="fe-dash-pill {{ $professional->is_verified ? '' : 'is-muted' }}">
              <i class="bi {{ $professional->is_verified ? 'bi-shield-fill-check' : 'bi-shield' }}"></i>
              {{ $professional->is_verified ? __('backend.psychologists.verified') : __('backend.psychologists.pending') }}
            </span>
          </div>
          --}}

          <div class="fe-psych-card__meta">
            @if ($professional->experience_years)
              <span><i class="bi bi-briefcase"></i> {{ __('frontend.professionals.years_experience', ['count' => $professional->experience_years]) }}</span>
            @endif
            @if ($professional->languages)
              <span>
                <i class="bi bi-translate"></i>
                {{ collect($professional->languages)->map(fn ($code) => \App\Support\Locales::label($code))->implode(', ') }}
              </span>
            @endif
            @if ($professional->city || $professional->country)
              <span><i class="bi bi-geo-alt"></i> {{ implode(', ', array_filter([$professional->city, $professional->country])) }}</span>
            @endif
          </div>

          @if ($professional->specializations->isNotEmpty())
            <div class="fe-psych-card__chips">
              @foreach ($professional->specializations->take(4) as $specialization)
                <span class="fe-chip">{{ $specialization->name }}</span>
              @endforeach
            </div>
          @endif

          @if ($professional->currentAbsences->isNotEmpty())
            @php $absence = $professional->currentAbsences->first(); @endphp
            <div class="fe-psych-card__away">
              <i class="bi bi-calendar-x"></i>
              <div>
                <strong>{{ __('frontend.professionals.currently_away') }}</strong>
                <span>{{ __('frontend.professionals.away_until', ['until' => \App\Support\AbsenceReason::format($absence->ends_at, 'd M Y')]) }}</span>
                @if ($absence->reason)
                  <span>{{ \App\Support\AbsenceReason::label($absence->reason) }}</span>
                @endif
              </div>
            </div>
          @endif

          <div class="fe-psych-card__availability">
            @if (count($availableDays) > 0)
              <div class="is-on">
                <span class="dot"></span>
                {{ implode(', ', array_map(fn ($day) => __('frontend.availability.days.'.$day), $availableDays)) }}
              </div>
            @endif

            <button type="button" class="fe-psych-card__avail-link" onclick="document.getElementById('{{ $modalId }}').showModal();">
              [ View full availability ]
            </button>

            @if (count($unavailableDays) > 0)
              <div class="is-off">
                <span class="dot"></span>
                {{ implode(', ', array_map(fn ($day) => __('frontend.availability.days.'.$day), $unavailableDays)) }} ({{ __('frontend.availability.unavailable') }})
              </div>
            @endif
          </div>

          <dialog id="{{ $modalId }}" class="fe-modal fe-avail-modal">
            <div class="fe-modal__panel fe-avail-modal__panel">
              <div class="fe-avail-modal__head">
                <div>
                  <span class="fe-avail-modal__eyebrow"><i class="bi bi-calendar2-week"></i> {{ __('frontend.professionals.availability') }}</span>
                  <h2>{{ $professional->name }}</h2>
                </div>
                <button type="button" class="fe-avail-modal__close" onclick="document.getElementById('{{ $modalId }}').close()" aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
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
              </div>
              <div class="fe-avail-modal__foot">
                <button type="button" class="fe-avail-modal__done" onclick="document.getElementById('{{ $modalId }}').close()">{{ __('frontend.psych_profile.close') }}</button>
              </div>
            </div>
          </dialog>

          <p class="fe-psych-card__accepting {{ $professional->is_accepting_patients ? 'is-available' : 'is-unavailable' }}">
            <i class="bi bi-circle-fill"></i>
            {{ $professional->is_accepting_patients ? __('frontend.professionals.accepting') : __('frontend.professionals.not_accepting') }}
          </p>

          <div class="fe-dash-profile-card__actions">
            <a href="{{ $profileUrl }}" class="fe-dash-btn-view">{{ __('frontend.professionals.view_profile') }}</a>
            <button
              type="button"
              class="fe-dash-btn-icon is-mint"
              onclick="document.getElementById('{{ $modalId }}').showModal();"
              aria-label="{{ __('frontend.professionals.availability') }}"
              title="{{ __('frontend.professionals.availability') }}"
            >
              <i class="bi bi-pencil-square"></i>
            </button>
            <a
              href="{{ $profileUrl }}"
              class="fe-dash-btn-icon is-rose"
              aria-label="{{ __('frontend.professionals.view_profile') }}"
              title="{{ __('frontend.professionals.view_profile') }}"
            >
              <i class="bi bi-box-arrow-up-right"></i>
            </a>
          </div>
        </div>
      </article>
    @empty
      @php
        $hasFilters = request()->filled('specialization')
          || request()->filled('language')
          || request()->filled('availability')
          || request()->boolean('accepting_only');
      @endphp
      {{-- Old: backend.no_records only --}}
      @include('frontend.partials.ui-empty', [
        'title' => $hasFilters ? __('frontend.professionals.empty_filtered') : __('frontend.professionals.empty'),
        'hint' => $hasFilters ? __('frontend.professionals.empty_filtered_hint') : __('frontend.professionals.empty_hint'),
        'actionUrl' => $hasFilters ? route('frontend.patient.psychologists.index') : null,
        'actionLabel' => $hasFilters ? __('ui.clear_filters') : null,
      ])
    @endforelse
  </div>

  @endif
@endsection
