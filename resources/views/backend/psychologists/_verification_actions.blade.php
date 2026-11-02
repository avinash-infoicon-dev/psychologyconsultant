@php
  $status = $professional->verification_status ?: 'draft';
@endphp

{{-- Old Approve/Revoke lived in the page header. Status actions are in the Verification card. --}}

@if ($status === 'submitted')
  @permission('psychologists.verify')
    <form
      action="{{ route('admin.psychologists.start-review', $psychologist) }}"
      method="post"
      data-confirm="{{ __('backend.psychologists.confirm_start_review') }}"
      onsubmit="return confirm(this.dataset.confirm)"
    >
      @csrf
      <p class="text-muted small mb-2">{{ __('backend.psychologists.actions_start_review_hint') }}</p>
      <button type="submit" class="btn btn-primary">{{ __('backend.psychologists.start_review') }}</button>
    </form>
  @endpermission
@elseif ($status === 'under_review')
  @permission('psychologists.verify')
    <div class="d-flex flex-column gap-3">
      <form
        action="{{ route('admin.psychologists.verify', $psychologist) }}"
        method="post"
        data-confirm="{{ __('backend.psychologists.confirm_verify') }}"
        onsubmit="return confirm(this.dataset.confirm)"
      >
        @csrf
        <button type="submit" class="btn btn-success">{{ __('backend.psychologists.verify') }}</button>
      </form>
      <form
        action="{{ route('admin.psychologists.reject', $psychologist) }}"
        method="post"
        data-confirm="{{ __('backend.psychologists.confirm_reject') }}"
        onsubmit="return confirm(this.dataset.confirm)"
      >
        @csrf
        <label class="form-label" for="reject_reason">{{ __('backend.psychologists.reason') }}</label>
        <textarea name="reason" id="reject_reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
        @error('reason')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <button type="submit" class="btn btn-outline-danger mt-3">{{ __('backend.psychologists.reject') }}</button>
      </form>
    </div>
  @endpermission
@elseif ($status === 'verified')
  @permission('psychologists.suspend')
    <form
      action="{{ route('admin.psychologists.suspend', $psychologist) }}"
      method="post"
      data-confirm="{{ __('backend.psychologists.confirm_suspend') }}"
      onsubmit="return confirm(this.dataset.confirm)"
    >
      @csrf
      <p class="text-muted small mb-2">{{ __('backend.psychologists.actions_suspend_hint') }}</p>
      <label class="form-label" for="suspend_reason">{{ __('backend.psychologists.reason') }}</label>
      <textarea name="reason" id="suspend_reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required>{{ old('reason') }}</textarea>
      @error('reason')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="mt-3">
        <button type="submit" class="btn btn-danger">{{ __('backend.psychologists.suspend') }}</button>
      </div>
    </form>
  @endpermission
@elseif ($status === 'suspended')
  @permission('psychologists.suspend')
    <form
      action="{{ route('admin.psychologists.restore', $psychologist) }}"
      method="post"
      data-confirm="{{ __('backend.psychologists.confirm_restore') }}"
      onsubmit="return confirm(this.dataset.confirm)"
    >
      @csrf
      <p class="text-muted small mb-2">{{ __('backend.psychologists.actions_restore_hint') }}</p>
      <button type="submit" class="btn btn-success">{{ __('backend.psychologists.restore') }}</button>
    </form>
  @endpermission
@else
  <p class="text-muted mb-0">{{ __('backend.psychologists.actions_none') }}</p>
@endif
