@php
  $languagesValue = old('languages', is_array($professional->languages)
    ? implode(', ', $professional->languages)
    : (string) ($professional->languages ?? ''));
  $selectedSpecs = collect(old('specialization_ids', $professional->specializations->pluck('id')->all()))
    ->map(fn ($id) => (int) $id)
    ->all();
@endphp

<form action="{{ route('frontend.psychologist.profile.update') }}" method="post" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="fe-profile-media-row">
    <div class="fe-form-group">
      <label>{{ __('frontend.psych_profile.fields.profile_image') }}</label>
      <div class="fe-profile-media-preview">
        <img src="{{ $professional->profile_image_url }}" alt="">
      </div>
      <input type="file" name="profile_image" class="fe-input" accept="image/*">
    </div>
    <div class="fe-form-group">
      <label>{{ __('frontend.psych_profile.fields.cover_image') }}</label>
      <div class="fe-profile-media-preview fe-profile-media-preview--cover">
        <img src="{{ $professional->cover_image_url }}" alt="">
      </div>
      <input type="file" name="cover_image" class="fe-input" accept="image/*">
    </div>
  </div>

  <div class="fe-form-grid">
    <div class="fe-form-group">
      <label for="name">{{ __('frontend.psych_profile.fields.name') }}</label>
      <input type="text" name="name" id="name" class="fe-input" required
        value="{{ old('name', $professional->user?->name ?? $professional->name) }}">
    </div>
    <div class="fe-form-group">
      <label for="email">{{ __('frontend.psych_profile.fields.email') }}</label>
      <input type="email" id="email" class="fe-input" value="{{ $professional->user?->email }}" disabled>
    </div>
    <div class="fe-form-group">
      <label for="phone">{{ __('frontend.psych_profile.fields.phone') }}</label>
      <input type="text" name="phone" id="phone" class="fe-input"
        value="{{ old('phone', $professional->phone ?? $professional->user?->phone) }}">
    </div>
    <div class="fe-form-group">
      <label for="locale">{{ __('frontend.psych_profile.fields.locale') }}</label>
      <select name="locale" id="locale" class="fe-select">
        @include('frontend.partials.locale-options', ['selected' => old('locale', $professional->user?->locale)])
      </select>
    </div>
    <div class="fe-form-group">
      <label for="title">{{ __('frontend.psych_profile.fields.title') }}</label>
      <input type="text" name="title" id="title" class="fe-input" value="{{ old('title', $professional->title) }}">
    </div>
    <div class="fe-form-group">
      <label for="first_name">{{ __('frontend.psych_profile.fields.first_name') }}</label>
      <input type="text" name="first_name" id="first_name" class="fe-input"
        value="{{ old('first_name', $professional->first_name) }}">
    </div>
    <div class="fe-form-group">
      <label for="last_name">{{ __('frontend.psych_profile.fields.last_name') }}</label>
      <input type="text" name="last_name" id="last_name" class="fe-input"
        value="{{ old('last_name', $professional->last_name) }}">
    </div>
    <div class="fe-form-group">
      <label for="gender">{{ __('frontend.psych_profile.fields.gender') }}</label>
      <input type="text" name="gender" id="gender" class="fe-input" value="{{ old('gender', $professional->gender) }}">
    </div>
    <div class="fe-form-group">
      <label for="date_of_birth">{{ __('frontend.psych_profile.fields.date_of_birth') }}</label>
      <input type="date" name="date_of_birth" id="date_of_birth" class="fe-input"
        value="{{ old('date_of_birth', $professional->date_of_birth?->format('Y-m-d')) }}">
    </div>
    <div class="fe-form-group">
      <label for="website">{{ __('frontend.psych_profile.fields.website') }}</label>
      <input type="url" name="website" id="website" class="fe-input" value="{{ old('website', $professional->website) }}">
    </div>
    <div class="fe-form-group">
      <label for="organization">{{ __('frontend.psych_profile.fields.organization') }}</label>
      <input type="text" name="organization" id="organization" class="fe-input"
        value="{{ old('organization', $professional->organization) }}">
    </div>
    <div class="fe-form-group">
      <label for="license_number">{{ __('frontend.psych_profile.fields.license_number') }}</label>
      <input type="text" name="license_number" id="license_number" class="fe-input"
        value="{{ old('license_number', $professional->license_number) }}">
    </div>
    <div class="fe-form-group">
      <label for="specialization">{{ __('frontend.psych_profile.fields.specialization_text') }}</label>
      <input type="text" name="specialization" id="specialization" class="fe-input"
        value="{{ old('specialization', $professional->specialization) }}">
    </div>
    <div class="fe-form-group">
      <label for="experience_years">{{ __('frontend.psych_profile.fields.experience_years') }}</label>
      <input type="number" name="experience_years" id="experience_years" class="fe-input" min="0" max="80"
        value="{{ old('experience_years', $professional->experience_years) }}">
    </div>
    <div class="fe-form-group">
      <label for="consultation_fee">{{ __('frontend.psych_profile.fields.consultation_fee') }}</label>
      <input type="number" step="0.01" min="0" name="consultation_fee" id="consultation_fee" class="fe-input"
        value="{{ old('consultation_fee', $professional->consultation_fee) }}">
    </div>
    <div class="fe-form-group">
      <label for="languages">{{ __('frontend.psych_profile.fields.languages') }}</label>
      <input type="text" name="languages" id="languages" class="fe-input"
        value="{{ $languagesValue }}" placeholder="{{ __('frontend.psych_profile.fields.languages_hint') }}">
    </div>
    <div class="fe-form-group">
      <label for="address">{{ __('frontend.psych_profile.fields.address') }}</label>
      <input type="text" name="address" id="address" class="fe-input" value="{{ old('address', $professional->address) }}">
    </div>
    <div class="fe-form-group">
      <label for="city">{{ __('frontend.psych_profile.fields.city') }}</label>
      <input type="text" name="city" id="city" class="fe-input" value="{{ old('city', $professional->city) }}">
    </div>
    <div class="fe-form-group">
      <label for="state">{{ __('frontend.psych_profile.fields.state') }}</label>
      <input type="text" name="state" id="state" class="fe-input" value="{{ old('state', $professional->state) }}">
    </div>
    <div class="fe-form-group">
      <label for="country">{{ __('frontend.psych_profile.fields.country') }}</label>
      <input type="text" name="country" id="country" class="fe-input" value="{{ old('country', $professional->country) }}">
    </div>
    <div class="fe-form-group">
      <label for="postal_code">{{ __('frontend.psych_profile.fields.postal_code') }}</label>
      <input type="text" name="postal_code" id="postal_code" class="fe-input"
        value="{{ old('postal_code', $professional->postal_code) }}">
    </div>
  </div>

  <div class="fe-form-group" style="margin-top:1rem;">
    <label for="bio">{{ __('frontend.psych_profile.fields.bio') }}</label>
    <textarea name="bio" id="bio" class="fe-input" rows="5">{{ old('bio', $professional->bio) }}</textarea>
  </div>

  @if ($specializations->isNotEmpty())
    <div class="fe-form-group" style="margin-top:1rem;">
      <label>{{ __('frontend.psych_profile.fields.specializations') }}</label>
      <div class="fe-checkbox-grid">
        @foreach ($specializations as $spec)
          <label class="fe-checkbox-row">
            @if ($spec->isSelectable())
              <input type="checkbox" name="specialization_ids[]" value="{{ $spec->id }}"
                @checked(in_array((int) $spec->id, $selectedSpecs, true))>
              <span>{{ $spec->name }}</span>
            @else
              <input type="hidden" name="specialization_ids[]" value="{{ $spec->id }}">
              <input type="checkbox" checked disabled>
              <span>
                {{ $spec->name }} —
                @if ($spec->isArchived())
                  {{ __('frontend.psych_profile.fields.specialization_archived') }}
                @else
                  {{ __('backend.inactive') }}
                @endif
              </span>
            @endif
          </label>
        @endforeach
      </div>
    </div>
  @endif

  <div class="fe-checkbox-grid" style="margin-top:1rem;">
    <label class="fe-checkbox-row">
      <input type="checkbox" name="online_consultation" value="1" @checked(old('online_consultation', $professional->online_consultation))>
      <span>{{ __('frontend.psych_profile.fields.online_consultation') }}</span>
    </label>
    <label class="fe-checkbox-row">
      <input type="checkbox" name="offline_consultation" value="1" @checked(old('offline_consultation', $professional->offline_consultation))>
      <span>{{ __('frontend.psych_profile.fields.offline_consultation') }}</span>
    </label>
    <label class="fe-checkbox-row">
      <input type="checkbox" name="is_accepting_patients" value="1" @checked(old('is_accepting_patients', $professional->is_accepting_patients))>
      <span>{{ __('frontend.psych_profile.fields.is_accepting_patients') }}</span>
    </label>
  </div>

  <div class="fe-form-actions">
    <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.save') }}</button>
  </div>
</form>
