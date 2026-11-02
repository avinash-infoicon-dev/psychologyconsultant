@extends('errors.layout')

@section('title', __('errors.403.title'))
@section('icon', '🚫')
@section('heading', __('errors.403.heading'))
@section('status', __('errors.403.status'))

@section('body')
    @php
        $exceptionMessage = isset($exception) ? trim((string) $exception->getMessage()) : '';
        $genericMessages = ['', 'This action is unauthorized.', 'Unauthorized.', 'Forbidden'];
        $body = ($exceptionMessage !== '' && ! in_array($exceptionMessage, $genericMessages, true))
            ? $exceptionMessage
            : __('errors.403.body');
    @endphp
    <p>{{ $body }}</p>
@endsection
