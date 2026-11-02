@component('mail::message')
# {{ __('auth_api.password_reset_mail_heading') }}

{{ __('auth_api.password_reset_mail_intro', ['name' => $userName]) }}

@component('mail::button', ['url' => $url])
{{ __('frontend.auth.reset_button') }}
@endcomponent

{{ __('auth_api.password_reset_mail_expiry', ['minutes' => $minutes]) }}

{{ __('auth_api.password_reset_mail_ignore') }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
