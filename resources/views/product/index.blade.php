@extends('layout.app')

@section('content')
<header>
   <h1>{{ __('Products') }}</h1>
</header>

<div>
  <a href="/product/add" class="btn btn-primary">{{ trans('New product') }}</a>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover table-condensed">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ trans('Name') }}</th>
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
      <td><a href="/product/edit/{{ $product->id }}">{{ $product->name }}</a></td>
      <td class="text-center">{{ $product->quantity }}</td>
      <td>{{ $product->price }}</td>
      <td>{{ $product->created_at }}</td>
      <td>
      </td>
  </tr>
  @endforeach
  </tbody>
  </table>
</div>
@endsection
