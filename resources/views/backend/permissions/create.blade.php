@extends('backend.layouts.app')

@section('title', __('backend.permissions.create'))
@section('page_title', __('backend.permissions.create'))
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">{{ __('backend.menu.permissions') }}</a></li>
  <li class="breadcrumb-item active">{{ __('backend.create') }}</li>
@endsection

@section('content')
  <div class="card card-primary card-outline">
    <form action="{{ route('admin.permissions.store') }}" method="post">
      @csrf
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label" for="name">{{ __('backend.name') }}</label>
          <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="slug">{{ __('backend.slug') }}</label>
          <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="users.view">
        </div>
        <div class="mb-3">
          <label class="form-label" for="group">{{ __('backend.group') }}</label>
          <input type="text" name="group" id="group" class="form-control" list="permission-groups" value="{{ old('group') }}">
          <datalist id="permission-groups">
            @foreach ($groups as $group)
              <option value="{{ $group }}"></option>
            @endforeach
          </datalist>
        </div>
        <div class="mb-3">
          <label class="form-label" for="description">{{ __('backend.description') }}</label>
          <textarea name="description" id="description" class="form-control" rows="2">{{ old('description') }}</textarea>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">{{ __('backend.save') }}</button>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">{{ __('backend.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
