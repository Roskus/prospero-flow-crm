@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Customers') }}</h1>
</header>

<div class="table-responsive">
    <table class="table">
    <thead>
    <tr>
        <th>#ID</th>
        <th>Name</th>
        <th>{{ trans('hammer.Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customers as $customer)
    <tr>
        <td>{{ $customer->id }}</td>
        <td>
            <a href="/customer/edit/{{ $customer->id }}">{{ $customer->company_name }}</a>
        </td>
        <td></td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection