@php
  $openAdd = $errors->any() && old('form_context') === 'document_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.documents') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="document-add">
      {{ __('frontend.psych_profile.add_document') }}
    </button>
  </div>

  @if ($professional->documents->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.documents') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.title_label') }}</th>
            <th>{{ __('frontend.psych_profile.fields.document_type') }}</th>
            <th>{{ __('frontend.psych_profile.verified') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->documents as $document)
            <tr>
              <td>{{ $document->title }}</td>
              <td>{{ $document->document_type ?: '—' }}</td>
              <td>{{ $document->is_verified ? __('frontend.psych_profile.verified') : '—' }}</td>
              <td>
                <div class="fe-table-actions">
                  <a href="{{ route('frontend.psychologist.profile.documents.download', $document) }}" class="fe-btn fe-btn--outline fe-btn--sm">
                    {{ __('frontend.psych_profile.download') }}
                  </a>
                  <form action="{{ route('frontend.psychologist.profile.documents.destroy', $document) }}" method="post"
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

<dialog id="document-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_document') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.documents.store') }}" method="post" enctype="multipart/form-data" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="document_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="doc_title">{{ __('frontend.psych_profile.fields.title_label') }}</label>
          <input type="text" name="title" id="doc_title" class="fe-input" required value="{{ $openAdd ? old('title') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="doc_type">{{ __('frontend.psych_profile.fields.document_type') }}</label>
          <input type="text" name="document_type" id="doc_type" class="fe-input"
            value="{{ $openAdd ? old('document_type') : '' }}"
            placeholder="{{ __('frontend.psych_profile.fields.document_type_hint') }}">
        </div>
        <div class="fe-form-group">
          <label for="doc_file">{{ __('frontend.psych_profile.fields.document') }}</label>
          <input type="file" name="document" id="doc_file" class="fe-input"
            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
