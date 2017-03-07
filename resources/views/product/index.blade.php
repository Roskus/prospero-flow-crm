@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Products') }}</h1>
</header>

<div>
  <a href="/product/new" class="btn btn-primary">{{ trans('hammer.New product') }}</a>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover table-condensed">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ trans('hammer.Name') }}</th>
      <th>{{ trans('hammer.Quantity') }}</th>
      <th>{{ trans('hammer.Price') }}</th>
  </tr>
  </thead>
  <tbody>
  @foreach($products as $product)
  <tr>
      <td>{{ $product->id }}</td>
      <td><a href="/product/edit/{{ $product->id }}">{{ $product->name }}</a></td>
      <td>{{ $product->quantity }}</td>
      <td>{{ $product->price }}</td>
  </tr>
  @endforeach
  </tbody>
  </table>
</div>
@endsection