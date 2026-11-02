@extends('errors.layout')

@section('title', __('errors.401.title'))
@section('icon', '🔒')
@section('heading', __('errors.401.heading'))
@section('status', __('errors.401.status'))

@section('body')
    @php
        $exceptionMessage = isset($exception) ? trim((string) $exception->getMessage()) : '';
        $genericMessages = ['', 'Unauthenticated.', 'Unauthorized.'];
        $body = ($exceptionMessage !== '' && ! in_array($exceptionMessage, $genericMessages, true))
            ? $exceptionMessage
            : __('errors.401.body');
    @endphp
    <p>{{ $body }}</p>
@endsection
