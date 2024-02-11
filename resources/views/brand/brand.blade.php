@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Brand') }}</h1>
</header>

<div class="card">
    <div class="card-body">
    <form method="post" action="/brand/save">
        @csrf
        <div class="form-group">
            <label for="name" class="label-control">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" value="{{ $brand->name }}" class="form-control" required="required">
        </div>
        <div class="form-group  mt-2">
            <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
        </div>
        <input type="hidden" name="id" id="id" value="{{ $brand->id }}">
    </form>
    </div><!--./card-body-->
</div><!--./card-->
@endsection
