@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Suppliers') }}</h1>
    </header>

    <div class="mb-2">
        <a href="{{ url('/supplier/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
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
                <td>
                    <a href="{{ url("/supplier/update/$supplier->id") }}">{{ $supplier->name }}</a>
                </td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email }}</td>
                <td>
                    @if($supplier->website)
                    <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                    @endif
                </td>
                <td>{{ $supplier->country_id }}</td>
                <td>
                    <a href="{{ url("/supplier/update/$supplier->id") }}"></a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
@endsection
