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
            <select name="customer_id" id="customer_id" required="required" class="form-control">
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
            <select name="customer_id" id="customer_id" class="form-control">
                <option value="">{{ __('Choose') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
    </div>
</form>
@endsection
