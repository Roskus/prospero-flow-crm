@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Product') }}</h1>
</header>

<form class="form" method="post" action="/product/save">
    <div class="form-group">
        <label class="label-control">{{ trans('hammer.Name') }}</label>
        <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control" required="required">
    </div>
    <div class="form-group">
        <label class="label-control">{{ trans('hammer.Quantity') }}</label>
        <input type="number" name="quantity" id="quantity" value="{{ $product->quantity }}" class="form-control" required="required" step="1" min="0">
    </div>
    <div class="form-group">
        <label class="label-control">{{ trans('hammer.Price') }}</label>
        <input type="number" name="price" id="price" value="{{ $product->price }}" class="form-control" required="required" step="0.1" min="0">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ trans('hammer.Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $product->id }}">
    {{ csrf_field() }}
</form>

@endsection