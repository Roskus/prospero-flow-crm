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
                    @include('components.category', ['category_id' => old('category_id', $product->category_id), 'required' => true])
                </div>
                <div class="col">
                    @include('components.brand', ['brand_id' => old('brand_id', $product->brand_id), 'required' => true])
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" required="required">
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="cost" class="label-control">{{ __('Cost') }} <span class="text-danger">*</span></label>
                    <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" class="form-control" required="required" step="0.01" min="0">
                </div>
                <div class="col">
                    <label for="price" class="label-control">{{ __('Price') }} <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="form-control" required="required" step="0.01" min="0">
                </div>
                <div class="col">
                    @include('components.currency', ['currency' => old('currency', $product->exists ? $product->currency : Auth::user()->company->currency), 'required' => true])
                </div>
                <div class="col">
                    <label for="tax" class="label-control">{{ __('Tax') }}</label>
                    <div class="input-group">
                        <input type="number" name="tax" id="tax" value="{{ old('tax', $product->tax) }}" class="form-control" step="0.1" min="0" max="80">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="quantity" class="label-control">{{ __('Quantity') }}</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" step="1" min="0" class="form-control">
                </div>
                <div class="col">
                    <label for="min_stock_quantity">{{ __('Minimum stock quantity') }}</label>
                    <input type="number" name="min_stock_quantity" id="min_stock_quantity" value="{{ old('min_stock_quantity', $product->min_stock_quantity ?? 0) }}" min="0" step="1" class="form-control">
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="weight" class="label-control">{{ __('Weight') }}</label>
                    <div class="input-group">
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" step="0.001" min="0" class="form-control">
                        <select name="weight_unit" id="weight_unit" class="form-select" aria-label="{{ __('Weight unit') }}" style="max-width: 6rem;">
                            @foreach(\App\Enums\WeightUnit::cases() as $weightUnit)
                                <option value="{{ $weightUnit->value }}" @selected(old('weight_unit', $product->weight_unit ?? \App\Enums\WeightUnit::Gram->value) === $weightUnit->value)>{{ $weightUnit->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label for="dimensions" class="label-control">{{ __('Dimensions') }}</label>
                    <div class="input-group">
                        <input type="text" name="dimensions" id="dimensions" value="{{ old('dimensions', $product->dimensions) }}" maxlength="100" placeholder="{{ __('L x W x H') }}" class="form-control">
                        <select name="dimension_unit" id="dimension_unit" class="form-select" aria-label="{{ __('Dimension unit') }}" style="max-width: 6rem;">
                            @foreach(\App\Enums\DimensionUnit::cases() as $dimensionUnit)
                                <option value="{{ $dimensionUnit->value }}" @selected(old('dimension_unit', $product->dimension_unit ?? \App\Enums\DimensionUnit::Millimeter->value) === $dimensionUnit->value)>{{ $dimensionUnit->value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col">
                    <label for="barcode" class="control">{{ __('Barcode') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-barcode"></i></span>
                        <input type="text" name="barcode" id="barcode" placeholder="EAN-13" value="{{ old('barcode', $product->barcode) }}" onkeydown="document.getElementById('barcode-preview').textContent = this.value" onkeyup="document.getElementById('barcode-preview').textContent = this.value" class="form-control">
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
