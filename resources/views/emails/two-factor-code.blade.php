@component('mail::message')
# Your verification code

Use the code below to finish signing in. It expires in **{{ $expiryMinutes }} minutes**.

@component('mail::panel')
<div style="font-size: 28px; letter-spacing: 6px; text-align: center; font-weight: bold;">
{{ $code }}
</div>
@endcomponent

If you didn't request this code, you can safely ignore this email — someone may have mistyped your address, and no one can access your account without it.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
