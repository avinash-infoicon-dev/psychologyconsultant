@extends('frontend.layouts.dashboard')

@section('title', __('frontend.reports.create'))
@section('page_title', __('frontend.reports.create'))

@section('content')
  <div class="fe-cms-card">
    <p class="fe-muted">{{ __('frontend.reports.form_hint') }}</p>

    <form method="post" action="{{ route($routePrefix.'.reports.store') }}" class="fe-form" data-report-form>
      @csrf

      <div class="fe-form-group">
        <label for="type">{{ __('frontend.reports.type') }}</label>
        <select name="type" id="type" class="fe-select" required data-report-type>
          <option value="conversation" @selected(old('type', $selectedType) === 'conversation')>
            {{ __('frontend.reports.type_conversation') }}
          </option>
          <option value="user" @selected(old('type', $selectedType) === 'user')>
            {{ __('frontend.reports.type_user') }}
          </option>
        </select>
      </div>

      <div class="fe-form-group" data-report-conversation-wrap>
        <label for="conversation_id">{{ __('frontend.reports.conversation') }}</label>
        @if ($conversations->isEmpty())
          @include('frontend.partials.ui-empty', [
            'title' => __('frontend.reports.need_conversation'),
            'hint' => null,
            'actionUrl' => route($routePrefix.'.conversations.index'),
            'actionLabel' => __('frontend.dashboard.quick_conversations'),
          ])
        @else
          <select name="conversation_id" id="conversation_id" class="fe-select">
            <option value="">{{ __('frontend.reports.choose_conversation') }}</option>
            @foreach ($conversations as $conversation)
              <option value="{{ $conversation->id }}" @selected((int) old('conversation_id', $selectedConversationId) === $conversation->id)>
                {{ $reportsService->conversationLabel($conversation, auth()->user()) }}
              </option>
            @endforeach
          </select>
        @endif
      </div>

      <div class="fe-form-group" data-report-user-wrap hidden>
        <label for="reported_user_id">{{ __('frontend.reports.person') }}</label>
        @if ($peers->isEmpty())
          @include('frontend.partials.ui-empty', [
            'title' => __('frontend.reports.need_conversation'),
            'hint' => null,
            'actionUrl' => route($routePrefix.'.conversations.index'),
            'actionLabel' => __('frontend.dashboard.quick_conversations'),
          ])
        @else
          <select name="reported_user_id" id="reported_user_id" class="fe-select">
            <option value="">{{ __('frontend.reports.choose_person') }}</option>
            @foreach ($peers as $peer)
              <option value="{{ $peer['id'] }}" @selected((int) old('reported_user_id', $selectedUserId) === $peer['id'])>
                {{ $peer['name'] }}
              </option>
            @endforeach
          </select>
        @endif
      </div>

      <div class="fe-form-group">
        <label for="reason">{{ __('frontend.reports.reason') }}</label>
        <select name="reason" id="reason" class="fe-select" required>
          @foreach (\App\Models\Report::REASONS as $reason)
            <option value="{{ $reason }}" @selected(old('reason') === $reason)>
              {{ __('frontend.reports.reason_'.$reason) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="fe-form-group">
        <label for="description">{{ __('frontend.reports.description') }}</label>
        <textarea name="description" id="description" class="fe-input" rows="5" required minlength="10">{{ old('description') }}</textarea>
      </div>

      <div class="fe-form-actions">
        <a href="{{ route($routePrefix.'.reports.index') }}" class="fe-btn fe-btn--outline">{{ __('frontend.cancel') }}</a>
        <button type="submit" class="fe-btn fe-btn--primary">{{ __('frontend.reports.submit') }}</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
      const form = document.querySelector('[data-report-form]');
      if (!form) return;
      const type = form.querySelector('[data-report-type]');
      const conversationWrap = form.querySelector('[data-report-conversation-wrap]');
      const userWrap = form.querySelector('[data-report-user-wrap]');
      const conversation = form.querySelector('#conversation_id');
      const user = form.querySelector('#reported_user_id');

      const sync = () => {
        const isUser = type.value === 'user';
        conversationWrap.hidden = isUser;
        userWrap.hidden = !isUser;
        conversation.required = !isUser;
        user.required = isUser;
      };

      type.addEventListener('change', sync);
      sync();
    })();
  </script>
@endpush
