@extends('layouts.app')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+EAN13+Text&display=swap" rel="stylesheet">
<!--
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128+Text&display=swap" rel="stylesheet">
-->

<header>
   <h1>{{ __('Product') }}</h1>
</header>

<form class="form" method="post" action="/product/save" enctype="multipart/form-data">
    <div class="row form-group">
        <div class="col">
            <label class="label-control mb-2">
                {{ __('Category') }} <span class="text-danger">*</span>
                <a href="/category" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
            </label>
            <select name="category_id" id="category_id" required class="form-control">
                <option value=""></option>
                @if(!empty($categories))
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" @if($product->category_id == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col">
            <label class="label-control mb-2">
                {{ __('Brand') }}
                <a href="/brand" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
            </label>
            <select name="brand_id" id="brand_id" required class="form-control">
                <option value=""></option>
                @if(!empty($brands))
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" @if($product->brand_id == $brand->id) selected="selected" @endif>{{ $brand->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control" required="required">
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Cost') }} <span class="text-danger">*</span></label>
            <input type="number" name="cost" id="cost" value="{{ $product->cost }}" class="form-control" required="required" step="0.1" min="0">
        </div>
        <div class="col">
            <label class="label-control">{{ __('Price') }} <span class="text-danger">*</span></label>
            <input type="number" name="price" id="price" value="{{ $product->price }}" class="form-control" required="required" step="0.1" min="0">
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Quantity') }}</label>
            <input type="number" name="quantity" id="quantity" value="{{ $product->quantity }}" class="form-control" step="1" min="0">
        </div>
        <div class="col">
            <label>{{ __('Minimun stock') }}</label>
            <input type="number" name="min_stock" value="0" class="form-control">
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="control">{{ __('Barcode') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="las la-barcode"></i></span>
                <input type="text" name="barcode" id="barcode" placeholder="EAN-13" value="{{ $product->barcode }}" onkeydown="document.getElementById('barcode-preview').innerHTML = this.value" class="form-control">
            </div>
            <div id="barcode-preview" class="barcode">{{ $product->barcode }}</div>
        </div>
        <div class="col">
            <label class="control">{{ __('SKU') }}</label>
            <input type="text" name="sku" id="sku" value="{{ $product->sku }}" class="form-control" placeholder="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Description') }}</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Photo') }}</label>
            <input type="file" name="photo" class="form-control">
            @if($product->photo)
            <picture class="mt-2 mb-2">
                <img src="{{ asset("/asset/upload/product/$product->id/$product->photo")}}" alt="" class="img-fluid img-thumbnail">
            </picture>
            @endif
        </div>
    </div>
    <div class="form-group mt-2">
        <a href="/product" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $product->id }}">
    {{ csrf_field() }}
</form>

@endsection
