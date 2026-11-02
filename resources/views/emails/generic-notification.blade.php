@component('mail::message')
# {{ $title }}

{{ $body }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
