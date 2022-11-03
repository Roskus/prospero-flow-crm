@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Companies') }}</h1>
</header>

<div class="mb-2">
    <a href="{{ url("/company/create") }}" class="btn btn-primary">{{ __('New') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>E-mail</th>
        <th>{{ __('Website') }}</th>
        <th>{{ __('Country') }}</th>
        <th>{{ __('Created at') }}</th>
        <th>{{ __('Updated at') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($companies as $company)
    <tr>
        <td>
            <a href="{{ url("/company/update/$company->id") }}">
            {{ $company->name }}
            </a>
        </td>
        <td>{{ $company->phone }}</td>
        <td>{{ $company->email }}</td>
        <td>
            <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
        </td>
        <td class="text-center"><span title="{{ (!empty($company->country)) ? $company->country->name : '' }}">{{ (!empty($company->country)) ? $company->country->flag : '' }}</span></td>
        <td>{{ $company->created_at }}</td>
        <td>{{ $company->updated_at }}</td>
        <td>
            <a href="{{ url("/company/delete/$company->id") }}" class="btn btn-danger" title="{{ __('Delete') }}"><i class="las la-trash-alt"></i></a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection
