@php
  $status = $professional->verification_status ?: 'draft';
  $canSubmit = $professional->canSubmitForVerification();
@endphp

<div class="fe-verify-banner fe-verify-banner--{{ $status }}" role="status">
  <p class="fe-verify-banner__status">{{ __('frontend.psych_profile.verification_status_'.$status) }}</p>
  <p class="fe-verify-banner__copy">{{ __('frontend.psych_profile.verification_banner_'.$status) }}</p>
  @if (in_array($status, ['rejected', 'suspended'], true) && filled($professional->verification_reason))
    <p class="fe-verify-banner__reason">{{ __('frontend.notifications.profile_rejected_reason', ['reason' => $professional->verification_reason]) }}</p>
  @endif
  @if ($canSubmit)
    <form method="post" action="{{ route('frontend.psychologist.profile.submit-verification') }}" class="fe-verify-banner__form">
      @csrf
      <button type="submit" class="fe-btn fe-btn--primary fe-btn--sm">{{ __('frontend.psych_profile.verification_submit') }}</button>
    </form>
  @endif
  @error('profile')
    <p class="fe-verify-banner__error">{{ $message }}</p>
  @enderror
  @error('verification_status')
    <p class="fe-verify-banner__error">{{ $message }}</p>
  @enderror
</div>
