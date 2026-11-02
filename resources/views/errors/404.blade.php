@extends('errors.layout')

@section('title', __('errors.404.title'))
@section('icon', '🔍')
@section('heading', __('errors.404.heading'))
@section('status', __('errors.404.status'))

@section('body')
    <p>{{ __('errors.404.body') }}</p>
@endsection
