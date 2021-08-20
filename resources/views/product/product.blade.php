@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Product') }}</h1>
</header>

<form class="form" method="post" action="/product/save">
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Category') }} <span class="text-danger">*</span></label>
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
            <label class="label-control">{{ __('Brand') }}</label>
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
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="control">{{ __('Barcode') }}</label>
            <input type="text" name="barcode" id="barcode" class="form-control" placeholder="EAN-128">
        </div>
        <div class="col">
            <label class="control">{{ __('SKU') }}</label>
            <input type="text" name="sku" id="sku" class="form-control" placeholder="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col">
            <label class="label-control">{{ __('Description') }}</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>
    </div>
    <div class="form-group mt-2">
        <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
    </div>
    <input type="hidden" name="id" id="id" value="{{ $product->id }}">
    {{ csrf_field() }}
</form>

@endsection
