@extends('layouts.app')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+EAN13+Text&display=swap" rel="stylesheet">
<!--
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128+Text&display=swap" rel="stylesheet">
-->

@include('layouts.partials._header', ['title' =>  __('Product')])

<div class="card">
    <div class="card-body">
        <form class="form" method="post" action="/product/save" enctype="multipart/form-data">
            <div class="row form-group">
                <div class="col">
                    <label for="category_id" class="label-control mb-2">
                        {{ __('Category') }} <span class="text-danger">*</span>
                        <a href="/category" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
                    </label>
                    <select name="category_id" id="category_id" required class="form-select">
                        <option value=""></option>
                        @if(!empty($categories))
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if($product->category_id == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col">
                    <label for="brand_id" class="label-control mb-2">
                        {{ __('Brand') }}
                        <a href="/brand" class="btn btn-outline-primary btn-sm"><i class="las la-plus-square"></i> {{ __('Add') }}</a>
                    </label>
                    <select name="brand_id" id="brand_id" required class="form-select">
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
                <label for="name" class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" required="required">
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="cost" class="label-control">{{ __('Cost') }} <span class="text-danger">*</span></label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" class="form-control" required="required" step="0.1" min="0">
                </div>
                <div class="col">
                    <label for="price" class="label-control">{{ __('Price') }} <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-control" required="required" step="0.1" min="0">
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="quantity" class="label-control">{{ __('Quantity') }}</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" step="1" min="0" class="form-control">
                </div>
                <div class="col">
                    <label for="min_stock_quantity">{{ __('Minimum stock quantity') }}</label>
                    <input type="number" name="min_stock_quantity" id="min_stock_quantity" value="{{ $product->min_stock_quantity }}" min="0" step="1" class="form-control">
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="barcode" class="control">{{ __('Barcode') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-barcode"></i></span>
                        <input type="text" name="barcode" id="barcode" placeholder="EAN-13" value="{{ old('barcode', $product->barcode) }}" onkeydown="document.getElementById('barcode-preview').innerHTML = this.value" onkeyup="document.getElementById('barcode-preview').innerHTML = this.value" class="form-control">
                    </div>
                    <div id="barcode-preview" class="barcode">{{ old('barcode', $product->barcode) }}</div>
                </div>
                <div class="col">
                    <label class="control">{{ __('SKU') }}</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="form-control" placeholder="">
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="elaboration_date">{{ __('Elaboration date') }}</label>
                    <input type="date" name="elaboration_date" id="elaboration_date" value="{{ ($product->elaboration_date) ? $product->elaboration_date : '' }}" class="form-control">
                </div>
                <div class="col">
                    <label for="expiration_date">{{ __('Expiration date') }}</label>
                    <input type="date" name="expiration_date" id="expiration_date" value="{{ $product->expiration_date }}" class="form-control">
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="description" class="label-control">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label class="label-control">{{ __('Photo') }}</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <div class="col pt-4">
                    @if($product->photo)
                        <picture class="">
                            <img src="{{ asset("/asset/upload/product/$product->id/$product->photo")}}" alt="" width="250" class="img-fluid img-thumbnail">
                        </picture>
                    @endif
                </div>
            </div>
            <div class="form-group mt-2">
                <a href="/product" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $product->id }}">
            {{ csrf_field() }}
        </form>
    </div>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
<!-- spanish translation -->
<script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/translations/es.js"></script>
<script>
    ClassicEditor.create(document.getElementById("description"), {

    });
</script>
@endsection
