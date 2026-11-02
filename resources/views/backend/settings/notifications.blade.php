@extends('backend.layouts.app')

@section('title', __('backend.settings.notifications.title'))
@section('page_title', __('backend.settings.notifications.title'))
@section('breadcrumb')
  <li class="breadcrumb-item active">{{ __('backend.settings.notifications.title') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.settings.notifications.update') }}" method="post">
      @csrf
      @method('PUT')
      <div class="card-body">
        {{-- Old: <p class="text-muted">{{ __('backend.settings.notifications.help') }}</p> --}}
        <p class="text-muted">{{ __('backend.settings.notifications.help_override') }}</p>
        <p class="text-muted small">{{ __('backend.settings.notifications.locked_help') }}</p>
        <p class="text-muted small">{{ __('backend.settings.notifications.visible_help') }}</p>

        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th>{{ __('backend.settings.notifications.type') }}</th>
                <th>{{ __('backend.settings.notifications.in_app') }}</th>
                <th>{{ __('backend.settings.notifications.email') }}</th>
                <th>{{ __('backend.settings.notifications.apply_globally') }}</th>
                <th>{{ __('backend.settings.notifications.show_to_users') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($groups as $row)
                @php
                  $inApp = $row['group'].'_in_app';
                  $email = $row['group'].'_email';
                  $apply = $row['group'].'_apply';
                  $visible = $row['group'].'_visible';
                @endphp
                <tr>
                  <td>{{ $row['label'] }}</td>
                  <td>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $inApp }}"
                        id="{{ $inApp }}"
                        value="1"
                        @checked(! empty($channels[$inApp]))
                      >
                      <label class="form-check-label" for="{{ $inApp }}">{{ __('backend.yes') }}</label>
                    </div>
                  </td>
                  <td>
                    @if ($row['email_locked'])
                      <input type="hidden" name="{{ $email }}" value="1">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked disabled>
                        <label class="form-check-label">{{ __('backend.settings.notifications.always_on') }}</label>
                      </div>
                    @else
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          name="{{ $email }}"
                          id="{{ $email }}"
                          value="1"
                          @checked(! empty($channels[$email]))
                        >
                        <label class="form-check-label" for="{{ $email }}">{{ __('backend.yes') }}</label>
                      </div>
                    @endif
                  </td>
                  <td>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $apply }}"
                        id="{{ $apply }}"
                        value="1"
                        @checked(! empty($channels[$apply]))
                      >
                      <label class="form-check-label" for="{{ $apply }}">{{ __('backend.yes') }}</label>
                    </div>
                  </td>
                  <td>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="{{ $visible }}"
                        id="{{ $visible }}"
                        value="1"
                        @checked(! empty($channels[$visible]))
                      >
                      <label class="form-check-label" for="{{ $visible }}">{{ __('backend.yes') }}</label>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
      </div>
    </form>
  </div>
@endsection
