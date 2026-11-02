<div class="fe-alert fe-alert--error" role="alert">
  <strong>{{ $title ?? __('ui.error_title') }}</strong>
  @if (! empty($message))
    <p class="mb-0 mt-1">{{ $message }}</p>
  @endif
  @if (! empty($retryUrl))
    <a href="{{ $retryUrl }}" class="fe-btn fe-btn--outline fe-btn--sm mt-2">{{ $retryLabel ?? __('ui.error_retry') }}</a>
  @endif
</div>
