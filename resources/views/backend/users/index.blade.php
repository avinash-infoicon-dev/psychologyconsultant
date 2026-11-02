@extends('backend.layouts.app')

@section('title', __('backend.users.title'))
@section('page_title', __('backend.users.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.users') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.users.title') }}</h3>
      <div class="d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('users.create')
          <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">{{ __('backend.users.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
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
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->phone ?: '—' }}</td>
              <td><code>{{ $user->locale ?: '—' }}</code></td>
              <td>
                @forelse ($user->roles as $role)
                  <span class="badge text-bg-secondary">{{ $role->name }}</span>
                @empty
                  —
                @endforelse
              </td>
              <td>
                @if ($user->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td>
                @if ($user->psychologist)
                  @php
                    $status = $user->psychologist->verification_status ?: ($user->psychologist->is_verified ? 'verified' : 'draft');
                    $badge = match ($status) {
                      'verified' => 'text-bg-success',
                      'submitted', 'under_review' => 'text-bg-warning',
                      'rejected' => 'text-bg-danger',
                      'suspended' => 'text-bg-dark',
                      default => 'text-bg-secondary',
                    };
                  @endphp
                  <span class="badge {{ $badge }}">{{ __('backend.psychologists.status_'.$status) }}</span>
                @else
                  —
                @endif
              </td>
              <td>
                @if (! $twoFactorGloballyEnabled)
                  <span class="badge text-bg-secondary">{{ __('backend.users.two_factor_global_off') }}</span>
                @elseif ($twoFactor->isRequiredFor($user))
                  <span class="badge text-bg-success">{{ __('backend.users.two_factor_required') }}</span>
                  @php($reason = $twoFactor->userRequirementReason($user))
                  {{--@if ($reason === 'user_enabled')
                    <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_user') }}</span>
                  @elseif ($reason === 'role')
                    <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_role') }}</span>
                  @endif
                @else
                  <span class="badge text-bg-secondary">{{ __('backend.users.two_factor_not_required') }}</span>
                  @if ($twoFactor->userRequirementReason($user) === 'exempt')
                    <span class="small text-muted d-block">{{ __('backend.users.two_factor_reason_exempt') }}</span>
                  @endif--}}
                @endif
              </td>
              <td class="text-nowrap">
                @permission('users.edit')
                  <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                  @if ($twoFactorGloballyEnabled)
                    <form action="{{ route('admin.users.two-factor.toggle', $user) }}" method="post" class="d-inline">
                      @csrf
                      @if ($twoFactor->isRequiredFor($user))
                        <button type="submit" class="btn btn-sm btn-outline-warning">{{ __('backend.users.two_factor_disable') }}</button>
                      @else
                        <button type="submit" class="btn btn-sm btn-outline-success">{{ __('backend.users.two_factor_enable') }}</button>
                      @endif
                    </form>
                  @endif
                @endpermission
                @permission('psychologists.view')
                  @if ($user->psychologist)
                    <a href="{{ route('admin.psychologists.show', $user->psychologist) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.psychologists.view_details') }}</a>
                  @endif
                @endpermission
                {{-- Old inline approve/revoke on this list used admin.psychologists.approve and admin.psychologists.revoke. Those routes were removed; use psychologist show. --}}
                @permission('users.delete')
                  <form action="{{ route('admin.users.destroy', $user) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($users->hasPages())
      <div class="card-footer">{{ $users->links() }}</div>
    @endif
  </div>
@endsection
