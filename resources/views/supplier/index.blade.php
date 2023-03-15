@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Suppliers')])
    @include('layouts.partials._search_buttons_bar', [
        'action_search' => url("/supplier"), 
        'buttons' => [
            ['url' => url('/supplier/create'), 'class' => 'btn btn-primary', 'text' => __('New')],
        ]
    ])

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
                <th>{{ __('Updated at') }}</th>
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
                <td>
                    @if($supplier->email)
                    <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                    @endif
                </td>
                <td>
                    @if($supplier->website)
                    <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                    @endif
                </td>
                <td class="text-center d-sm-table-cell" title="{{ !empty($supplier->country) ? $supplier->country->name : '' }}">
                    {{ !empty($supplier->country) ? $supplier->country->flag : '' }}
                </td>
                <td>
                    {{ !empty($supplier->updated_at) ? $supplier->updated_at->format('d/m/Y H:i') : '' }}
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
