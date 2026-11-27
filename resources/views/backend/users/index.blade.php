@extends('backend.layouts.app')

@section('title', __('backend.users.title'))
@section('page_title', __('backend.users.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.users') }}</li>
@endsection

@push('styles')
  @include('backend.partials._admin-modern-styles')
@endpush

@section('content')
  @php
    $hasFilters = request()->filled('q') || request()->filled('status');
  @endphp

  <div class="adm">
    {{-- ================= Hero ================= --}}
    <section class="adm__hero">
      <div class="adm__heading">
        <span class="adm__hero-icon" aria-hidden="true"><i class="bi bi-people"></i></span>
        <div>
          <h1 class="adm__title">{{ __('backend.users.title') }}</h1>
          <p class="adm__subtitle">{{ __('backend.logs.showing', ['first' => number_format($users->firstItem() ?? 0), 'last' => number_format($users->lastItem() ?? 0), 'total' => number_format($users->total())]) }}</p>
        </div>
      </div>
      <div class="adm__actions">
        @permission('users.create')
          <a href="{{ route('admin.users.create') }}" class="adm__btn adm__btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            {{ __('backend.users.create') }}
          </a>
        @endpermission
      </div>
    </section>

    {{-- ================= Filters ================= --}}
    <section class="adm__card adm__filters" aria-labelledby="users-filter-title">
      <div class="adm__filters-head">
        <h2 class="adm__filters-title" id="users-filter-title"><i class="bi bi-funnel" aria-hidden="true"></i>{{ __('backend.search') }}</h2>
        @if ($hasFilters)
          <a class="adm__clear" href="{{ route('admin.users.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i>{{ __('backend.logs.clear_filters') }}</a>
        @endif
      </div>
      <form method="get" class="adm__filter-form" action="{{ route('admin.users.index') }}">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('backend.search') }}" aria-label="{{ __('backend.search') }}">
        <select name="status" class="form-select" aria-label="{{ __('backend.status') }}">
          <option value="">{{ __('backend.status') }}</option>
          <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
          <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
        </select>
        <button class="adm__btn adm__btn--primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i>{{ __('backend.search') }}</button>
      </form>
    </section>

    {{-- ================= Results ================= --}}
    <section class="adm__card adm__results" aria-labelledby="users-results-title">
      <div class="adm__results-head">
        <h2 class="adm__card-title" id="users-results-title"><i class="bi bi-list-check" aria-hidden="true"></i>{{ __('backend.users.title') }}</h2>
      </div>
      <div class="adm__table-wrap">
        <table class="table adm__table">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.email') }}</th>
              <th>{{ __('backend.phone') }}</th>
              <th>{{ __('backend.locale') }}</th>
              <th>{{ __('backend.menu.roles') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.psychologists.verification') }}</th>
              <th>{{ __('backend.users.two_factor') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              <tr>
                <td>
                  <div class="adm__person">
                    <span class="adm__avatar" aria-hidden="true">{{ collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: '?' }}</span>
                    <span class="adm__name">{{ $user->name }}</span>
                  </div>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?: '—' }}</td>
                <td><code>{{ $user->locale ?: '—' }}</code></td>
                <td>
                  @forelse ($user->roles as $role)
                    <span class="adm__chip">{{ $role->name }}</span>
                  @empty
                    <span class="adm__muted">—</span>
                  @endforelse
                </td>
                <td>
                  @if ($user->is_active)
                    <span class="adm__status adm__status--success">{{ __('backend.active') }}</span>
                  @else
                    <span class="adm__status adm__status--danger">{{ __('backend.inactive') }}</span>
                  @endif
                </td>
                <td>
                  @if ($user->psychologist)
                    @php
                      $status = $user->psychologist->verification_status ?: ($user->psychologist->is_verified ? 'verified' : 'draft');
                      $tone = match ($status) {
                        'verified' => 'success',
                        'submitted', 'under_review' => 'warning',
                        'rejected' => 'danger',
                        'suspended' => 'dark',
                        default => 'secondary',
                      };
                    @endphp
                    <span class="adm__status adm__status--{{ $tone }}">{{ __('backend.psychologists.status_'.$status) }}</span>
                  @else
                    <span class="adm__muted">—</span>
                  @endif
                </td>
                <td>
                  @if (! $twoFactorGloballyEnabled)
                    <span class="adm__status adm__status--secondary">{{ __('backend.users.two_factor_global_off') }}</span>
                  @elseif ($twoFactor->isRequiredFor($user))
                    <span class="adm__status adm__status--success">{{ __('backend.users.two_factor_required') }}</span>
                    @php($reason = $twoFactor->userRequirementReason($user))
                    {{--@if ($reason === 'user_enabled')
                      <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_user') }}</span>
                    @elseif ($reason === 'role')
                      <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_role') }}</span>
                    @endif--}}
                  @else
                    <span class="adm__status adm__status--secondary">{{ __('backend.users.two_factor_not_required') }}</span>
                    {{--@if ($twoFactor->userRequirementReason($user) === 'exempt')
                      <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_exempt') }}</span>
                    @endif--}}
                  @endif
                </td>
                <td>
                  <div class="adm__row-actions">
                    @permission('users.edit')
                      <a href="{{ route('admin.users.edit', $user) }}" class="adm__btn adm__btn--ghost">{{ __('backend.edit') }}</a>
                      @if ($twoFactorGloballyEnabled)
                        <form action="{{ route('admin.users.two-factor.toggle', $user) }}" method="post" class="d-inline">
                          @csrf
                          @if ($twoFactor->isRequiredFor($user))
                            <button type="submit" class="adm__btn adm__btn--warn">{{ __('backend.users.two_factor_disable') }}</button>
                          @else
                            <button type="submit" class="adm__btn adm__btn--success">{{ __('backend.users.two_factor_enable') }}</button>
                          @endif
                        </form>
                      @endif
                    @endpermission
                    @permission('psychologists.view')
                      @if ($user->psychologist)
                        <a href="{{ route('admin.psychologists.show', $user->psychologist) }}" class="adm__btn adm__btn--ghost">{{ __('backend.psychologists.view_details') }}</a>
                      @endif
                    @endpermission
                    {{-- Old inline approve/revoke on this list used admin.psychologists.approve and admin.psychologists.revoke. Those routes were removed; use psychologist show. --}}
                    @permission('users.delete')
                      <form action="{{ route('admin.users.destroy', $user) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="adm__btn adm__btn--danger">{{ __('backend.delete') }}</button>
                      </form>
                    @endpermission
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="adm__empty">
                  <span class="adm__empty-icon" aria-hidden="true"><i class="bi bi-people"></i></span>
                  <span class="adm__empty-title">{{ __('backend.no_records') }}</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if ($users->hasPages())
        <div class="adm__pagination">{{ $users->links() }}</div>
      @endif
    </section>
  </div>
@endsection
