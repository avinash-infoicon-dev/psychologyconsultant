@extends('backend.layouts.app')

@section('title', __('backend.specializations.title'))
@section('page_title', __('backend.specializations.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.specializations') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.specializations.title') }}</h3>
      <div class="d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.active') }}</option>
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
            <option value="archived" @selected(request('status') === 'archived')>{{ __('backend.specializations.archived_badge') }}</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('specializations.create')
          <a href="{{ route('admin.specializations.create') }}" class="btn btn-primary btn-sm">{{ __('backend.specializations.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.slug') }}</th>
            <th>{{ __('backend.description') }}</th>
            <th>{{ __('backend.specializations.professionals_count') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($specializations as $specialization)
            <tr>
              <td>{{ $specialization->name }}</td>
              <td><code>{{ $specialization->slug }}</code></td>
              <td>{{ \Illuminate\Support\Str::limit($specialization->description, 80) ?: '—' }}</td>
              <td>{{ $specialization->professionals_count }}</td>
              <td>
                @if ($specialization->isArchived())
                  <span class="badge text-bg-secondary">{{ __('backend.specializations.archived_badge') }}</span>
                @elseif ($specialization->status)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
              </td>
              <td class="text-nowrap">
                @permission('specializations.edit')
                  <a href="{{ route('admin.specializations.edit', $specialization) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                  @unless ($specialization->isArchived())
                    <form action="{{ route('admin.specializations.toggle-status', $specialization) }}" method="post" class="d-inline">
                      @csrf
                      @method('PATCH')
                      @if ($specialization->status)
                        <button type="submit" class="btn btn-sm btn-outline-warning">{{ __('backend.specializations.deactivate') }}</button>
                      @else
                        <button type="submit" class="btn btn-sm btn-outline-success">{{ __('backend.specializations.activate') }}</button>
                      @endif
                    </form>
                  @endunless
                @endpermission
                @permission('specializations.delete')
                  {{--
                  <form action="{{ route('admin.specializations.destroy', $specialization) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                  --}}
                  @unless ($specialization->isArchived())
                    <form action="{{ route('admin.specializations.archive', $specialization) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.specializations.confirm_archive')))">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('backend.specializations.archive') }}</button>
                    </form>
                  @endunless
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4">{{ __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($specializations->hasPages())
      <div class="card-footer">{{ $specializations->links() }}</div>
    @endif
  </div>

  @if (! request()->filled('status'))
    <div class="card mt-3">
      <div class="card-header">
        <h3 class="card-title mb-0">{{ __('backend.specializations.archive_section') }}</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>{{ __('backend.name') }}</th>
              <th>{{ __('backend.slug') }}</th>
              <th>{{ __('backend.specializations.professionals_count') }}</th>
              <th>{{ __('backend.status') }}</th>
              <th>{{ __('backend.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($archivedSpecializations ?? [] as $specialization)
              <tr>
                <td>{{ $specialization->name }}</td>
                <td><code>{{ $specialization->slug }}</code></td>
                <td>{{ $specialization->professionals_count }}</td>
                <td><span class="badge text-bg-secondary">{{ __('backend.specializations.archived_badge') }}</span></td>
                <td>
                  @permission('specializations.edit')
                    <a href="{{ route('admin.specializations.edit', $specialization) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                  @endpermission
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center py-4">{{ __('backend.specializations.archive_empty') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  @endif
@endsection
