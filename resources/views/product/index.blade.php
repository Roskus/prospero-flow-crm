@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Products') }}</h1>
</header>

<div class="mb-2">
  <a href="/product/add" class="btn btn-primary">{{ trans('New product') }}</a>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-striped table-hover table-condensed">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ trans('Category') }}</th>
      <th>{{ trans('Name') }}</th>
      <th>{{ trans('Brand') }}</th>
      <th class="text-center">{{ trans('Quantity') }}</th>
      <th>{{ trans('Price') }}</th>
      <th>{{ trans('Created at') }}</th>
      <th>{{ trans('Actions') }}</th>
  </tr>
  </thead>
  <tbody>
  @foreach($products as $product)
  <tr>
      <td>{{ $product->id }}</td>
      <td>{{ $product->category->name }}</td>
      <td><a href="/product/edit/{{ $product->id }}">{{ $product->name }}</a></td>
      <td>{{ $product->brand->name }}</td>
      <td class="text-center">{{ $product->quantity }}</td>
      <td>{{ $product->price }}</td><!--Money::format(-->
      <td>{{ $product->created_at }}</td>
      <td>
        <a href="/product/edit/{{ $product->id}}" class="btn bt-xs btn-warning text-white">
            <i class="las la-pen"></i>
        </a>
        <a href="/product/delete/{{ $product->id}}" class="btn bt-xs btn-danger">
            <i class="las la-trash-alt"></i>
        </a>
      </td>
  </tr>
  @endforeach
  </tbody>
  </table>
</div>
@endsection
