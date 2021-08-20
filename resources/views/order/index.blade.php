@extends('layouts.app')

@section('content')
<header>
   <h1>{{ trans('Orders') }}</h1>
</header>

<div>
  <a href="/order/add" class="btn btn-primary">{{ trans('New order') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th>#ID</th>
        <th>{{ trans('Customer') }}</th>
        <th>{{ trans('Amount') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
    @endforeach
    </tbody>
    </table>
</div>
@endsection
