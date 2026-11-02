@extends('backend.layouts.app')
@section('title', __('backend.cms.team.title'))
@section('page_title', __('backend.cms.team.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.cms_team') }}</li>
@endsection
@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.cms.team.title') }}</h3>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('cms.team.create')
          <a href="{{ route('admin.cms.team.create') }}" class="btn btn-primary btn-sm">{{ __('backend.cms.team.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.cms.team.photo') }}</th>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.cms.team.role') }}</th>
            <th>{{ __('backend.cms.sort_order') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($members as $member)
            <tr>
              <td>
                @if ($member->photo_url)
                  <img src="{{ $member->photo_url }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:6px;">
                @else
                  —
                @endif
              </td>
              <td>{{ $member->name }}</td>
              <td>{{ $member->role }}</td>
              <td>{{ $member->sort_order }}</td>
              <td>
                @if ($member->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('cms.team.edit')
                  <a href="{{ route('admin.cms.team.edit', $member) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('cms.team.delete')
                  <form action="{{ route('admin.cms.team.destroy', $member) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($members->hasPages())
      <div class="card-footer">{{ $members->links() }}</div>
    @endif
  </div>
@endsection
