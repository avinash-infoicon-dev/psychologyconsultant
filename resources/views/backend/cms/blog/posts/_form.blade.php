@php($item = $post ?? null)
<div class="row mb-3">
  <div class="col-md-4 mb-3">
    <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $item?->slug) }}">
    <div class="form-text">{{ __('backend.cms.slug_help') }}</div>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label" for="cms_blog_category_id">{{ __('backend.cms.blog.category') }}</label>
    <select name="cms_blog_category_id" id="cms_blog_category_id" class="form-select">
      <option value="">{{ __('backend.cms.no_category') }}</option>
      @foreach ($categories as $cat)
        <option value="{{ $cat->id }}" @selected((string) old('cms_blog_category_id', $item?->cms_blog_category_id) === (string) $cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="sort_order">{{ __('backend.cms.sort_order') }}</label>
    <input type="number" min="0" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
  </div>
  <div class="col-md-2 mb-3">
    <label class="form-label" for="published_at">{{ __('backend.cms.published_at') }}</label>
    <input type="datetime-local" name="published_at" id="published_at" class="form-control"
      value="{{ old('published_at', optional($item?->published_at)->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="video_url">{{ __('backend.cms.blog.posts.video_url') }}</label>
    <input type="url" name="video_url" id="video_url" class="form-control" value="{{ old('video_url', $item?->video_url) }}">
  </div>
  <div class="col-md-3 mb-3 d-flex align-items-end">
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item?->is_active ?? true))>
      <label class="form-check-label" for="is_active">{{ __('backend.active') }}</label>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label" for="cover">{{ __('backend.cms.blog.posts.cover') }}</label>
    <input type="file" name="cover" id="cover" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($item?->cover_url)
      <div class="mt-2 d-flex align-items-center gap-2">
        <img src="{{ $item->cover_url }}" alt="" style="width:96px;height:56px;object-fit:cover;border-radius:6px;">
        <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" name="remove_cover" value="1" id="remove_cover">
          <label class="form-check-label" for="remove_cover">{{ __('backend.cms.blog.posts.remove_image') }}</label>
        </div>
      </div>
    @endif
  </div>
  {{--<div class="col-md-6 mb-3">
    <label class="form-label" for="author_photo">{{ __('backend.cms.blog.posts.author_photo') }}</label>
    <input type="file" name="author_photo" id="author_photo" class="form-control" accept="image/jpeg,image/png,image/webp">
    @if ($item?->author_photo_url)
      <div class="mt-2 d-flex align-items-center gap-2">
        <img src="{{ $item->author_photo_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:50%;">
        <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" name="remove_author_photo" value="1" id="remove_author_photo">
          <label class="form-check-label" for="remove_author_photo">{{ __('backend.cms.blog.posts.remove_image') }}</label>
        </div>
      </div>
    @endif
  </div>--}}
</div>

<ul class="nav nav-tabs" role="tablist">
  @foreach ($locales as $i => $locale)
    <li class="nav-item" role="presentation">
      <button class="nav-link @if($i === 0) active @endif" data-bs-toggle="tab" data-bs-target="#blog-post-locale-{{ $locale }}" type="button">
        {{ \App\Support\Locales::label($locale) }}
      </button>
    </li>
  @endforeach
</ul>
<div class="tab-content border border-top-0 p-3">
  @foreach ($locales as $i => $locale)
    @php($tr = $item?->translations->firstWhere('locale', $locale))
    <div class="tab-pane fade @if($i === 0) show active @endif" id="blog-post-locale-{{ $locale }}">
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.title') }}</label>
        <input type="text" name="translations[{{ $locale }}][title]" class="form-control" required
          value="{{ old("translations.$locale.title", $tr?->title) }}">
      </div>
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.blog.posts.excerpt') }}</label>
        <textarea name="translations[{{ $locale }}][excerpt]" class="form-control" rows="3">{{ old("translations.$locale.excerpt", $tr?->excerpt) }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.content') }}</label>
        <textarea name="translations[{{ $locale }}][content]" class="form-control" rows="8">{{ old("translations.$locale.content", $tr?->content) }}</textarea>
      </div>
      {{--<div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.blog.posts.author') }}</label>
          <input type="text" name="translations[{{ $locale }}][author_name]" class="form-control"
            value="{{ old("translations.$locale.author_name", $tr?->author_name) }}">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.blog.posts.author_role') }}</label>
          <input type="text" name="translations[{{ $locale }}][author_role]" class="form-control"
            value="{{ old("translations.$locale.author_role", $tr?->author_role) }}">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.blog.posts.author_bio') }}</label>
        <textarea name="translations[{{ $locale }}][author_bio]" class="form-control" rows="3">{{ old("translations.$locale.author_bio", $tr?->author_bio) }}</textarea>
      </div>--}}
      <div class="mb-3">
        <label class="form-label">{{ __('backend.cms.blog.posts.quote') }}</label>
        <textarea name="translations[{{ $locale }}][quote]" class="form-control" rows="2">{{ old("translations.$locale.quote", $tr?->quote) }}</textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">{{ __('backend.cms.blog.posts.quote_author') }}</label>
          <input type="text" name="translations[{{ $locale }}][quote_author]" class="form-control"
            value="{{ old("translations.$locale.quote_author", $tr?->quote_author) }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.blog.posts.read_time') }}</label>
          <input type="text" name="translations[{{ $locale }}][read_time]" class="form-control"
            value="{{ old("translations.$locale.read_time", $tr?->read_time) }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">{{ __('backend.cms.blog.posts.tags') }}</label>
          <input type="text" name="translations[{{ $locale }}][tags]" class="form-control"
            value="{{ old("translations.$locale.tags", $tr?->tags) }}" placeholder="Blog, Stress">
        </div>
      </div>
    </div>
  @endforeach
</div>
