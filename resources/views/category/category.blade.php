@extends('layout.app')

@section('content')
<header>
   <h1>{{ __('hammer.Category') }}</h1>
</header>

<form method="post" action="/category/save">
    <div class="form-group">
        <label class="label-control">{{ __('hammer.Name') }}</label>
        <input type="text" name="name" id="name" value="{{ $category->name }}" class="form-control" required="required">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('hammer.Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $category->id }}">
    {{ csrf_field() }}
</form>

@endsection
