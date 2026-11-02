@php
  $w = fn (string $key) => $site->psychWizardCopy($key, __('frontend.auth.psych_wizard.'.$key));
  $f = fn (string $key) => __('frontend.psych_profile.fields.'.$key);
  $eduRows = old('education', [[]]);
  $expRows = old('experience', [[]]);
  $socialRows = old('social', [[]]);
  $galleryRows = old('gallery', [[]]);
  $docRows = old('documents', [[]]);
  $awardRows = old('awards', [[]]);
  $isPsych = old('role') === 'psychologist';
@endphp
<style>
  .form-group.spec-picker input[type="checkbox"] {
    width: auto !important;
    min-height: auto !important;
    padding: 0 !important;
  }
  .fe-field-hint {
    margin: 6px 0 0;
    font-size: 13px;
    line-height: 1.4;
    color: #c0392b;
  }
  .fe-field-hint a { color: inherit; font-weight: 600; }
</style>

<form class="reg-body fe-role-fields @if($isPsych) active @endif" id="professionalRegister" data-fe-role-form="psychologist"
      method="post" action="{{ route('frontend.auth.register') }}" enctype="multipart/form-data"
      data-email-check="{{ route('frontend.auth.register.email') }}"
      data-login-url="{{ route('frontend.auth.login') }}">
    @csrf
    <input type="hidden" name="role" value="psychologist">
    <input type="hidden" name="intent" id="psych_intent" value="{{ old('intent', 'draft') }}">

    <div class="steps" id="regSteps">
        @foreach (range(1, 7) as $n)
          <div class="step {{ $n === 1 ? 'active' : '' }}">
            <div class="step-num">{{ $n }}</div><span>{{ $w('step_'.$n) }}</span>
          </div>
        @endforeach
    </div>

    <div class="reg-panel active" data-reg-panel="0">
        <div class="panel-title">
            <h3>{{ $w('panel_1_title') }}</h3>
            <p>{{ $w('panel_1_text') }}</p>
        </div>
        <div class="form-grid">
            <div class="form-group"><label>{{ $f('profile_image') }}</label><input type="file" name="profile_photo" accept="image/*" class="file-input"></div>
            <div class="form-group"><label>{{ $f('cover_image') }}</label><input type="file" name="cover_image" accept="image/*" class="file-input"></div>
            <div class="form-group"><label>{{ $f('name') }}</label><input name="name" value="{{ old('name') }}"></div>
            <div class="form-group">
                <label>{{ __('frontend.auth.email') }}</label>
                <input type="email" name="email" id="psych_email" value="{{ old('email') }}">
                <p id="psychEmailHint" class="fe-field-hint" hidden></p>
            </div>
            <div class="form-group"><label>{{ __('frontend.auth.phone') }}</label><input name="phone" value="{{ old('phone') }}"></div>
            <div class="form-group">
                <label>{{ $f('locale') }}</label>
                <select name="locale">
                    @foreach (\App\Support\Locales::codes() as $code)
                      <option value="{{ $code }}" @selected(old('locale', app()->getLocale()) === $code)>{{ \App\Support\Locales::label($code) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>{{ __('frontend.auth.password') }}</label><input type="password" name="password"></div>
            <div class="form-group"><label>{{ __('frontend.auth.password_confirmation') }}</label><input type="password" name="password_confirmation"></div>
            <div class="form-group"><label>{{ $f('title_label') }}</label><input name="title" value="{{ old('title') }}"></div>
            <div class="form-group"><label>{{ $f('first_name') }}</label><input name="first_name" value="{{ old('first_name') }}"></div>
            <div class="form-group"><label>{{ $f('last_name') }}</label><input name="last_name" value="{{ old('last_name') }}"></div>
            <div class="form-group">
                <label>{{ $f('gender') }}</label>
                <select name="gender">
                    <option value="">{{ __('frontend.auth.gender_placeholder') }}</option>
                    <option value="female" @selected(in_array(old('gender'), ['female', 'Female'], true))>{{ __('frontend.auth.gender_female') }}</option>
                    <option value="male" @selected(in_array(old('gender'), ['male', 'Male'], true))>{{ __('frontend.auth.gender_male') }}</option>
                    <option value="other" @selected(in_array(old('gender'), ['other', 'Other'], true))>{{ __('frontend.auth.gender_other') }}</option>
                </select>
            </div>
            <div class="form-group"><label>{{ $f('date_of_birth') }}</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"></div>
            <div class="form-group"><label>{{ $f('website') }}</label><input type="url" name="website" value="{{ old('website') }}"></div>
            <div class="form-group"><label>{{ $f('organization') }}</label><input name="organization" value="{{ old('organization') }}"></div>
            <div class="form-group"><label>{{ __('frontend.auth.license_number') }}</label><input name="license_number" value="{{ old('license_number') }}"></div>
            {{-- Legacy free-text specialty:
            <div class="form-group"><label>{{ $f('specialization_text') }}</label><input name="specialization" value="{{ old('specialization') }}"></div>
            --}}
            <div class="form-group full spec-picker">
                <label>{{ $f('specializations') }}</label>
                @php
                  $specializations = $specializations ?? collect();
                  $selectedSpecIds = collect(old('specialization_ids', []))->map(fn ($id) => (int) $id)->all();
                @endphp
                @if ($specializations->isNotEmpty())
                  <div class="fe-checkbox-grid" style="margin-top:8px;">
                    @foreach ($specializations as $spec)
                      <label class="fe-check" style="align-items:center;">
                        <input type="checkbox" name="specialization_ids[]" value="{{ $spec->id }}"
                          @checked(in_array((int) $spec->id, $selectedSpecIds, true))>
                        <span>{{ $spec->name }}</span>
                      </label>
                    @endforeach
                  </div>
                @else
                  <p class="fe-muted" style="margin:8px 0 0;">{{ __('frontend.auth.no_specializations') }}</p>
                @endif
            </div>
            <div class="form-group"><label>{{ $f('experience_years') }}</label><input type="number" min="0" max="70" name="years_experience" value="{{ old('years_experience') }}"></div>
            <div class="form-group"><label>{{ $f('consultation_fee') }}</label><input name="consultation_fee" value="{{ old('consultation_fee') }}"></div>
            <div class="form-group"><label>{{ $f('languages') }}</label><input name="languages" value="{{ old('languages') }}" placeholder="{{ $f('languages_hint') }}"></div>
            <div class="form-group"><label>{{ $f('address') }}</label><input name="address" value="{{ old('address') }}"></div>
            <div class="form-group"><label>{{ $f('city') }}</label><input name="city" value="{{ old('city') }}"></div>
            <div class="form-group"><label>{{ $f('state') }}</label><input name="state" value="{{ old('state') }}"></div>
            <div class="form-group"><label>{{ $f('country') }}</label><input name="country" value="{{ old('country') }}"></div>
            <div class="form-group"><label>{{ $f('postal_code') }}</label><input name="postal_code" value="{{ old('postal_code') }}"></div>
            <div class="form-group full"><label>{{ $f('bio') }}</label><textarea name="bio">{{ old('bio') }}</textarea></div>
            {{-- Legacy free-text specializations list:
            <div class="form-group full"><label>{{ $f('specializations') }}</label><textarea name="specializations_text">{{ old('specializations_text') }}</textarea></div>
            --}}
        </div>
    </div>

    <div class="reg-panel" data-reg-panel="1">
        <div class="panel-title"><h3>{{ $w('panel_2_title') }}</h3><p>{{ $w('panel_2_text') }}</p></div>
        <div id="educationList">
            @foreach ($eduRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ $f('degree') }}</label><input name="education[{{ $i }}][degree]" value="{{ $row['degree'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('institution') }}</label><input name="education[{{ $i }}][institution]" value="{{ $row['institution'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('field_of_study') }}</label><input name="education[{{ $i }}][field_of_study]" value="{{ $row['field_of_study'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('year') }}</label><input type="number" name="education[{{ $i }}][year]" value="{{ $row['year'] ?? '' }}"></div>
                    <div class="form-group full"><label>{{ $f('description') }}</label><textarea name="education[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="education">{{ $w('add_education') }}</button>
    </div>

    <div class="reg-panel" data-reg-panel="2">
        <div class="panel-title"><h3>{{ $w('panel_3_title') }}</h3><p>{{ $w('panel_3_text') }}</p></div>
        <div id="experienceList">
            @foreach ($expRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ $f('designation') }}</label><input name="experience[{{ $i }}][role]" value="{{ $row['role'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('organization') }}</label><input name="experience[{{ $i }}][organization]" value="{{ $row['organization'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('start_date') }}</label><input type="date" name="experience[{{ $i }}][start_date]" value="{{ $row['start_date'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('end_date') }}</label><input type="date" name="experience[{{ $i }}][end_date]" value="{{ $row['end_date'] ?? '' }}"></div>
                    <div class="form-group full"><label><input type="checkbox" name="experience[{{ $i }}][current]" value="1" @checked(!empty($row['current']))> {{ $f('currently_working') }}</label></div>
                    <div class="form-group full"><label>{{ $f('description') }}</label><textarea name="experience[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="experience">{{ $w('add_experience') }}</button>
    </div>

    <div class="reg-panel" data-reg-panel="3">
        <div class="panel-title"><h3>{{ $w('panel_4_title') }}</h3><p>{{ $w('panel_4_text') }}</p></div>
        <div id="socialList">
            @foreach ($socialRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ $f('platform') }}</label><input name="social[{{ $i }}][platform]" value="{{ $row['platform'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('url') }}</label><input type="url" name="social[{{ $i }}][url]" value="{{ $row['url'] ?? '' }}"></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="social">{{ $w('add_social') }}</button>
    </div>

    <div class="reg-panel" data-reg-panel="4">
        <div class="panel-title"><h3>{{ $w('panel_5_title') }}</h3><p>{{ $w('panel_5_text') }}</p></div>
        <div id="galleryList">
            @foreach ($galleryRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label>
                      <input name="gallery[{{ $i }}][title]" value="{{ $row['title'] ?? '' }}">
                    </div>
                    <div class="form-group"><label>{{ $f('sort_order') }}</label><input type="number" name="gallery[{{ $i }}][sort_order]" value="{{ $row['sort_order'] ?? $i + 1 }}"></div>
                    <div class="form-group"><label>{{ $f('image') }}</label><input type="file" name="gallery[{{ $i }}][image]" accept="image/*" class="file-input"></div>
                    <div class="form-group full"><label>{{ $f('description') }}</label><textarea name="gallery[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="gallery">{{ $w('add_gallery') }}</button>
    </div>

    <div class="reg-panel" data-reg-panel="5">
        <div class="panel-title"><h3>{{ $w('panel_6_title') }}</h3><p>{{ $w('panel_6_text') }}</p></div>
        <div id="documentList">
            @foreach ($docRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label>
                      <input name="documents[{{ $i }}][title]" value="{{ $row['title'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label>{{ $f('document_type') }}</label>
                        <select name="documents[{{ $i }}][type]">
                            <option value="License" @selected(($row['type'] ?? '') === 'License')>{{ __('frontend.auth.doc_license') }}</option>
                            <option value="Certificate" @selected(($row['type'] ?? '') === 'Certificate')>{{ __('frontend.auth.doc_certificate') }}</option>
                            <option value="Qualification" @selected(($row['type'] ?? '') === 'Qualification')>{{ __('frontend.auth.doc_qualification') }}</option>
                            <option value="Other" @selected(($row['type'] ?? '') === 'Other')>{{ __('frontend.auth.doc_other') }}</option>
                        </select>
                    </div>
                    <div class="form-group full"><label>{{ $f('document') }}</label><input type="file" name="documents[{{ $i }}][file]" class="file-input"></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="document">{{ $w('add_document') }}</button>
    </div>

    <div class="reg-panel" data-reg-panel="6">
        <div class="panel-title"><h3>{{ $w('panel_7_title') }}</h3><p>{{ $w('panel_7_text') }}</p></div>
        <div id="awardList">
            @foreach ($awardRows as $i => $row)
              <div class="repeat-card">
                @if ($i > 0)<button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button>@endif
                <div class="form-grid">
                    <div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label><input name="awards[{{ $i }}][title]" value="{{ $row['title'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('organization') }}</label><input name="awards[{{ $i }}][organization]" value="{{ $row['organization'] ?? '' }}"></div>
                    <div class="form-group"><label>{{ $f('year') }}</label><input type="number" name="awards[{{ $i }}][year]" value="{{ $row['year'] ?? '' }}"></div>
                    <div class="form-group full"><label>{{ $f('description') }}</label><textarea name="awards[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea></div>
                </div>
              </div>
            @endforeach
        </div>
        <button type="button" class="add-btn" data-add-repeat="award">{{ $w('add_award') }}</button>

        <div style="margin-top:28px;">
            <label class="fe-check">
                <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms'))>
                <span>{{ __('frontend.auth.accept_terms') }}</span>
            </label>
            <label class="fe-check" style="margin-top:0.5rem;">
                <input type="checkbox" name="accept_privacy_policy" value="1" @checked(old('accept_privacy_policy'))>
                <span>{{ __('frontend.auth.accept_privacy') }}</span>
            </label>
        </div>
    </div>

    <div class="reg-actions">
        <button type="button" class="reg-btn prev" id="psychPrevBtn">{{ $w('btn_previous') }}</button>
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            <button type="button" class="reg-btn" id="psychNextBtn">{{ $w('btn_continue') }} <i class="fa-solid fa-arrow-right ms-2"></i></button>
            <button type="submit" class="reg-btn prev" id="psychDraftBtn" hidden>{{ $w('btn_save_draft') }}</button>
            <button type="submit" class="reg-btn" id="psychSubmitBtn" hidden>{{ $w('btn_submit_review') }}</button>
        </div>
    </div>
</form>

<template id="tpl-education">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ $f('degree') }}</label><input name="education[__i__][degree]"></div><div class="form-group"><label>{{ $f('institution') }}</label><input name="education[__i__][institution]"></div><div class="form-group"><label>{{ $f('field_of_study') }}</label><input name="education[__i__][field_of_study]"></div><div class="form-group"><label>{{ $f('year') }}</label><input type="number" name="education[__i__][year]"></div><div class="form-group full"><label>{{ $f('description') }}</label><textarea name="education[__i__][description]"></textarea></div></div></div>
</template>
<template id="tpl-experience">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ $f('designation') }}</label><input name="experience[__i__][role]"></div><div class="form-group"><label>{{ $f('organization') }}</label><input name="experience[__i__][organization]"></div><div class="form-group"><label>{{ $f('start_date') }}</label><input type="date" name="experience[__i__][start_date]"></div><div class="form-group"><label>{{ $f('end_date') }}</label><input type="date" name="experience[__i__][end_date]"></div><div class="form-group full"><label><input type="checkbox" name="experience[__i__][current]" value="1"> {{ $f('currently_working') }}</label></div><div class="form-group full"><label>{{ $f('description') }}</label><textarea name="experience[__i__][description]"></textarea></div></div></div>
</template>
<template id="tpl-social">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ $f('platform') }}</label><input name="social[__i__][platform]"></div><div class="form-group"><label>{{ $f('url') }}</label><input type="url" name="social[__i__][url]"></div></div></div>
</template>
<template id="tpl-gallery">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label><input name="gallery[__i__][title]"></div><div class="form-group"><label>{{ $f('sort_order') }}</label><input type="number" name="gallery[__i__][sort_order]"></div><div class="form-group"><label>{{ $f('image') }}</label><input type="file" name="gallery[__i__][image]" accept="image/*" class="file-input"></div><div class="form-group full"><label>{{ $f('description') }}</label><textarea name="gallery[__i__][description]"></textarea></div></div></div>
</template>
<template id="tpl-document">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label><input name="documents[__i__][title]"></div><div class="form-group"><label>{{ $f('document_type') }}</label><select name="documents[__i__][type]"><option value="License">{{ __('frontend.auth.doc_license') }}</option><option value="Certificate">{{ __('frontend.auth.doc_certificate') }}</option><option value="Qualification">{{ __('frontend.auth.doc_qualification') }}</option><option value="Other">{{ __('frontend.auth.doc_other') }}</option></select></div><div class="form-group full"><label>{{ $f('document') }}</label><input type="file" name="documents[__i__][file]" class="file-input"></div></div></div>
</template>
<template id="tpl-award">
  <div class="repeat-card"><button type="button" class="remove-item" onclick="this.parentElement.remove()">{{ $w('btn_remove') }}</button><div class="form-grid"><div class="form-group"><label>{{ __('frontend.psych_profile.fields.name') }}</label><input name="awards[__i__][title]"></div><div class="form-group"><label>{{ $f('organization') }}</label><input name="awards[__i__][organization]"></div><div class="form-group"><label>{{ $f('year') }}</label><input type="number" name="awards[__i__][year]"></div><div class="form-group full"><label>{{ $f('description') }}</label><textarea name="awards[__i__][description]"></textarea></div></div></div>
</template>
