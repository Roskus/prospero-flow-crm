@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Companies') }}</h1>
</header>

<div class="mb-2">
    <a href="/company/add" class="btn btn-primary">{{ __('New') }}</a>
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
    </tr>
    </thead>
    <tbody>
    @foreach($companies as $company)
    <tr>
        <td>
            <a href="/company/edit/{{ $company->id }}">
            {{ $company->name }}
            </a>
        </td>
        <td>{{ $company->phone }}</td>
        <td>{{ $company->email }}</td>
        <td>{{ $company->website }}</td>
        <td>{{ $company->country_id }}</td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection
