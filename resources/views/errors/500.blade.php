@extends('errors.layout')

@section('title', __('errors.500.title'))
@section('icon', '⚠️')
@section('heading', __('errors.500.heading'))
@section('status', __('errors.500.status'))

@section('body')
    <p>{{ __('errors.500.body') }}</p>
@endsection
