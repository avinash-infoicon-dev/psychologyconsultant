@php
  $openAdd = $errors->any() && old('form_context') === 'experience_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.experience') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="experience-add">
      {{ __('frontend.psych_profile.add_experience') }}
    </button>
  </div>

  @if ($professional->experiences->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.experience') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.designation') }}</th>
            <th>{{ __('frontend.psych_profile.fields.organization') }}</th>
            <th>{{ __('frontend.psych_profile.fields.start_date') }}</th>
            <th>{{ __('frontend.psych_profile.fields.end_date') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->experiences as $experience)
            <tr>
              <td>{{ $experience->designation }}</td>
              <td>{{ $experience->organization }}</td>
              <td>{{ $experience->start_date?->format('Y-m-d') ?: '—' }}</td>
              <td>
                @if ($experience->currently_working)
                  {{ __('frontend.psych_profile.fields.currently_working') }}
                @else
                  {{ $experience->end_date?->format('Y-m-d') ?: '—' }}
                @endif
              </td>
              <td>
                <div class="fe-table-actions">
                  <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="experience-edit-{{ $experience->id }}">
                    {{ __('frontend.edit') }}
                  </button>
                  <form action="{{ route('frontend.psychologist.profile.experiences.destroy', $experience) }}" method="post"
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

@foreach ($professional->experiences as $experience)
  @php
    $editId = 'experience-edit-'.$experience->id;
    $openEdit = $errors->any() && old('form_context') === 'experience_edit_'.$experience->id;
  @endphp
  <dialog id="{{ $editId }}" class="fe-modal fe-modal--edit" @if ($openEdit) open @endif>
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h3>{{ __('frontend.edit') }} — {{ __('frontend.psych_profile.tabs.experience') }}</h3>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
      </div>
      <form action="{{ route('frontend.psychologist.profile.experiences.update', $experience) }}" method="post" class="fe-modal__body">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_context" value="experience_edit_{{ $experience->id }}">
        <div class="fe-form-grid">
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.designation') }}</label>
            <input type="text" name="designation" class="fe-input" required
              value="{{ $openEdit ? old('designation') : $experience->designation }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.organization') }}</label>
            <input type="text" name="organization" class="fe-input" required
              value="{{ $openEdit ? old('organization') : $experience->organization }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.start_date') }}</label>
            <input type="date" name="start_date" class="fe-input"
              value="{{ $openEdit ? old('start_date') : $experience->start_date?->format('Y-m-d') }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.end_date') }}</label>
            <input type="date" name="end_date" class="fe-input"
              value="{{ $openEdit ? old('end_date') : $experience->end_date?->format('Y-m-d') }}">
          </div>
          <div class="fe-form-group" style="grid-column:1/-1;">
            <label class="fe-checkbox-row">
              <input type="checkbox" name="currently_working" value="1"
                @checked($openEdit ? old('currently_working') : $experience->currently_working)>
              <span>{{ __('frontend.psych_profile.fields.currently_working') }}</span>
            </label>
          </div>
          <div class="fe-form-group" style="grid-column:1/-1;">
            <label>{{ __('frontend.psych_profile.fields.description') }}</label>
            <textarea name="description" class="fe-input" rows="3">{{ $openEdit ? old('description') : $experience->description }}</textarea>
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

<dialog id="experience-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_experience') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.experiences.store') }}" method="post" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="experience_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="exp_designation">{{ __('frontend.psych_profile.fields.designation') }}</label>
          <input type="text" name="designation" id="exp_designation" class="fe-input" required value="{{ $openAdd ? old('designation') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="exp_organization">{{ __('frontend.psych_profile.fields.organization') }}</label>
          <input type="text" name="organization" id="exp_organization" class="fe-input" required value="{{ $openAdd ? old('organization') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="exp_start">{{ __('frontend.psych_profile.fields.start_date') }}</label>
          <input type="date" name="start_date" id="exp_start" class="fe-input" value="{{ $openAdd ? old('start_date') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="exp_end">{{ __('frontend.psych_profile.fields.end_date') }}</label>
          <input type="date" name="end_date" id="exp_end" class="fe-input" value="{{ $openAdd ? old('end_date') : '' }}">
        </div>
        <div class="fe-form-group" style="grid-column:1/-1;">
          <label class="fe-checkbox-row">
            <input type="checkbox" name="currently_working" value="1" @checked($openAdd && old('currently_working'))>
            <span>{{ __('frontend.psych_profile.fields.currently_working') }}</span>
          </label>
        </div>
        <div class="fe-form-group" style="grid-column:1/-1;">
          <label for="exp_description">{{ __('frontend.psych_profile.fields.description') }}</label>
          <textarea name="description" id="exp_description" class="fe-input" rows="3">{{ $openAdd ? old('description') : '' }}</textarea>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
