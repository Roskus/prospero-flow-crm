@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Products') }}</h1>
</header>

<div>
  <a href="/product/new" class="btn btn-primary">{{ trans('hammer.New product') }}</a>
</div>

<div class="table-responsive">
  <table class="table">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ trans('hammer.Name') }}</th>
      <th>{{ trans('hammer.Quantity') }}</th>
      <th>{{ trans('hammer.Price') }}</th>
  </tr>
  </thead>
  <tbody>

  </tbody>
  </table>
</div>
@endsection