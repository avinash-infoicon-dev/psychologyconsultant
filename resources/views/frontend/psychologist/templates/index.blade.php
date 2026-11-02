@extends('frontend.layouts.dashboard')

@section('title', __('frontend.templates.title'))
@section('page_title', __('frontend.templates.title'))

@section('content')
  {{-- Success flash is rendered by frontend.layouts.dashboard --}}
  <div class="fe-cms-card">
    <div class="fe-exchange-queue__head">
      <div>
        <h2 style="margin:0;">{{ __('frontend.templates.title') }}</h2>
        <p class="fe-muted" style="margin:.35rem 0 0;">{{ __('frontend.templates.subtitle') }}</p>
      </div>
      <a href="{{ route('frontend.psychologist.templates.create') }}" class="fe-btn fe-btn--primary">{{ __('frontend.templates.create') }}</a>
    </div>

    @forelse ($templates as $template)
      <article class="fe-note-card">
        <div class="fe-note-card__meta">
          <strong>{{ $template->title }}</strong>
          <span>
            <a href="{{ route('frontend.psychologist.templates.edit', $template) }}">{{ __('frontend.edit') }}</a>
            ·
            <form action="{{ route('frontend.psychologist.templates.destroy', $template) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('frontend.confirm_delete')))">
              @csrf
              @method('DELETE')
              <button type="submit" class="fe-link-btn">{{ __('frontend.delete') }}</button>
            </form>
          </span>
        </div>
        <p style="white-space:pre-wrap;">{{ $template->body }}</p>
      </article>
    @empty
      @include('frontend.partials.ui-empty', [
        'title' => __('frontend.templates.empty'),
        'hint' => __('frontend.templates.empty_hint'),
        'actionUrl' => route('frontend.psychologist.templates.create'),
        'actionLabel' => __('frontend.templates.create'),
      ])
    @endforelse

    @if ($templates->hasPages())
      <div style="margin-top:1rem;">{{ $templates->links() }}</div>
    @endif
  </div>
@endsection
