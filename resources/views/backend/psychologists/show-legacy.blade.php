{{--
  ARCHIVE / ROLLBACK COPY — not routed.
  Previous non-tab psychologist admin show layout.
  Active view: resources/views/backend/psychologists/show.blade.php
--}}
@extends('backend.layouts.app')

@section('title', $professional->name)
@section('page_title', __('backend.psychologists.details'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.psychologists.index') }}">{{ __('backend.menu.psychologists') }}</a></li>
  <li class="breadcrumb-item active">{{ $professional->name }}</li>
@endsection

@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('admin.psychologists.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('backend.back') }}
    </a>
    <div class="d-flex flex-wrap gap-2">
      {{-- Old approve/revoke used admin.psychologists.approve and admin.psychologists.revoke. This archive view is not routed. --}}
    </div>
  </div>

  {{-- Header --}}
  <div class="card mb-3 overflow-hidden">
    <div style="height:160px;background:center/cover no-repeat url('{{ $professional->cover_image_url }}');"></div>
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-start gap-3">
        <img
          src="{{ $professional->profile_image_url }}"
          alt="{{ $professional->name }}"
          class="rounded-circle border border-3 border-white shadow-sm"
          style="width:96px;height:96px;object-fit:cover;margin-top:-56px;background:#fff;"
        >
        <div class="flex-grow-1">
          <h3 class="mb-1">
            {{ trim(($professional->title ? $professional->title.' ' : '').$professional->name) }}
          </h3>
          <p class="text-muted mb-2">
            {{ $professional->specialization ?: $professional->professionalType?->name ?: '—' }}
          </p>
          <div class="d-flex flex-wrap gap-2">
            @if ($professional->is_verified)
              <span class="badge text-bg-success">{{ __('backend.psychologists.verified') }}</span>
            @else
              <span class="badge text-bg-warning">{{ __('backend.psychologists.pending') }}</span>
            @endif
            @if ($professional->is_featured)
              <span class="badge text-bg-primary">{{ __('backend.psychologists.featured') }}</span>
            @endif
            @if ($professional->status)
              <span class="badge text-bg-secondary">{{ __('backend.psychologists.active') }}</span>
            @else
              <span class="badge text-bg-dark">{{ __('backend.psychologists.inactive') }}</span>
            @endif
            @if ($professional->is_accepting_patients)
              <span class="badge text-bg-info">{{ __('backend.psychologists.accepting') }}</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- Profile --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_profile') }}</h3></div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tbody>
              <tr><th style="width:40%">{{ __('backend.name') }}</th><td>{{ $professional->name }}</td></tr>
              <tr><th>{{ __('backend.email') }}</th><td>{{ $professional->user?->email ?: $professional->email ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.phone') }}</th><td>{{ $professional->phone ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.website') }}</th><td>@if($professional->website)<a href="{{ $professional->website }}" target="_blank" rel="noopener">{{ $professional->website }}</a>@else — @endif</td></tr>
              <tr><th>{{ __('backend.psychologists.license') }}</th><td><code>{{ $professional->license_number ?: '—' }}</code></td></tr>
              <tr><th>{{ __('backend.psychologists.organization') }}</th><td>{{ $professional->organization ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.gender') }}</th><td>{{ $professional->gender ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.date_of_birth') }}</th><td>{{ $professional->date_of_birth ? \Illuminate\Support\Carbon::parse($professional->date_of_birth)->format('Y-m-d') : '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.experience_years') }}</th><td>{{ $professional->experience_years ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.consultation_fee') }}</th><td>{{ $professional->consultation_fee !== null ? number_format((float) $professional->consultation_fee, 2) : '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.rating') }}</th><td>{{ number_format((float) $professional->rating, 1) }} ({{ $professional->total_reviews }})</td></tr>
              <tr><th>{{ __('backend.psychologists.max_patients') }}</th><td>{{ $professional->max_active_patients ?: '—' }}</td></tr>
              <tr>
                <th>{{ __('backend.psychologists.languages') }}</th>
                <td>
                  @php $langs = is_array($professional->languages) ? $professional->languages : []; @endphp
                  {{ $langs ? implode(', ', $langs) : '—' }}
                </td>
              </tr>
              <tr>
                <th>{{ __('backend.psychologists.online_offline') }}</th>
                <td>
                  {{ $professional->online_consultation ? __('backend.psychologists.online') : '' }}
                  {{ $professional->online_consultation && $professional->offline_consultation ? ' / ' : '' }}
                  {{ $professional->offline_consultation ? __('backend.psychologists.clinic') : '' }}
                  @unless ($professional->online_consultation || $professional->offline_consultation) — @endunless
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Location + bio --}}
    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_location') }}</h3></div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tbody>
              <tr><th style="width:40%">{{ __('backend.psychologists.address') }}</th><td>{{ $professional->address ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.city') }}</th><td>{{ $professional->city ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.state') }}</th><td>{{ $professional->state ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.country') }}</th><td>{{ $professional->country ?: '—' }}</td></tr>
              <tr><th>{{ __('backend.psychologists.postal_code') }}</th><td>{{ $professional->postal_code ?: '—' }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_bio') }}</h3></div>
        <div class="card-body">
          <div class="mb-3">{!! $professional->bio ? nl2br(e($professional->bio)) : '—' !!}</div>
          <div>
            <strong>{{ __('backend.psychologists.specializations') }}:</strong>
            @forelse ($professional->specializations as $spec)
              <span class="badge text-bg-light border me-1">{{ $spec->name }}</span>
            @empty
              <span class="text-muted">{{ $professional->specialization ?: '—' }}</span>
            @endforelse
          </div>
          @if (is_array($professional->credentials) && count($professional->credentials))
            <div class="mt-2">
              <strong>{{ __('backend.psychologists.credentials') }}:</strong>
              <ul class="mb-0 mt-1">
                @foreach ($professional->credentials as $credential)
                  <li>{{ is_scalar($credential) ? $credential : json_encode($credential) }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Social links --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_social') }}</h3></div>
        <div class="card-body p-0 table-responsive">
          <table class="table mb-0">
            <thead><tr><th>{{ __('backend.psychologists.platform') }}</th><th>URL</th><th>{{ __('backend.status') }}</th></tr></thead>
            <tbody>
              @forelse ($professional->socialLinks as $link)
                <tr>
                  <td>{{ $link->platform }}</td>
                  <td><a href="{{ $link->url }}" target="_blank" rel="noopener">{{ $link->url }}</a></td>
                  <td>{{ $link->status ? __('backend.yes') : __('backend.no') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted text-center py-3">{{ __('backend.psychologists.empty_section') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Documents --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_documents') }}</h3></div>
        <div class="card-body p-0 table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>{{ __('backend.psychologists.doc_title') }}</th>
                <th>{{ __('backend.psychologists.doc_type') }}</th>
                <th>{{ __('backend.psychologists.doc_verified') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($professional->documents as $document)
                <tr>
                  <td>{{ $document->title }}</td>
                  <td>{{ $document->document_type ?: '—' }}</td>
                  <td>
                    @if ($document->is_verified)
                      <span class="badge text-bg-success">{{ __('backend.yes') }}</span>
                    @else
                      <span class="badge text-bg-warning">{{ __('backend.no') }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($document->url)
                      <a href="{{ $document->url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">{{ __('backend.psychologists.open') }}</a>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted text-center py-3">{{ __('backend.psychologists.empty_section') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Gallery --}}
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_gallery') }}</h3></div>
        <div class="card-body">
          @if ($professional->galleries->isEmpty())
            <p class="text-muted mb-0">{{ __('backend.psychologists.empty_section') }}</p>
          @else
            <div class="row g-3">
              @foreach ($professional->galleries as $gallery)
                <div class="col-6 col-md-4 col-lg-3">
                  <div class="border rounded overflow-hidden h-100">
                    @if ($gallery->url)
                      <img src="{{ $gallery->url }}" alt="{{ $gallery->title }}" class="w-100" style="height:140px;object-fit:cover;">
                    @endif
                    <div class="p-2 small">
                      <strong>{{ $gallery->title ?: '—' }}</strong>
                      @if ($gallery->description)
                        <div class="text-muted">{{ \Illuminate\Support\Str::limit($gallery->description, 80) }}</div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Education --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_education') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($professional->educations as $education)
              <li class="list-group-item">
                <strong>{{ $education->degree }}</strong>
                <div class="text-muted small">
                  {{ $education->institution }}
                  @if ($education->field_of_study) · {{ $education->field_of_study }} @endif
                  @if ($education->year) · {{ $education->year }} @endif
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.psychologists.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- Experience --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_experience') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($professional->experiences as $experience)
              <li class="list-group-item">
                <strong>{{ $experience->designation }}</strong>
                <div class="text-muted small">
                  {{ $experience->organization }}
                  ·
                  {{ $experience->start_date?->format('Y') }}
                  –
                  {{ $experience->currently_working ? __('backend.psychologists.present') : $experience->end_date?->format('Y') }}
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.psychologists.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- Services --}}
    {{--<div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_services') }}</h3></div>
        <div class="card-body p-0 table-responsive">
          <table class="table mb-0">
            <thead><tr><th>{{ __('backend.name') }}</th><th>{{ __('backend.psychologists.price') }}</th><th>{{ __('backend.psychologists.duration') }}</th><th>{{ __('backend.status') }}</th></tr></thead>
            <tbody>
              @forelse ($professional->services as $service)
                <tr>
                  <td>{{ $service->name }}</td>
                  <td>{{ $service->price !== null ? number_format((float) $service->price, 2) : '—' }}</td>
                  <td>{{ $service->duration ?: '—' }}</td>
                  <td>{{ $service->status ? __('backend.yes') : __('backend.no') }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted text-center py-3">{{ __('backend.psychologists.empty_section') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>--}}

    {{-- Availability + Absences --}}
    <div class="col-lg-6">
      <div class="card mb-3">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_availability') }}</h3></div>
        <div class="card-body p-0 table-responsive">
          <table class="table mb-0">
            <thead><tr><th>{{ __('backend.psychologists.day') }}</th><th>{{ __('backend.psychologists.hours') }}</th><th>{{ __('backend.status') }}</th></tr></thead>
            <tbody>
              @forelse ($professional->availabilities as $slot)
                <tr>
                  <td>{{ $slot->day }}</td>
                  <td>{{ $slot->start_time }} – {{ $slot->end_time }}</td>
                  <td>{{ $slot->is_available ? __('backend.yes') : __('backend.no') }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-muted text-center py-3">{{ __('backend.psychologists.empty_section') }}</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_absences') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($professional->absences as $absence)
              <li class="list-group-item">
                <strong>{{ $absence->starts_at?->format('d M Y H:i') }} → {{ $absence->ends_at?->format('d M Y H:i') }}</strong>
                <div class="text-muted small">{{ $absence->reason ?: '—' }}</div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.psychologists.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- Awards --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_awards') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($professional->awards as $award)
              <li class="list-group-item">
                <strong>{{ $award->title }}</strong>
                <div class="text-muted small">
                  {{ $award->organization ?: '—' }}
                  @if ($award->year) · {{ $award->year }} @endif
                </div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.psychologists.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- Reviews --}}
    {{--<div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="card-title mb-0">{{ __('backend.psychologists.section_reviews') }}</h3></div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse ($professional->reviews as $review)
              <li class="list-group-item">
                <strong>{{ $review->reviewer_name ?? '—' }}</strong>
                <span class="badge text-bg-light border">{{ $review->rating }}/5</span>
                <div class="small mt-1">{{ \Illuminate\Support\Str::limit($review->review ?? '', 160) }}</div>
              </li>
            @empty
              <li class="list-group-item text-muted">{{ __('backend.psychologists.empty_section') }}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>--}}
@endsection
