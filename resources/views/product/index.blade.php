@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Products') }}</h1>
</header>

<div class="mb-2">
  <a href="{{ url('/product/create') }}" class="btn btn-primary">{{ __('New') }}</a>
</div>

<div class="row mt-2">
    <div class="col">
    <form method="post" action="{{ url('/product') }}" class="form-inline mb-2">
        @csrf
        <div class="input-group">
            <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit" id="btn-search"><i class="las la-search"></i></button>
            </div>
        </div>
    </form>
    </div><!--./col-->
</div><!--./row-->

<div class="table-responsive">
  <table class="table table-bordered table-striped table-hover table-condensed">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ __('Photo') }}</th>
      <th>{{ __('Category') }}</th>
      <th>{{ __('Name') }}</th>
      <th>{{ __('Brand') }}</th>
      <th class="text-center">{{ __('Quantity') }}</th>
      <th>{{ __('Price') }}</th>
      <th>{{ __('Created at') }}</th>
      <th>{{ __('Actions') }}</th>
  </tr>
  </thead>
  <tbody>
  @foreach($products as $product)
  <tr>
      <td>{{ $product->id }}</td>
      <td>
          @if($product->photo)
              <img src="{{ asset("/asset/upload/product/$product->id/$product->photo")}}" alt="" class="img-fluid img-thumbnail">
          @endif
      </td>
      <td>{{ $product->category->name }}</td>
      <td><a href="{{ url("/product/update/$product->id") }}">{{ $product->name }}</a></td>
      <td>{{ $product->brand->name }}</td>
      <td class="text-center">
          <span class="@if($product->quantity < $product->min_stock_quantity) text-danger @else text-success @endif">
          {{ $product->quantity }}
          </span>
      </td>
      <td>{{ $product->price }} {{ Auth::user()->company->country->currency->symbol }}</td><!--Money::format(-->
      <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
      <td>
        <a href="{{ url("/product/update/$product->id") }}" class="btn bt-xs btn-warning text-white">
            <i class="las la-pen"></i>
        </a>
        <a href="{{ url("/product/delete/$product->id") }}" class="btn bt-xs btn-danger">
            <i class="las la-trash-alt"></i>
        </a>
      </td>
  </tr>
  @endforeach
  </tbody>
  </table>
</div>
@endsection
