@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Leads') }}</h1>
</header>

<div class="">
    <a href="/lead/create" class="btn btn-primary">{{ __('New') }}</a>
</div>

<div class="table-responsive mt-2">
    <table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>#ID</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>E-mail</th>
        <th>{{ __('Company') }}</th>
        <th>{{ __('Country') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($leads as $lead)
    <tr>
        <td>{{ $lead->id }}</td>
        <td>
            <a href="/lead/update/{{ $lead->id }}">{{ $lead->first_name }} {{ $lead->last_name }}</a>
        </td>
        <td>{{ $lead->phone }}</td>
        <td>{{ $lead->email }}</td>
        <td>{{ $lead->company->name }}</td>
        <td>{{ $lead->country_id }}</td>
        <td>
            <a onclick="/lead/update/{{ $lead->id }}" class="btn btn-xs btn-warning text-white">
                <i class="las la-pen"></i>
            </a>
            <a onclick="Lead.delete({{ $lead->id }}, '{{ $lead->first_name }} {{ $lead->last_name }}');" class="btn btn-xs btn-danger">
                <i class="las la-trash-alt"></i>
            </a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection
