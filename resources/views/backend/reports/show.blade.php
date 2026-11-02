@extends('backend.layouts.app')

@section('title', __('backend.reports.show_title', ['id' => $report->id]))
@section('page_title', __('backend.reports.show_title', ['id' => $report->id]))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">{{ __('backend.menu.reports') }}</a></li>
  <li class="breadcrumb-item active">#{{ $report->id }}</li>
@endsection

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('backend.back') }}</a>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">{{ __('backend.reports.details') }}</h3>
          <span class="badge {{ $report->adminBadgeClass() }}">{{ __('backend.reports.status_'.$report->status) }}</span>
        </div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tr>
              <th style="width:40%">{{ __('backend.reports.reporter') }}</th>
              <td>{{ $report->reporter?->name }} <span class="text-muted">{{ $report->reporter?->email }}</span></td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.reported') }}</th>
              <td>{{ $report->reportedUser?->name ?: '—' }} <span class="text-muted">{{ $report->reportedUser?->email }}</span></td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.type') }}</th>
              <td>{{ __('backend.reports.type_'.$report->type) }}</td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.reason') }}</th>
              <td>{{ __('backend.reports.reason_'.$report->reason) }}</td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.conversation') }}</th>
              <td>
                @if ($report->conversation)
                  #{{ $report->conversation->id }}
                  ({{ $report->conversation->patient?->user?->name }} → {{ $report->conversation->psychologist?->user?->name }})
                @else
                  —
                @endif
              </td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.description') }}</th>
              <td>{{ $report->description }}</td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.created_at') }}</th>
              <td>{{ $report->created_at?->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
              <th>{{ __('backend.reports.assignee') }}</th>
              <td>{{ $report->assignee?->name ?: '—' }}</td>
            </tr>
          </table>
        </div>
      </div>

      {{-- Old: single combined thread with optional internal checkbox.
      <div class="card mt-3">
        <div class="card-header">
          <h3 class="card-title mb-0">{{ __('backend.reports.thread') }}</h3>
        </div>
        <div class="card-body">
          @forelse ($report->comments as $comment)
            <article class="mb-3 pb-3 border-bottom">
              <div class="d-flex justify-content-between gap-2">
                <strong>{{ $comment->user?->name }}</strong>
                <small class="text-muted">{{ $comment->created_at?->diffForHumans() }}</small>
              </div>
              @if ($comment->is_internal)
                <span class="badge text-bg-secondary">{{ __('backend.reports.internal') }}</span>
              @endif
              <p class="mb-0 mt-1">{{ $comment->body }}</p>
            </article>
          @empty
            <p class="text-muted mb-0">{{ __('backend.reports.thread_empty') }}</p>
          @endforelse
          @permission('reports.manage')
            <form method="post" action="{{ route('admin.reports.comments.store', $report) }}" class="mt-3">
              @csrf
              <div class="mb-2">
                <label class="form-label" for="body">{{ __('backend.reports.reply') }}</label>
                <textarea name="body" id="body" class="form-control" rows="3" required>{{ old('body') }}</textarea>
              </div>
              <div class="form-check mb-3">
                <input type="checkbox" name="is_internal" id="is_internal" value="1" class="form-check-input" @checked(old('is_internal'))>
                <label class="form-check-label" for="is_internal">{{ __('backend.reports.internal_note') }}</label>
              </div>
              <button type="submit" class="btn btn-primary">{{ __('backend.reports.send') }}</button>
            </form>
          @endpermission
        </div>
      </div>
      --}}

      @include('backend.reports._thread', [
        'audience' => \App\Models\ReportComment::AUDIENCE_REPORTER,
        'title' => __('backend.reports.thread_with', ['name' => $report->reporter?->name ?: __('backend.reports.reporter')]),
        'empty' => __('backend.reports.thread_empty'),
      ])

      @include('backend.reports._thread', [
        'audience' => \App\Models\ReportComment::AUDIENCE_REPORTED,
        'title' => __('backend.reports.thread_with', ['name' => $report->reportedUser?->name ?: __('backend.reports.reported')]),
        'empty' => __('backend.reports.thread_reported_empty'),
      ])

      @include('backend.reports._thread', [
        'audience' => \App\Models\ReportComment::AUDIENCE_INTERNAL,
        'title' => __('backend.reports.thread_internal'),
        'empty' => __('backend.reports.thread_empty'),
      ])
    </div>

    <div class="col-lg-5">
      @permission('reports.manage')
        <div class="card">
          <div class="card-header">
            <h3 class="card-title mb-0">{{ __('backend.reports.manage') }}</h3>
          </div>
          <div class="card-body">
            <form method="post" action="{{ route('admin.reports.update', $report) }}">
              @csrf
              @method('PATCH')
              <div class="mb-3">
                <label for="status" class="form-label">{{ __('backend.status') }}</label>
                <select name="status" id="status" class="form-select" required>
                  @foreach (\App\Models\Report::STATUSES as $status)
                    <option value="{{ $status }}" @selected($report->status === $status)>
                      {{ __('backend.reports.status_'.$status) }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label for="assigned_to" class="form-label">{{ __('backend.reports.assignee') }}</label>
                <select name="assigned_to" id="assigned_to" class="form-select">
                  <option value="">{{ __('backend.reports.unassigned') }}</option>
                  @foreach ($admins as $admin)
                    <option value="{{ $admin->id }}" @selected((int) $report->assigned_to === $admin->id)>
                      {{ $admin->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
            </form>
          </div>
        </div>
      @endpermission

      @if (! empty($reportedPsychologist) && $reportedPsychologist->verification_status === \App\Models\Professional::VERIFICATION_VERIFIED)
        @permission('psychologists.suspend')
          <div class="card mt-3">
            <div class="card-header">
              <h3 class="card-title mb-0">{{ __('backend.reports.suspend_psychologist') }}</h3>
            </div>
            <div class="card-body">
              <p class="text-muted small">{{ __('backend.reports.suspend_psychologist_hint') }}</p>
              <form
                method="post"
                action="{{ route('admin.psychologists.suspend', $reportedPsychologist) }}"
                data-confirm="{{ __('backend.psychologists.confirm_suspend') }}"
                onsubmit="return confirm(this.dataset.confirm)"
              >
                @csrf
                <div class="mb-3">
                  <label class="form-label" for="report_suspend_reason">{{ __('backend.psychologists.reason') }}</label>
                  <textarea name="reason" id="report_suspend_reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
                  @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <button type="submit" class="btn btn-danger">{{ __('backend.psychologists.suspend') }}</button>
              </form>
            </div>
          </div>
        @endpermission
      @endif
    </div>
  </div>
@endsection
