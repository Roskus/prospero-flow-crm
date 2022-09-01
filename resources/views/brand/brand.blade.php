@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Brand') }}</h1>
</header>

<form method="post" action="/brand/save">
    <div class="form-group">
        <label class="label-control">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" value="{{ $brand->name }}" class="form-control" required="required">
    </div>
    <div class="form-group  mt-2">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $brand->id }}">
    {{ csrf_field() }}
</form>

@endsection
