@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Suppliers') }}</h1>
    </header>

    <div class="mb-2">
        <a href="{{ url('/supplier/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>E-mail</th>
        <th>{{ __('Website') }}</th>
        <th>{{ __('Country') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($suppliers as $supplier)
    <tr>
        <td>{{ $supplier->name }}</td>
        <td>{{ $supplier->phone }}</td>
        <td>{{ $supplier->email }}</td>
        <td>{{ $supplier->website }}</td>
        <td>{{ $supplier->country_id }}</td>
        <td>
            <a href="{{ url("/supplier/update/$supplier->id") }}"></a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
@endsection
