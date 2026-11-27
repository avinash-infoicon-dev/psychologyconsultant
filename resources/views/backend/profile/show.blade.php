@extends('backend.layouts.app')

@section('title', __('backend.profile.title'))
@section('page_title', __('backend.profile.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.profile.title') }}</li>
@endsection

@push('styles')
  <style>
    .admin-profile {
      --ap-accent: #3567d6;
      --ap-ink: #17243b;
      --ap-muted: #68778d;
      --ap-line: #e8edf4;
    }

    /* ---------- Hero ---------- */
    .admin-profile__hero {
      position: relative;
      display: flex;
      align-items: center;
      gap: 1.25rem;
      overflow: hidden;
      padding: 1.5rem 1.75rem;
      border: 1px solid #dfe8fb;
      border-radius: 1rem;
      background: linear-gradient(115deg, #f3f7ff 0%, #fff 58%, #f5f8ff 100%);
      box-shadow: 0 8px 26px rgba(34, 65, 120, .06);
    }

    .admin-profile__hero::after {
      position: absolute;
      top: -5.5rem;
      right: 7rem;
      width: 14rem;
      height: 14rem;
      border: 1.5rem solid rgba(53, 103, 214, .045);
      border-radius: 50%;
      content: "";
      pointer-events: none;
    }

    .admin-profile__avatar {
      position: relative;
      z-index: 1;
      display: grid;
      width: 4rem;
      height: 4rem;
      flex: 0 0 4rem;
      place-items: center;
      border-radius: 50%;
      background: linear-gradient(145deg, #477be7, #2855bd);
      box-shadow: 0 8px 18px rgba(53, 103, 214, .23);
      color: #fff;
      font-size: 1.25rem;
      font-weight: 700;
    }

    .admin-profile__avatar--photo {
      overflow: hidden;
      padding: 0;
    }

    .admin-profile__avatar--photo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .admin-profile__identity {
      position: relative;
      z-index: 1;
      min-width: 0;
    }

    .admin-profile__name {
      margin: 0 0 .2rem;
      color: var(--ap-ink);
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .admin-profile__email {
      margin: 0 0 .55rem;
      color: var(--ap-muted);
      font-size: .88rem;
    }

    .admin-profile__roles {
      display: flex;
      flex-wrap: wrap;
      gap: .35rem;
      align-items: center;
    }

    .admin-profile__role {
      display: inline-flex;
      align-items: center;
      padding: .25rem .6rem;
      border: 1px solid #dce6fa;
      border-radius: 999px;
      background: rgba(255, 255, 255, .86);
      color: #315ebd;
      font-size: .72rem;
      font-weight: 650;
    }

    .admin-profile__role--empty {
      color: var(--ap-muted);
      border-color: var(--ap-line);
      background: transparent;
    }

    /* ---------- Grid ---------- */
    .admin-profile__grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.15rem;
      margin-top: 1.15rem;
    }

    .admin-profile__avatar-row { display: flex; align-items: center; gap: 1rem; }
    .admin-profile__avatar--lg { width: 4.5rem; height: 4.5rem; flex-basis: 4.5rem; font-size: 1.25rem; }
    .admin-profile__avatar-field { min-width: 0; flex: 1 1 auto; }
    .admin-profile__input--file { padding: .45rem .6rem; }
    .admin-profile__hint { margin-top: .4rem; color: var(--ap-muted); font-size: .78rem; }
    .admin-profile__error { margin-top: .4rem; color: #b42318; font-size: .8rem; }
    .admin-profile__check { display: flex; align-items: center; gap: .5rem; margin-top: 1rem; color: #46566e; font-size: .85rem; }
    .admin-profile__checkbox { width: 1.05rem; height: 1.05rem; accent-color: var(--ap-accent); }

    [data-bs-theme="dark"] .admin-profile__check { color: #b9c6d8; }
    [data-bs-theme="dark"] .admin-profile__hint { color: #93a2b8; }

    /* ---------- Card ---------- */
    .admin-profile__card {
      display: flex;
      flex-direction: column;
      overflow: hidden;
      border: 1px solid var(--ap-line);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 6px 24px rgba(24, 39, 75, .045);
    }

    .admin-profile__card-head {
      display: flex;
      align-items: center;
      gap: .6rem;
      padding: 1.05rem 1.25rem;
      border-bottom: 1px solid var(--ap-line);
    }

    .admin-profile__card-icon {
      display: grid;
      width: 2.1rem;
      height: 2.1rem;
      flex: 0 0 2.1rem;
      place-items: center;
      border-radius: .6rem;
      background: #edf2ff;
      color: #4166b4;
      font-size: 1rem;
    }

    .admin-profile__card-title {
      margin: 0;
      color: var(--ap-ink);
      font-size: .98rem;
      font-weight: 700;
    }

    .admin-profile__card-body {
      flex: 1 1 auto;
      padding: 1.25rem;
    }

    .admin-profile__card-foot {
      display: flex;
      justify-content: flex-end;
      gap: .5rem;
      padding: 1rem 1.25rem;
      border-top: 1px solid var(--ap-line);
      background: #fafbfd;
    }

    /* ---------- Fields ---------- */
    .admin-profile__field {
      margin-bottom: 1rem;
    }

    .admin-profile__field:last-child {
      margin-bottom: 0;
    }

    .admin-profile__label {
      display: block;
      margin-bottom: .4rem;
      color: #46566e;
      font-size: .82rem;
      font-weight: 600;
    }

    .admin-profile__input,
    .admin-profile__select {
      width: 100%;
      min-height: 2.75rem;
      border: 1px solid #e0e6ef;
      border-radius: .65rem;
      background: #fff;
      color: var(--ap-ink);
      padding: .55rem .8rem;
      box-shadow: none;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    .admin-profile__input:focus,
    .admin-profile__select:focus {
      border-color: #85a8f3;
      box-shadow: 0 0 0 .2rem rgba(53, 103, 214, .1);
      outline: 0;
    }

    .admin-profile__input:disabled {
      background: #f3f5f9;
      color: #93a2b8;
      cursor: not-allowed;
    }

    /* ---------- Buttons ---------- */
    .admin-profile__btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .4rem;
      min-height: 2.6rem;
      padding-inline: 1.1rem;
      border: 0;
      border-radius: .65rem;
      font-size: .875rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }

    .admin-profile__btn--primary {
      background: var(--ap-accent);
      color: #fff;
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
    }

    .admin-profile__btn--primary:hover {
      background: #2855bd;
      color: #fff;
    }

    .admin-profile__btn--security {
      background: #477be7;
      color: #fff;
      box-shadow: 0 4px 10px rgba(53, 103, 214, .18);
    }

    .admin-profile__btn--security:hover {
      background: #2855bd;
      color: #fff;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
      .admin-profile__grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 575.98px) {
      .admin-profile__hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 1.1rem;
      }
    }

    /* ---------- Dark mode ---------- */
    [data-bs-theme="dark"] .admin-profile {
      --ap-ink: #e4ebf6;
      --ap-muted: #93a2b8;
      --ap-line: #29374d;
    }

    [data-bs-theme="dark"] .admin-profile__hero {
      border-color: #29374d;
      background: linear-gradient(115deg, #1a2639, #182233 58%, #1b2940);
    }

    [data-bs-theme="dark"] .admin-profile__name,
    [data-bs-theme="dark"] .admin-profile__card-title {
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-profile__card,
    [data-bs-theme="dark"] .admin-profile__role {
      border-color: #29374d;
      background: #182233;
    }

    [data-bs-theme="dark"] .admin-profile__role {
      color: #a9c1ff;
    }

    [data-bs-theme="dark"] .admin-profile__card-icon {
      background: #24334d;
      color: #b6caff;
    }

    [data-bs-theme="dark"] .admin-profile__card-foot {
      border-color: #29374d;
      background: #141d2c;
    }

    [data-bs-theme="dark"] .admin-profile__label {
      color: #b9c6d8;
    }

    [data-bs-theme="dark"] .admin-profile__input,
    [data-bs-theme="dark"] .admin-profile__select {
      border-color: #334258;
      background: #111a29;
      color: #e4ebf6;
    }

    [data-bs-theme="dark"] .admin-profile__input:disabled {
      background: #16202f;
      color: #8794a7;
    }
  </style>
@endpush

@section('content')
  @php
    $userName = $user->name;
    $initials = $userName
      ? collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('')
      : '?';
    $roles = $user->roles ?? collect();
  @endphp

  <div class="admin-profile">
    {{-- ================= Hero ================= --}}
    <section class="admin-profile__hero">
      <span class="admin-profile__avatar admin-profile__avatar--photo {{ $user->avatar_path ? '' : 'admin-profile__avatar--initials' }}" aria-hidden="true">
        @if ($user->avatar_path)
          <img src="{{ $user->avatar_url }}" alt="">
        @else
          {{ $initials !== '' ? $initials : '?' }}
        @endif
      </span>
      <div class="admin-profile__identity">
        <h1 class="admin-profile__name">{{ $user->name }}</h1>
        <p class="admin-profile__email">{{ $user->email }}</p>
        <div class="admin-profile__roles" aria-label="{{ __('backend.menu.roles') }}">
          @forelse ($roles as $role)
            <span class="admin-profile__role">{{ $role->name }}</span>
          @empty
            <span class="admin-profile__role admin-profile__role--empty">—</span>
          @endforelse
        </div>
      </div>
    </section>

    <div class="admin-profile__grid">
      {{-- ================= Profile photo ================= --}}
      <section class="admin-profile__card">
        <div class="admin-profile__card-head">
          <span class="admin-profile__card-icon" aria-hidden="true"><i class="bi bi-camera"></i></span>
          <h2 class="admin-profile__card-title">{{ __('backend.profile.avatar') }}</h2>
        </div>
        <form action="{{ route('admin.profile.avatar') }}" method="post" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="admin-profile__card-body">
            <div class="admin-profile__avatar-row">
              <span class="admin-profile__avatar admin-profile__avatar--lg admin-profile__avatar--photo {{ $user->avatar_path ? '' : 'admin-profile__avatar--initials' }}" aria-hidden="true">
                @if ($user->avatar_path)
                  <img src="{{ $user->avatar_url }}" alt="">
                @else
                  {{ $initials !== '' ? $initials : '?' }}
                @endif
              </span>
              <div class="admin-profile__avatar-field">
                <input type="file" name="avatar" id="avatar" class="admin-profile__input admin-profile__input--file" accept="image/jpeg,image/png,image/webp" aria-label="{{ __('backend.profile.avatar') }}">
                <div class="admin-profile__hint">{{ __('backend.profile.avatar_help') }}</div>
                @error('avatar')
                  <div class="admin-profile__error">{{ $message }}</div>
                @enderror
              </div>
            </div>
            @if ($user->avatar_path)
              <div class="admin-profile__check">
                <input class="admin-profile__checkbox" type="checkbox" name="remove_avatar" value="1" id="remove_avatar">
                <label for="remove_avatar">{{ __('backend.profile.remove_avatar') }}</label>
              </div>
            @endif
          </div>
          <div class="admin-profile__card-foot">
            <button type="submit" class="admin-profile__btn admin-profile__btn--primary">
              <i class="bi bi-upload" aria-hidden="true"></i>
              {{ __('backend.profile.update_avatar') }}
            </button>
          </div>
        </form>
      </section>

      {{-- ================= Account details ================= --}}
      <section class="admin-profile__card">
        <div class="admin-profile__card-head">
          <span class="admin-profile__card-icon" aria-hidden="true"><i class="bi bi-person-gear"></i></span>
          <h2 class="admin-profile__card-title">{{ __('backend.profile.details') }}</h2>
        </div>
        <form action="{{ route('admin.profile.update') }}" method="post">
          @csrf
          @method('PUT')
          <div class="admin-profile__card-body">
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="name">{{ __('backend.name') }}</label>
              <input type="text" name="name" id="name" class="admin-profile__input" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="email">{{ __('backend.email') }}</label>
              <input type="email" name="email" id="email" class="admin-profile__input" value="{{ old('email', $user->email) }}" disabled required>
            </div>
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="phone">{{ __('backend.phone') }}</label>
              <input type="text" name="phone" id="phone" class="admin-profile__input" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="locale">{{ __('backend.locale') }}</label>
              <select name="locale" id="locale" class="admin-profile__select">
                @include('backend.partials.locale-options', ['selected' => old('locale', $user->locale)])
              </select>
            </div>
          </div>
          <div class="admin-profile__card-foot">
            <button type="submit" class="admin-profile__btn admin-profile__btn--primary">
              <i class="bi bi-check-lg" aria-hidden="true"></i>
              {{ __('backend.save') }}
            </button>
          </div>
        </form>
      </section>

      {{-- ================= Change password ================= --}}
      <section class="admin-profile__card">
        <div class="admin-profile__card-head">
          <span class="admin-profile__card-icon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
          <h2 class="admin-profile__card-title">{{ __('backend.profile.change_password') }}</h2>
        </div>
        <form action="{{ route('admin.profile.password') }}" method="post">
          @csrf
          @method('PUT')
          <div class="admin-profile__card-body">
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="current_password">{{ __('backend.profile.current_password') }}</label>
              <input type="password" name="current_password" id="current_password" class="admin-profile__input" required autocomplete="current-password">
            </div>
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="password">{{ __('backend.profile.new_password') }}</label>
              <input type="password" name="password" id="password" class="admin-profile__input" required autocomplete="new-password">
            </div>
            <div class="admin-profile__field">
              <label class="admin-profile__label" for="password_confirmation">{{ __('backend.confirm_password') }}</label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="admin-profile__input" required autocomplete="new-password">
            </div>
          </div>
          <div class="admin-profile__card-foot">
            <button type="submit" class="admin-profile__btn admin-profile__btn--security">
              <i class="bi bi-key" aria-hidden="true"></i>
              {{ __('backend.profile.update_password') }}
            </button>
          </div>
        </form>
      </section>
    </div>
  </div>
@endsection
