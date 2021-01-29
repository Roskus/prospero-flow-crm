@extends('layouts.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Customers') }}</h1>
</header>

<div class="">
    <a href="/customer/add" class="btn btn-primary">{{ __('New') }}</a>
</div>

<div class="table-responsive mt-2">
    <table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>#ID</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>E-mail</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customers as $customer)
    <tr>
        <td>{{ $customer->id }}</td>
        <td>
            <a href="/customer/edit/{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</a>
        </td>
        <td>{{ $customer->phone }}</td>
        <td>{{ $customer->email }}</td>
        <td>
            <a onclick="Customer.delete({{ $customer->id }}, '{{ $customer->first_name }} {{ $customer->last_name }}');" class="btn btn-xs btn-danger">D
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection
