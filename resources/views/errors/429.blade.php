@extends('errors.layout')

@section('title', __('errors.429.title'))
@section('icon', '⏳')
@section('heading', __('errors.429.heading'))
@section('status', __('errors.429.status'))

@section('body')
    <p>{{ __('errors.429.body') }}</p>
@endsection
