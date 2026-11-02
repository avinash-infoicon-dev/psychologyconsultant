@extends('backend.layouts.app')

@section('title', __('backend.plans.title'))
@section('page_title', __('backend.plans.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.menu.plans') }}</li>
@endsection

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h3 class="card-title mb-0">{{ __('backend.plans.title') }}</h3>
      <div class="d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
          <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="{{ __('backend.search') }}">
          <select name="status" class="form-select form-select-sm">
            <option value="">{{ __('backend.status') }}</option>
            <option value="draft" @selected(request('status') === 'draft' || request('status') === 'inactive')>{{ __('backend.plans.statuses.draft') }}</option>
            <option value="active" @selected(request('status') === 'active')>{{ __('backend.plans.statuses.active') }}</option>
            <option value="archived" @selected(request('status') === 'archived')>{{ __('backend.plans.statuses.archived') }}</option>
            {{-- Legacy:
            <option value="inactive" @selected(request('status') === 'inactive')>{{ __('backend.inactive') }}</option>
            --}}
          </select>
          <button class="btn btn-sm btn-outline-secondary" type="submit">{{ __('backend.search') }}</button>
        </form>
        @permission('plans.create')
          <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">{{ __('backend.plans.create') }}</a>
        @endpermission
      </div>
    </div>
    <div class="card-body table-responsive p-0">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>{{ __('backend.name') }}</th>
            <th>{{ __('backend.plans.plan_type') }}</th>
            <th>{{ __('backend.plans.price') }}</th>
            <th>{{ __('backend.plans.motto') }}</th>
            <th>{{ __('backend.plans.billing_interval') }}</th>
            <th>{{ __('backend.plans.exchange_quota') }}</th>
            <th>{{ __('backend.plans.max_text_chars') }}</th>
            <th>{{ __('backend.plans.max_audio_seconds') }}</th>
            <th>{{ __('backend.plans.response_time') }}</th>
            <th>{{ __('backend.plans.features') }}</th>
            <th>{{ __('backend.plans.channels') }}</th>
            <th>{{ __('backend.plans.subscriptions_count') }}</th>
            <th>{{ __('backend.status') }}</th>
            <th>{{ __('backend.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($plans as $plan)
            <tr>
              <td>
                <div>
                  {{ $plan->name }}
                  @if ($plan->is_recommended)
                    <span class="badge text-bg-primary">{{ __('backend.plans.recommended') }}</span>
                  @endif
                </div>
                <code class="small">{{ $plan->slug }}</code>
              </td>
              <td>{{ __('backend.plans.types.'.$plan->plan_type) }}</td>
              <td>{{ $plan->displayPrice() }} {{ $plan->currency }}</td>
              <td>{{ $plan->motto ?: '—' }}</td>
              <td>{{ __('backend.plans.intervals.'.$plan->billing_interval) }}</td>
              <td>{{ number_format($plan->exchange_quota) }}</td>
              <td>
                @if ($plan->max_text_chars === null)
                  —
                @elseif ((int) $plan->max_text_chars === 0)
                  {{ __('backend.plans.unlimited') }}
                @else
                  {{ number_format($plan->max_text_chars) }}
                @endif
              </td>
              <td>
                @if ($plan->max_audio_seconds === null)
                  —
                @elseif ((int) $plan->max_audio_seconds === 0)
                  {{ __('backend.plans.unlimited') }}
                @else
                  {{ number_format($plan->max_audio_seconds) }}
                @endif
              </td>
              <td>{{ $plan->response_time_label ?: ($plan->response_time_hours ? '≤ '.$plan->response_time_hours.'h' : '—') }}</td>
              <td>
                @if (is_array($plan->features) && count($plan->features))
                  <span class="small">{{ \Illuminate\Support\Str::limit(implode(' · ', $plan->features), 48) }}</span>
                @else
                  —
                @endif
              </td>
              <td>
                @forelse (($plan->channels ?? []) as $channel)
                  <span class="badge text-bg-light border">{{ __('backend.plans.channel_options.'.$channel) }}</span>
                @empty
                  —
                @endforelse
              </td>
              <td>{{ $plan->subscriptions_count }}</td>
              <td>
                @php($planStatus = $plan->status ?: ($plan->is_active ? 'active' : 'draft'))
                @if ($planStatus === 'active')
                  <span class="badge text-bg-success">{{ __('backend.plans.statuses.active') }}</span>
                @elseif ($planStatus === 'archived')
                  <span class="badge text-bg-secondary">{{ __('backend.plans.statuses.archived') }}</span>
                @else
                  <span class="badge text-bg-warning">{{ __('backend.plans.statuses.draft') }}</span>
                @endif
                {{-- Legacy:
                @if ($plan->is_active)
                  <span class="badge text-bg-success">{{ __('backend.active') }}</span>
                @else
                  <span class="badge text-bg-danger">{{ __('backend.inactive') }}</span>
                @endif
                --}}
              </td>
              <td class="text-nowrap">
                @permission('plans.edit')
                  <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">{{ __('backend.edit') }}</a>
                @endpermission
                @permission('plans.delete')
                  @unless ($planStatus === 'archived')
                    <form action="{{ route('admin.plans.archive', $plan) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.plans.confirm_archive')))">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('backend.plans.archive') }}</button>
                    </form>
                  @endunless
                  {{-- Keep delete for history, but do not use it:
                  <form action="{{ route('admin.plans.destroy', $plan) }}" method="post" class="d-inline" onsubmit="return confirm(@json(__('backend.confirm_delete')))">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('backend.delete') }}</button>
                  </form>
                  --}}
                @endpermission
              </td>
            </tr>
          @empty
            <tr><td colspan="14" class="text-center py-4">{{ request()->filled('q') ? __('backend.empty_filtered') : __('backend.no_records') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($plans->hasPages())
      <div class="card-footer">{{ $plans->links() }}</div>
    @endif
  </div>
@endsection
