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
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Identity number') }}</th>
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
                <td>{{ $supplier->business_name }}</td>
                <td>{{ $supplier->vat }}</td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email }}</td>
                <td>
                    @if($supplier->website)
                    <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                    @endif
                </td>
                <td class="text-center d-sm-table-cell" title="{{ !empty($supplier->country) ? $supplier->country->name : '' }}">
                    {{ !empty($supplier->country) ? $supplier->country->flag : '' }}
                </td>
                <td class="text-nowrap">
                    <a href="{{ url("/supplier/update/$supplier->id") }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    <a onclick="Supplier.delete({{ $supplier->id }}, '{{ $supplier->name }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
@endsection
