@extends('layouts.app')

@section('content')
<header>
   <h1>{{ trans('Orders') }}</h1>
</header>

<div class="mb-2">
  <a href="{{ url('order/create') }}" class="btn btn-primary">{{ __('New') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-condensed">
    <thead>
    <tr>
        <th>#ID</th>
        <th>{{ __('Customer') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Updated at') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
    <tr>
        <td>{{ $order->id }}</td>
        <td>{{ (!empty($order->customer)) ? $order->customer->name : '' }}</td>
        <td>{{ $order->getAmount() }}</td>
        <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
        <td>{{ $order->updated_at->format('d/m/Y H:i:s') }}</td>
        <td>{{ $order->status }}</td>
        <td>
            <a href="{{ url('/order/update/'.$order->id) }}" class="btn bt-xs btn-warning text-white" title="{{ __('Edit') }}">
                <i class="las la-pen"></i>
            </a>
            <a href="{{ url('/order/delete/'.$order->id) }}" class="btn bt-xs btn-danger" title="{{ __('Delete') }}">
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

