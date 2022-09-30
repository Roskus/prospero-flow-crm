@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Category') }}</h1>
</header>

<form method="post" action="/category/save">
    <div class="form-group">
        <label class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ $category->name }}" class="form-control" required="required">
    </div>

    <!-- Validation Errors -->
    <x-validation-errors :errors="$errors" />

    <div class="form-group mt-2">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $category->id }}">
    <input type="hidden" name="company_id" id="company_id" value="">
    {{ csrf_field() }}
</form>

@endsection
