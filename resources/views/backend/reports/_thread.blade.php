@php
  $threadComments = $report->comments->where('audience', $audience);
@endphp

<div class="card mt-3">
  <div class="card-header">
    <h3 class="card-title mb-0">{{ $title }}</h3>
  </div>
  <div class="card-body">
    @forelse ($threadComments as $comment)
      <article class="mb-3 pb-3 border-bottom">
        <div class="d-flex justify-content-between gap-2">
          <strong>{{ $comment->user?->name }}</strong>
          <small class="text-muted">{{ $comment->created_at?->diffForHumans() }}</small>
        </div>
        @if ($audience === \App\Models\ReportComment::AUDIENCE_INTERNAL)
          <span class="badge text-bg-secondary">{{ __('backend.reports.internal') }}</span>
        @endif
        <p class="mb-0 mt-1">{{ $comment->body }}</p>
      </article>
    @empty
      <p class="text-muted mb-0">{{ $empty }}</p>
    @endforelse

    @permission('reports.manage')
      <form method="post" action="{{ route('admin.reports.comments.store', $report) }}" class="mt-3">
        @csrf
        <input type="hidden" name="audience" value="{{ $audience }}">
        <div class="mb-2">
          <label class="form-label" for="body-{{ $audience }}">{{ __('backend.reports.reply') }}</label>
          <textarea name="body" id="body-{{ $audience }}" class="form-control" rows="3" required>{{ old('audience') === $audience ? old('body') : '' }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('backend.reports.send') }}</button>
      </form>
    @endpermission
  </div>
</div>
