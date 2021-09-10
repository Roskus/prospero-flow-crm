@extends('layouts.app')

@section('content')
<header>
   <h1>{{ trans('Order') }}</h1>
</header>

<form method="post" action="/order/save">
    @csrf
    <div class="row">
        <div class="col">
            <label>{{ __('Customer') }}</label>
            <select name="customer_id" id="customer_id" required="required" class="form-control form-control-lg">
                <option value="">{{ __('Choose') }}</option>
                @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="row">
        <div class="col">
            <label>{{ __('Product') }}</label>
            <select name="product_id" id="product_id" onchange="Order.updatePrice(this)" class="form-control form-control-lg">
                <option value="" data-price="0">{{ __('Choose') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price}}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label>{{ __('Quantity')}}</label>
            <input type="number" name="quantity" id="quantity" required="required" placeholder="{{ __('Quantity') }}" class="form-control form-control-lg">
        </div>
        <div class="col">
            <label>{{ __('Price')}}</label>
            <input type="number" name="price" id="price" required="required" placeholder="{{ __('Price') }}" min="0" class="form-control form-control-lg">
        </div>
        <div class="col">
            <button type="button" name="btn-add-product" id="btn-add-product" onclick="Order.addItem()" class="btn btn-primary btn-lg btn-primary-outlined mt-4">
                <i class="las la-plus"></i>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col mt-2">
            <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody id="order-items">

            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">&nbsp;</th>
                    <th class="text-right">{{ __('Total') }}</th>
                    <th>
                        <input type="number" name="total" id="total" value="{{ $total ?? 0 }}" readonly="readonly" class="">
                    </th>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
    <div>
        <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
    </div>
</form>

<template id="product-row">
<tr>
    <td>
        <input type="hidden" name="product_id[]" value="">
    </td>
    <td></td>
    <td></td>
    <td></td>
</tr>
</template>
@push('scripts')
<script src="{{ asset('/asset/js/Order.js') }}"></script>
@endpush
@endsection
