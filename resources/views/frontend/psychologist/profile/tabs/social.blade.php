@php
  $openAdd = $errors->any() && old('form_context') === 'social_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.social') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="social-add">
      {{ __('frontend.psych_profile.add_social') }}
    </button>
  </div>

  @if ($professional->socialLinks->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.social') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.platform') }}</th>
            <th>{{ __('frontend.psych_profile.fields.url') }}</th>
            <th>{{ __('frontend.active') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->socialLinks as $link)
            <tr>
              <td>{{ $link->platform }}</td>
              <td>
                <a href="{{ $link->url }}" target="_blank" rel="noopener" class="fe-table-link">{{ \Illuminate\Support\Str::limit($link->url, 48) }}</a>
              </td>
              <td>{{ $link->status ? __('frontend.active') : '—' }}</td>
              <td>
                <div class="fe-table-actions">
                  <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="social-edit-{{ $link->id }}">
                    {{ __('frontend.edit') }}
                  </button>
                  <form action="{{ route('frontend.psychologist.profile.social-links.destroy', $link) }}" method="post"
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
</section>

@foreach ($professional->socialLinks as $link)
  @php
    $editId = 'social-edit-'.$link->id;
    $openEdit = $errors->any() && old('form_context') === 'social_edit_'.$link->id;
  @endphp
  <dialog id="{{ $editId }}" class="fe-modal fe-modal--edit" @if ($openEdit) open @endif>
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h3>{{ __('frontend.edit') }} — {{ __('frontend.psych_profile.tabs.social') }}</h3>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
      </div>
      <form action="{{ route('frontend.psychologist.profile.social-links.update', $link) }}" method="post" class="fe-modal__body">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_context" value="social_edit_{{ $link->id }}">
        <div class="fe-form-grid">
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.platform') }}</label>
            <input type="text" name="platform" class="fe-input" required
              value="{{ $openEdit ? old('platform') : $link->platform }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.url') }}</label>
            <input type="url" name="url" class="fe-input" required
              value="{{ $openEdit ? old('url') : $link->url }}">
          </div>
          <div class="fe-form-group">
            <label class="fe-checkbox-row" style="margin-top:1.6rem;">
              <input type="checkbox" name="status" value="1"
                @checked($openEdit ? old('status') : $link->status)>
              <span>{{ __('frontend.active') }}</span>
            </label>
          </div>
        </div>
        <div class="fe-form-actions fe-form-actions--inline">
          <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
          <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
        </div>
      </form>
    </div>
  </dialog>
@endforeach

<dialog id="social-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_social') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.social-links.store') }}" method="post" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="social_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="social_platform">{{ __('frontend.psych_profile.fields.platform') }}</label>
          <input type="text" name="platform" id="social_platform" class="fe-input" required
            value="{{ $openAdd ? old('platform') : '' }}" placeholder="LinkedIn, Website…">
        </div>
        <div class="fe-form-group">
          <label for="social_url">{{ __('frontend.psych_profile.fields.url') }}</label>
          <input type="url" name="url" id="social_url" class="fe-input" required value="{{ $openAdd ? old('url') : '' }}">
        </div>
        <div class="fe-form-group">
          <label class="fe-checkbox-row" style="margin-top:1.6rem;">
            <input type="checkbox" name="status" value="1" @checked(! $openAdd || old('status', true))>
            <span>{{ __('frontend.active') }}</span>
          </label>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
