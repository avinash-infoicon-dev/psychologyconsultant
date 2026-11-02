@php
  $openAdd = $errors->any() && old('form_context') === 'gallery_add';
@endphp

<section class="fe-profile-section-block">
  <div class="fe-profile-toolbar">
    <h3>{{ __('frontend.psych_profile.tabs.gallery') }}</h3>
    <button type="button" class="fe-btn fe-btn--primary" data-fe-modal-open="gallery-add">
      {{ __('frontend.psych_profile.add_gallery') }}
    </button>
  </div>

  @if ($professional->galleries->isEmpty())
    <p class="fe-muted">{{ __('frontend.psych_profile.empty.gallery') }}</p>
  @else
    <div class="fe-table-wrap">
      <table class="fe-table">
        <thead>
          <tr>
            <th>{{ __('frontend.psych_profile.fields.image') }}</th>
            <th>{{ __('frontend.psych_profile.fields.title_label') }}</th>
            <th>{{ __('frontend.psych_profile.fields.sort_order') }}</th>
            <th>{{ __('frontend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($professional->galleries as $gallery)
            <tr>
              <td>
                <img src="{{ $gallery->url }}" alt="{{ $gallery->title }}" class="fe-table-thumb">
              </td>
              <td>
                <strong>{{ $gallery->title ?: __('frontend.psych_profile.untitled') }}</strong>
                @if ($gallery->description)
                  <div class="fe-muted" style="font-size:.88rem;margin-top:.2rem;">{{ \Illuminate\Support\Str::limit($gallery->description, 80) }}</div>
                @endif
              </td>
              <td>{{ $gallery->sort_order }}</td>
              <td>
                <div class="fe-table-actions">
                  <form action="{{ route('frontend.psychologist.profile.galleries.destroy', $gallery) }}" method="post"
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

<dialog id="gallery-add" class="fe-modal" @if ($openAdd) open @endif>
  <div class="fe-modal__panel">
    <div class="fe-modal__head">
      <h3>{{ __('frontend.psych_profile.add_gallery') }}</h3>
      <button type="button" class="fe-modal__close" data-fe-modal-close aria-label="{{ __('frontend.psych_profile.close') }}">&times;</button>
    </div>
    <form action="{{ route('frontend.psychologist.profile.galleries.store') }}" method="post" enctype="multipart/form-data" class="fe-modal__body">
      @csrf
      <input type="hidden" name="form_context" value="gallery_add">
      <div class="fe-form-grid">
        <div class="fe-form-group">
          <label for="gallery_title">{{ __('frontend.psych_profile.fields.title_label') }}</label>
          <input type="text" name="title" id="gallery_title" class="fe-input" value="{{ $openAdd ? old('title') : '' }}">
        </div>
        <div class="fe-form-group">
          <label for="gallery_sort">{{ __('frontend.psych_profile.fields.sort_order') }}</label>
          <input type="number" name="sort_order" id="gallery_sort" class="fe-input" min="0" value="{{ $openAdd ? old('sort_order', 0) : 0 }}">
        </div>
        <div class="fe-form-group">
          <label for="gallery_image">{{ __('frontend.psych_profile.fields.image') }}</label>
          <input type="file" name="image" id="gallery_image" class="fe-input" accept="image/*" required>
        </div>
        <div class="fe-form-group" style="grid-column:1/-1;">
          <label for="gallery_description">{{ __('frontend.psych_profile.fields.description') }}</label>
          <textarea name="description" id="gallery_description" class="fe-input" rows="3">{{ $openAdd ? old('description') : '' }}</textarea>
        </div>
      </div>
      <div class="fe-form-actions fe-form-actions--inline">
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.psych_profile.add') }}</button>
        <button type="button" class="fe-btn fe-btn--outline" data-fe-modal-close>{{ __('frontend.cancel') }}</button>
      </div>
    </form>
  </div>
</dialog>
