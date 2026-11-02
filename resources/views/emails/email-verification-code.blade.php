@component('mail::message')
# {{ __('auth_api.email_verify_mail_heading') }}

{{ __('auth_api.email_verify_mail_intro', ['name' => $userName]) }}

@component('mail::panel')
<div style="font-size: 28px; letter-spacing: 6px; text-align: center; font-weight: bold;">
{{ $code }}
</div>
@endcomponent

{{ __('auth_api.email_verify_mail_expiry', ['minutes' => $expiryMinutes]) }}

{{ __('auth_api.email_verify_mail_ignore') }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
