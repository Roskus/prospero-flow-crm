@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Orders') }}</h1>
</header>

<div>
  <a href="/order/new" class="btn btn-primary">{{ trans('hammer.New order') }}</a>
</div>

<div class="table-responsive">
  <table class="table">
  <thead>
  <tr>
      <th>#ID</th>
      <th>{{ trans('hammer.Customer') }}</th>
      <th>{{ trans('hammer.Amount') }}</th>
  </tr>
  </thead>
  <tbody>

  </tbody>
  </table>
</div>
@endsection