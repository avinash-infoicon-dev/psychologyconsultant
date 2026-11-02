@extends('frontend.layouts.dashboard')

@section('title', __('frontend.patients.title'))
@section('page_title', __('frontend.patients.title'))

@section('content')
  <div class="fe-cms-card">
    <h2 style="margin-top:0;">{{ __('frontend.patients.title') }}</h2>
    <p class="fe-muted">{{ __('frontend.patients.subtitle') }}</p>

    <div class="table-responsive">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.patients.name') }}</th>
            <th>{{ __('frontend.patients.email') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($patients as $patient)
            <tr>
              <td>{{ $patient->user?->name ?: '—' }}</td>
              <td>{{ $patient->user?->email ?: '—' }}</td>
              <td class="text-nowrap">
                <a href="{{ route('frontend.psychologist.patients.show', $patient) }}" class="fe-btn fe-btn--outline">{{ __('frontend.patients.view') }}</a>
                <a href="{{ route('frontend.psychologist.patients.clinical-notes.index', $patient) }}" class="fe-btn fe-btn--primary">{{ __('frontend.clinical_notes.title') }}</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3">
                @include('frontend.partials.ui-empty', [
                  'title' => __('frontend.patients.empty'),
                  'hint' => __('frontend.patients.empty_hint'),
                ])
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($patients->hasPages())
      <div style="margin-top:1rem;">{{ $patients->links() }}</div>
    @endif
  </div>
@endsection
