@extends('frontend.layouts.dashboard')

@section('title', __('frontend.subscription.exchanges_history'))
@section('page_title', __('frontend.subscription.exchanges_history'))

@section('content')
  <div class="card radius-16 border-0">
    <div class="card-header border-bottom bg-base py-16 px-24">
      <h3 class="h6 mb-0">{{ __('frontend.subscription.exchanges_history') }}</h3>
    </div>
    <div class="card-body p-24">
      <div class="table-responsive scroll-sm">
        <table class="table bordered-table sm-table mb-0">
          <thead>
            <tr>
              <th>{{ __('frontend.subscription.exchange_id') }}</th>
              <th>{{ __('frontend.subscription.date') }}</th>
              <th>{{ __('frontend.subscription.psychologist') }}</th>
              <th>{{ __('frontend.subscription.messages_used') }}</th>
              <th>{{ __('frontend.subscription.status') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($exchangesHistory as $excItem)
              <tr>
                <td><strong class="text-primary-light">#EXC-{{ $excItem->id }}</strong></td>
                <td>{{ $excItem->created_at?->format('d M Y, H:i') ?? '—' }}</td>
                <td>{{ $excItem->psychologist?->user?->name ?? '—' }}</td>
                <td>{{ (int) $excItem->patient_messages_used + (int) $excItem->psychologist_messages_used }}</td>
                <td>
                  @if ($excItem->isCompleted())
                    <span class="bg-success-focus text-success-600 border border-success-main px-12 py-4 radius-4 fw-medium text-sm">{{ __('frontend.subscription.completed') }}</span>
                  @elseif ($excItem->isPending())
                    <span class="bg-warning-focus text-warning-600 border border-warning-main px-12 py-4 radius-4 fw-medium text-sm">{{ __('frontend.subscription.pending') }}</span>
                  @else
                    <span class="bg-neutral-100 text-neutral-600 border border-neutral-300 px-12 py-4 radius-4 fw-medium text-sm">{{ $excItem->status }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-secondary-light py-20">{{ __('frontend.subscription.no_exchanges_history') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($exchangesHistory->total() > 0)
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-24">
          <p class="mb-0 text-secondary-light">
            Showing {{ $exchangesHistory->firstItem() ?: 0 }} to {{ $exchangesHistory->lastItem() ?: 0 }} of {{ $exchangesHistory->total() }} entries
          </p>
          {{ $exchangesHistory->links() }}
        </div>
      @endif
    </div>
  </div>
@endsection
