@component('mail::message')
# {{ __('frontend.contact_inquiry.mail_heading') }}

**{{ __('frontend.home.form_name') }}:** {{ $inquiry->name }}

**{{ __('frontend.home.form_email') }}:** {{ $inquiry->email }}

@if ($inquiry->phone)
**{{ __('frontend.home.form_phone') }}:** {{ $inquiry->phone }}
@endif

@if ($inquiry->consultation_type)
**{{ __('frontend.blog.consultation_type') }}:** {{ $inquiry->consultation_type }}
@endif

**{{ __('frontend.home.form_message') }}:**

{{ $inquiry->message }}

@if ($inquiry->page_url)
{{ __('frontend.contact_inquiry.page_url') }}: {{ $inquiry->page_url }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
