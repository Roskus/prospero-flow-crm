@extends('layouts.app')

@section('content')
<header>
   <h1>{{ trans('Orders') }}</h1>
</header>

<div class="mb-2">
  <a href="/order/add" class="btn btn-primary">{{ trans('New order') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th>#ID</th>
        <th>{{ __('Customer') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
    <tr>
        <td>{{ $order->id }}</td>
        <td>{{ $order->customer->last_name.' '.$order->customer->first_name }}</td>
        <td>{{ $order->getAmount() }}</td>
        <td>{{ $order->customer->created_at->format('Y-m-d H:i:s') }}</td>
        <td>
            <a href="/order/edit/{{ $order->id}}" class="btn bt-xs btn-warning text-white">
                <i class="las la-pen"></i>
            </a>
            <a href="/order/delete/{{ $order->id}}" class="btn bt-xs btn-danger">
                <i class="las la-trash-alt"></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>

    <div>
            {{ $orders->links() }}
    </div>
</div>
@endsection

