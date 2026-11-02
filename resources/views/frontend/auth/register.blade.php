@php
  $site = app(\App\Services\Cms\CmsSiteSettingService::class)->current();
  $heroBg = $site->registerHeroUrl();
@endphp
@extends('frontend.layouts.guest')

@section('title', $site->registerCopy('register_hero_title', __('frontend.nav.register')))

@section('content')
<div class="tp-hero-2-wrapper">
     <div class="tp-banner-area include-bg pt-180 pb-150" data-background="{{ $heroBg }}">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12">
                     <div class="tp-hero-2-content text-center mb-25">
                         <span class="tp-hero-2-subtitle">{{ $site->registerCopy('register_hero_subtitle', __('frontend.auth.register_title')) }}</span>
                         <h1 class="tp-hero-2-title font-bold">{{ $site->registerCopy('register_hero_title', __('frontend.nav.register')) }}</h1>
                         <p class="cst-hero-text text-white mb-45">{{ $site->registerCopy('register_hero_text', __('frontend.auth.register_subtitle')) }}</p>
                     </div>
                 </div>
             </div>
         </div>
     </div>
</div>

<main>
    <section class="reg-page">
        <div class="container container-1530">
            <div class="reg-shell">
                <div class="reg-head">
                    @if ($site->registerCopy('register_kicker'))
                      <span class="cst-section-subtitle">{{ $site->registerCopy('register_kicker') }}</span>
                    @endif
                    <h2>{{ $site->registerCopy('register_heading', __('frontend.auth.register_title')) }}</h2>
                    <p>{{ $site->registerCopy('register_intro', __('frontend.auth.register_subtitle')) }}</p>
                </div>

                @if ($errors->any())
                  <div class="fe-alert fe-alert--error" style="margin:0 50px 1.25rem;">
                    <ul style="margin:0;padding-left:1.1rem;">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                    @if ($errors->has('email'))
                      <p style="margin:0.65rem 0 0;">
                        <a href="{{ route('frontend.auth.login', ['email' => old('email')]) }}">{{ __('frontend.nav.login') }}</a>
                      </p>
                    @endif
                  </div>
                @endif

                <div class="fe-role-tabs" style="max-width:420px;margin:0 auto 10px;">
                    <button type="button" class="fe-role-tab @if(old('role', 'patient') === 'patient') active @endif" data-fe-role-tab="patient">
                      {{ __('frontend.auth.role_patient') }}
                    </button>
                    <button type="button" class="fe-role-tab @if(old('role') === 'psychologist') active @endif" data-fe-role-tab="psychologist">
                      {{ __('frontend.auth.role_psychologist') }}
                    </button>
                </div>

                <form class="reg-body fe-role-fields @if(old('role', 'patient') === 'patient') active @endif" method="post" action="{{ route('frontend.auth.register') }}" data-fe-role-form="patient" id="patientRegister">
                    @csrf
                    <input type="hidden" name="role" id="register_role" value="patient">

                    <div class="reg-panel active">
                        <div class="panel-title">
                            <h3>{{ $site->registerCopy('register_form_title', __('frontend.auth.register_title')) }}</h3>
                            <p>{{ $site->registerCopy('register_form_subtitle', __('frontend.auth.register_subtitle')) }}</p>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">{{ __('frontend.auth.name') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('frontend.auth.email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">{{ __('frontend.auth.phone') }}</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ __('frontend.auth.password') }}</label>
                                <input type="password" name="password" id="password" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">{{ __('frontend.auth.password_confirmation') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required>
                            </div>
                        </div>

                        <div class="fe-role-fields @if(old('role', 'patient') === 'patient') active @endif" data-fe-role-fields="patient" style="margin-top:20px;">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="date_of_birth">{{ __('frontend.auth.date_of_birth') }}</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}">
                                </div>
                                <div class="form-group">
                                    <label for="country">{{ __('frontend.auth.country') }}</label>
                                    {{-- Legacy free-text ISO code:
                                    <input type="text" name="country" id="country" class="fe-input" maxlength="2" value="{{ old('country', 'CH') }}">
                                    --}}
                                    @php
                                      $selectedCountry = old(
                                          'country',
                                          $allowedCountries->has('CH') ? 'CH' : $allowedCountries->keys()->first()
                                      );
                                    @endphp
                                    <select name="country" id="country" required>
                                      <option value="" disabled @selected(! $selectedCountry)>
                                        {{ __('frontend.auth.country_placeholder') }}
                                      </option>
                                      @foreach ($allowedCountries as $code => $name)
                                        <option value="{{ $code }}" @selected($selectedCountry === $code)>{{ $name }}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Legacy psychologist fields on the patient form (replaced by multi-step form)
                        <div class="fe-role-fields @if(old('role') === 'psychologist') active @endif" data-fe-role-fields="psychologist">
                          ...
                        </div>
                        --}}

                        <div style="margin-top:20px;">
                            <label class="fe-check">
                                <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms')) required>
                                <span>{{ __('frontend.auth.accept_terms') }}</span>
                            </label>
                            <label class="fe-check" style="margin-top:0.5rem;">
                                <input type="checkbox" name="accept_privacy_policy" value="1" @checked(old('accept_privacy_policy')) required>
                                <span>{{ __('frontend.auth.accept_privacy') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="reg-actions">
                        <span></span>
                        <button class="reg-btn" type="submit">
                            {{ __('frontend.auth.register_button') }}
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                </form>

                @include('frontend.auth._psychologist-register')

                <div class="auth-footer" style="text-align:center;padding:0 50px 30px;">
                    {{ __('frontend.auth.has_account') }}
                    <a href="{{ route('frontend.auth.login') }}">{{ __('frontend.nav.login') }}</a>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
(function () {
  const form = document.getElementById('professionalRegister');
  if (!form) return;
  const panels = form.querySelectorAll('.reg-panel');
  const steps = form.querySelectorAll('.step');
  const prev = document.getElementById('psychPrevBtn');
  const next = document.getElementById('psychNextBtn');
  const draft = document.getElementById('psychDraftBtn');
  const submit = document.getElementById('psychSubmitBtn');
  const intent = document.getElementById('psych_intent');
  let current = 0;

  function show(n) {
    current = n;
    panels.forEach((p, i) => p.classList.toggle('active', i === n));
    steps.forEach((s, i) => s.classList.toggle('active', i <= n));
    prev.style.visibility = n === 0 ? 'hidden' : 'visible';
    const last = n === panels.length - 1;
    next.hidden = last;
    draft.hidden = !last;
    submit.hidden = !last;
  }

  const emailInput = form.querySelector('#psych_email');
  const emailHint = document.getElementById('psychEmailHint');
  const emailCheckUrl = form.dataset.emailCheck;
  const loginUrl = form.dataset.loginUrl;

  function setEmailHint(html) {
    if (!emailHint) return;
    emailHint.innerHTML = html || '';
    emailHint.hidden = !html;
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
    });
  }

  async function emailIsAvailable() {
    if (!emailInput || !emailCheckUrl) return true;
    const email = (emailInput.value || '').trim();
    if (!email) {
      setEmailHint(escapeHtml(@json(__('auth_api.validation.messages')['email.required'])));
      emailInput.focus();
      return false;
    }
    try {
      const url = emailCheckUrl + (emailCheckUrl.includes('?') ? '&' : '?') + 'email=' + encodeURIComponent(email);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      if (!res.ok) {
        const msg = (data.errors && data.errors.email && data.errors.email[0]) || data.message || '';
        setEmailHint(escapeHtml(msg));
        emailInput.focus();
        return false;
      }
      if (data.available === false) {
        const login = data.login_url || loginUrl || '';
        const label = data.login_label || @json(__('frontend.nav.login'));
        const href = login ? (login + (login.indexOf('?') >= 0 ? '&' : '?') + 'email=' + encodeURIComponent(email)) : '';
        setEmailHint(escapeHtml(data.message || '') + (href ? ' <a href="' + href + '">' + escapeHtml(label) + '</a>' : ''));
        emailInput.focus();
        return false;
      }
      setEmailHint('');
      return true;
    } catch (e) {
      return true;
    }
  }

  next.addEventListener('click', async function () {
    if (current === 0) {
      const ok = await emailIsAvailable();
      if (!ok) return;
    }
    if (current < panels.length - 1) show(current + 1);
  });
  if (emailInput) {
    emailInput.addEventListener('blur', function () {
      if ((emailInput.value || '').trim()) {
        emailIsAvailable();
      }
    });
  }
  prev.addEventListener('click', function () {
    if (current > 0) show(current - 1);
  });
  draft.addEventListener('click', function () { intent.value = 'draft'; });
  submit.addEventListener('click', function () { intent.value = 'submit'; });
  form.addEventListener('submit', function () {
    form.querySelectorAll('input, select, textarea, button').forEach((el) => { el.disabled = false; });
  });

  const lists = {
    education: { list: 'educationList', tpl: 'tpl-education' },
    experience: { list: 'experienceList', tpl: 'tpl-experience' },
    social: { list: 'socialList', tpl: 'tpl-social' },
    gallery: { list: 'galleryList', tpl: 'tpl-gallery' },
    document: { list: 'documentList', tpl: 'tpl-document' },
    award: { list: 'awardList', tpl: 'tpl-award' }
  };
  form.querySelectorAll('[data-add-repeat]').forEach((btn) => {
    btn.addEventListener('click', function () {
      const cfg = lists[btn.getAttribute('data-add-repeat')];
      if (!cfg) return;
      const list = document.getElementById(cfg.list);
      const tpl = document.getElementById(cfg.tpl);
      if (!list || !tpl) return;
      const i = list.querySelectorAll('.repeat-card').length;
      list.insertAdjacentHTML('beforeend', tpl.innerHTML.replaceAll('__i__', String(i)));
    });
  });

  show(0);
  if (form.classList.contains('active') === false) {
    form.querySelectorAll('input, select, textarea, button').forEach((el) => { el.disabled = true; });
  }
})();
</script>
@endpush

{{--
Previous register layout (kept for reference):

@extends('frontend.layouts.guest', ['hideHeader' => true, 'hideFooter' => true])

@section('title', __('frontend.auth.register_title'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('frontendassets/css/auth.css') }}">
@endpush

@section('content')
  <div class="fe-auth-page">
    @include('frontend.partials.auth-brand-panel')

    <div class="fe-auth-panel">
      <div class="fe-auth-box">
        @include('frontend.partials.locale-switch')

        <h2>{{ __('frontend.auth.register_title') }}</h2>
        <!-- <p class="fe-subtitle">{{ __('frontend.auth.register_subtitle') }}</p> -->

        @if ($errors->any())
          <div class="fe-alert fe-alert--error">
            <ul style="margin:0;padding-left:1.1rem;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="post" action="{{ route('frontend.auth.register') }}">
          @csrf
          <input type="hidden" name="role" id="register_role" value="{{ old('role', 'patient') }}">

          <div class="fe-role-tabs">
            <button type="button" class="fe-role-tab @if(old('role', 'patient') === 'patient') active @endif" data-fe-role-tab="patient">
              {{ __('frontend.auth.role_patient') }}
            </button>
            <button type="button" class="fe-role-tab @if(old('role') === 'psychologist') active @endif" data-fe-role-tab="psychologist">
              {{ __('frontend.auth.role_psychologist') }}
            </button>
          </div>

          <div class="fe-form-group">
            <label for="name">{{ __('frontend.auth.name') }}</label>
            <input type="text" name="name" id="name" class="fe-input" value="{{ old('name') }}" required>
          </div>
          <div class="fe-form-group">
            <label for="email">{{ __('frontend.auth.email') }}</label>
            <input type="email" name="email" id="email" class="fe-input" value="{{ old('email') }}" required>
          </div>
          <div class="fe-form-group">
            <label for="phone">{{ __('frontend.auth.phone') }}</label>
            <input type="text" name="phone" id="phone" class="fe-input" value="{{ old('phone') }}">
          </div>
          <div class="fe-form-group">
            <label for="password">{{ __('frontend.auth.password') }}</label>
            <input type="password" name="password" id="password" class="fe-input" required>
          </div>
          <div class="fe-form-group">
            <label for="password_confirmation">{{ __('frontend.auth.password_confirmation') }}</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="fe-input" required>
          </div>

          <div class="fe-role-fields @if(old('role', 'patient') === 'patient') active @endif" data-fe-role-fields="patient">
            <div class="fe-form-group">
              <label for="date_of_birth">{{ __('frontend.auth.date_of_birth') }}</label>
              <input type="date" name="date_of_birth" id="date_of_birth" class="fe-input" value="{{ old('date_of_birth') }}">
            </div>
            <div class="fe-form-group">
              <label for="country">{{ __('frontend.auth.country') }}</label>
              <select name="country" id="country" class="fe-select" required>
                ...
              </select>
            </div>
          </div>

          <label class="fe-check">
            <input type="checkbox" name="accept_terms" value="1" @checked(old('accept_terms')) required>
            <span>{{ __('frontend.auth.accept_terms') }}</span>
          </label>
          <label class="fe-check" style="margin-top:0.5rem;">
            <input type="checkbox" name="accept_privacy_policy" value="1" @checked(old('accept_privacy_policy')) required>
            <span>{{ __('frontend.auth.accept_privacy') }}</span>
          </label>

          <button type="submit" class="fe-btn fe-btn--primary fe-btn--block" style="margin-top:1rem;">
            {{ __('frontend.auth.register_button') }}
          </button>
        </form>

        <p class="fe-auth-footer">
          {{ __('frontend.auth.has_account') }}
          <a href="{{ route('frontend.auth.login') }}">{{ __('frontend.nav.login') }}</a>
        </p>
      </div>
    </div>
  </div>
@endsection
--}}
