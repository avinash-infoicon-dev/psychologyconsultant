@extends('frontend.layouts.dashboard')

@section('title', __('frontend.absences.title'))
@section('page_title', __('frontend.absences.title'))

@section('content')
  {{-- Success flash is rendered by frontend.layouts.dashboard --}}
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ __('frontend.absences.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ __('frontend.absences.subtitle') }}</p>
      </div>
      <a href="{{ route('frontend.psychologist.absences.create') }}" class="fe-btn fe-btn--primary">{{ __('frontend.absences.create') }}</a>
    </div>

    @if ($currentAbsence)
      <div class="fe-alert fe-alert--warning">
        {{ __('frontend.absences.currently_away', [
          'until' => \App\Support\AbsenceReason::format($currentAbsence->ends_at),
        ]) }}
      </div>
    @endif

    @if (false)
    @forelse ($absences as $absence)
      <article class="fe-note-card">
        <div class="fe-note-card__meta">
          <strong>
            {{ $absence->starts_at?->format('d M Y H:i') }}
            →
            {{ $absence->ends_at?->format('d M Y H:i') }}
          </strong>
          <span>
            <a href="{{ route('frontend.psychologist.absences.edit', $absence) }}">{{ __('frontend.edit') }}</a>
            ·
            <form action="{{ route('frontend.psychologist.absences.destroy', $absence) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('frontend.confirm_delete')))">
              @csrf
              @method('DELETE')
              <button type="submit" class="fe-link-btn">{{ __('frontend.delete') }}</button>
            </form>
          </span>
        </div>
        <p class="fe-muted" style="margin:0;">{{ \App\Support\AbsenceReason::label($absence->reason) }}</p>
      </article>
    @empty
      @include('frontend.partials.ui-empty', [
        'title' => __('frontend.absences.empty'),
        'hint' => __('frontend.absences.empty_hint'),
        'actionUrl' => route('frontend.psychologist.absences.create'),
        'actionLabel' => __('frontend.absences.create'),
      ])
    @endforelse
    @endif

    @if ($absences->isEmpty())
      @include('frontend.partials.ui-empty', [
        'title' => __('frontend.absences.empty'),
        'hint' => __('frontend.absences.empty_hint'),
        'actionUrl' => route('frontend.psychologist.absences.create'),
        'actionLabel' => __('frontend.absences.create'),
      ])
    @else
      <div class="fe-table-wrap">
        <table class="fe-table">
          <thead>
            <tr>
              <th>{{ __('frontend.absences.starts_at') }}</th>
              <th>{{ __('frontend.absences.ends_at') }}</th>
              <th>{{ __('frontend.absences.reason') }}</th>
              <th>{{ __('frontend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($absences as $absence)
              <tr>
                <td>{{ \App\Support\AbsenceReason::format($absence->starts_at) }}</td>
                <td>{{ \App\Support\AbsenceReason::format($absence->ends_at) }}</td>
                <td>{{ \App\Support\AbsenceReason::label($absence->reason) }}</td>
                <td>
                  <div class="fe-table-actions">
                    <a href="{{ route('frontend.psychologist.absences.edit', $absence) }}" class="fe-btn fe-btn--outline fe-btn--sm">
                      {{ __('frontend.edit') }}
                    </a>
                    <form action="{{ route('frontend.psychologist.absences.destroy', $absence) }}" method="post"
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

    @if ($absences->hasPages())
      <div style="margin-top:1rem;">{{ $absences->links() }}</div>
    @endif
  </div>
@endsection
