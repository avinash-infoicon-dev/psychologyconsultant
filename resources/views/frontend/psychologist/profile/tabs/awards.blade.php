@php
  $openAdd = $errors->any() && old('form_context') === 'award_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.awards') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="award-add">
      {{ __('frontend.psych_profile.add_award') }}
    </button>
  </div>

  @if ($professional->awards->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.awards') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.title_label') }}</th>
            <th>{{ __('frontend.psych_profile.fields.organization') }}</th>
            <th>{{ __('frontend.psych_profile.fields.year') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->awards as $award)
            <tr>
              <td>{{ $award->title }}</td>
              <td>{{ $award->organization ?: '—' }}</td>
              <td>{{ $award->year ?: '—' }}</td>
              <td>
                <div class="fe-table-actions">
                  <button type="button" class="fe-btn fe-btn--outline fe-btn--sm" data-fe-modal-open="award-edit-{{ $award->id }}">
                    {{ __('frontend.edit') }}
                  </button>
                  <form action="{{ route('frontend.psychologist.profile.awards.destroy', $award) }}" method="post"
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

@foreach ($professional->awards as $award)
  @php
    $editId = 'award-edit-'.$award->id;
    $openEdit = $errors->any() && old('form_context') === 'award_edit_'.$award->id;
  @endphp
  <dialog id="{{ $editId }}" class="fe-modal fe-modal--edit" @if ($openEdit) open @endif>
    <div class="fe-modal__panel">
      <div class="fe-modal__head">
        <h3>{{ __('frontend.edit') }} — {{ __('frontend.psych_profile.tabs.awards') }}</h3>
        <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
      </div>
      <form action="{{ route('frontend.psychologist.profile.awards.update', $award) }}" method="post" class="fe-modal__body">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_context" value="award_edit_{{ $award->id }}">
        <div class="fe-form-grid">
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.title_label') }}</label>
            <input type="text" name="title" class="fe-input" required
              value="{{ $openEdit ? old('title') : $award->title }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.organization') }}</label>
            <input type="text" name="organization" class="fe-input"
              value="{{ $openEdit ? old('organization') : $award->organization }}">
          </div>
          <div class="fe-form-group">
            <label>{{ __('frontend.psych_profile.fields.year') }}</label>
            <input type="number" name="year" class="fe-input" min="1900" max="2100"
              value="{{ $openEdit ? old('year') : $award->year }}">
          </div>
          <div class="fe-form-group" style="grid-column:1/-1;">
            <label>{{ __('frontend.psych_profile.fields.description') }}</label>
            <textarea name="description" class="fe-input" rows="3">{{ $openEdit ? old('description') : $award->description }}</textarea>
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

<dialog id="award-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_award') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.awards.store') }}" method="post" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="award_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="award_title">{{ __('frontend.psych_profile.fields.title_label') }}</label>
          <input type="text" name="title" id="award_title" class="fe-input" required value="{{ $openAdd ? old('title') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="award_org">{{ __('frontend.psych_profile.fields.organization') }}</label>
          <input type="text" name="organization" id="award_org" class="fe-input" value="{{ $openAdd ? old('organization') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="award_year">{{ __('frontend.psych_profile.fields.year') }}</label>
          <input type="number" name="year" id="award_year" class="fe-input" min="1900" max="2100" value="{{ $openAdd ? old('year') : '' }}">
        </div>
        <div class="fe-form-group" style="grid-column:1/-1;">
          <label for="award_description">{{ __('frontend.psych_profile.fields.description') }}</label>
          <textarea name="description" id="award_description" class="fe-input" rows="3">{{ $openAdd ? old('description') : '' }}</textarea>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
