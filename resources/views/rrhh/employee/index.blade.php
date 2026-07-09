@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Employees'), 'count' => $employees->total()])

<div class="row">
    <div class="col">
        <a href="{{ url('/rrhh/employee/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>
    <div class="col">
        <form method="get" action="{{ url('/rrhh') }}" class="form-inline mb-2">
            @csrf
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ request('search') }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit"><i class="las la-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-2">
            <table class="table table-striped table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>{{ __('Employee #') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Hire date') }}</th>
                <th>{{ __('Seniority') }}</th>
                <th>{{ __('Manager') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->employee_number }}</td>
                <td><a href="{{ url("/rrhh/employee/show/{$employee->id}") }}">{{ $employee->first_name }} {{ $employee->last_name }}</a></td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->phone }}</td>
                <td>{{ $employee->hire_date?->format('Y-m-d') }}</td>
                <td>@if($employee->hire_date)
                    @php $diff = $employee->hire_date->diff(now()); @endphp
                    {{ $diff->y }}a {{ $diff->m }}m
                    @endif</td>
                <td>{{ $employee->manager?->first_name }} {{ $employee->manager?->last_name }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/rrhh/employee/show/{$employee->id}") }}" class="btn btn-xs btn-info text-white"><i class="las la-eye"></i></a>
                    <a href="{{ url("/user/update/{$employee->id}") }}" class="btn btn-xs btn-warning text-white"><i class="las la-pen"></i></a>
                    @can('delete rrhh')
                    <a onclick="if(!confirm('{{ __('Are you sure?') }}')) return false; window.location='{{ url("/user/delete/{$employee->id}") }}'" class="btn btn-xs btn-danger"><i class="las la-trash-alt"></i></a>
                    @endcan
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
            <div>{{ $employees->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
