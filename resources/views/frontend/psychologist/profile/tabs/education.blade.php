@php
  $openAdd = $errors->any() && old('form_context') === 'education_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.education') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="education-add">
      {{ __('frontend.psych_profile.add_education') }}
    </button>
  </div>

  @if ($professional->educations->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.education') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.degree') }}</th>
            <th>{{ __('frontend.psych_profile.fields.institution') }}</th>
            <th>{{ __('frontend.psych_profile.fields.field_of_study') }}</th>
            <th>{{ __('frontend.psych_profile.fields.year') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->educations as $education)
            <tr>
              <td>{{ $education->degree }}</td>
              <td>{{ $education->institution }}</td>
              <td>{{ $education->field_of_study ?: '—' }}</td>
              <td>{{ $education->year ?: '—' }}</td>
              <td>
                <div class="fe-table-actions">
                  <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="education-edit-{{ $education->id }}">
                    {{ __('frontend.edit') }}
                  </button>
                  <form action="{{ route('frontend.psychologist.profile.educations.destroy', $education) }}" method="post"
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

@foreach ($professional->educations as $education)
  @php
    $editId = 'education-edit-'.$education->id;
    $openEdit = $errors->any() && old('form_context') === 'education_edit_'.$education->id;
  @endphp
  <dialog id="{{ $editId }}" class="fe-modal fe-modal--edit" @if ($openEdit) open @endif>
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h3>{{ __('frontend.edit') }} — {{ __('frontend.psych_profile.tabs.education') }}</h3>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
      </div>
      <form action="{{ route('frontend.psychologist.profile.educations.update', $education) }}" method="post" class="fe-modal__body">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_context" value="education_edit_{{ $education->id }}">
        <div class="fe-form-grid">
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.degree') }}</label>
            <input type="text" name="degree" class="fe-input" required
              value="{{ $openEdit ? old('degree') : $education->degree }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.institution') }}</label>
            <input type="text" name="institution" class="fe-input" required
              value="{{ $openEdit ? old('institution') : $education->institution }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.field_of_study') }}</label>
            <input type="text" name="field_of_study" class="fe-input"
              value="{{ $openEdit ? old('field_of_study') : $education->field_of_study }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.year') }}</label>
            <input type="number" name="year" class="fe-input" min="1900" max="2100"
              value="{{ $openEdit ? old('year') : $education->year }}">
          </div>
          <div class="fe-form-group" style="grid-column:1/-1;">
            <label>{{ __('frontend.psych_profile.fields.description') }}</label>
            <textarea name="description" class="fe-input" rows="3">{{ $openEdit ? old('description') : $education->description }}</textarea>
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

<dialog id="education-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_education') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.educations.store') }}" method="post" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="education_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="edu_degree">{{ __('frontend.psych_profile.fields.degree') }}</label>
          <input type="text" name="degree" id="edu_degree" class="fe-input" required value="{{ $openAdd ? old('degree') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="edu_institution">{{ __('frontend.psych_profile.fields.institution') }}</label>
          <input type="text" name="institution" id="edu_institution" class="fe-input" required value="{{ $openAdd ? old('institution') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="edu_field">{{ __('frontend.psych_profile.fields.field_of_study') }}</label>
          <input type="text" name="field_of_study" id="edu_field" class="fe-input" value="{{ $openAdd ? old('field_of_study') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="edu_year">{{ __('frontend.psych_profile.fields.year') }}</label>
          <input type="number" name="year" id="edu_year" class="fe-input" min="1900" max="2100" value="{{ $openAdd ? old('year') : '' }}">
        </div>
        <div class="fe-form-group" style="grid-column:1/-1;">
          <label for="edu_description">{{ __('frontend.psych_profile.fields.description') }}</label>
          <textarea name="description" id="edu_description" class="fe-input" rows="3">{{ $openAdd ? old('description') : '' }}</textarea>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
