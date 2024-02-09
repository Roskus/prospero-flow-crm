@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Companies') }}</h1>
</header>

@can('create company')
<div class="mb-2">
    <a href="{{ url("/company/create") }}" class="btn btn-primary">{{ __('New') }}</a>
</div>
@endcan

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>#ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Tax identification') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>E-mail</th>
                <th>{{ __('Website') }}</th>
                <th>{{ __('Country') }}</th>
                <th>{{ __('Currency') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
            <tr>
                <td>{{ $company->id }}</td>
                <td>
                    @can('update company')
                        <a href="{{ url("/company/update/$company->id") }}">
                        {{ $company->name }}
                        </a>
                    @else
                        {{ $company->name }}
                    @endcan
                </td>
                <td>{{ $company->business_name }}</td>
                <td>{{ $company->vat }}</td>
                <td>{{ $company->phone }}</td>
                <td>
                    <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                </td>
                <td>
                    <a href="{{ $company->website }}" rel="noopener" target="_blank">{{ $company->website }}</a>
                </td>
                <td class="text-center">
                    <span title="{{ (!empty($company->country)) ? $company->country->name : '' }}">
                        {{ (!empty($company->country)) ? $company->country->flag : '' }}
                    </span>
                </td>
                <td class="text-center">{{ $company->currency }}</td>
                <td>{{ ($company->created_at) ? $company->created_at->format('d/m/Y H:i') : '' }}</td>
                <td>{{ ($company->updated_at) ? $company->updated_at->format('d/m/Y H:i') : '' }}</td>
                <td class="text-nowrap">
                @can('update company')
                <a href="{{ url("/company/update/$company->id") }}" class="btn btn-warning" title="{{ __('Update') }}">
                    <i class="las la-edit"></i>
                </a>
                @endcan

                @can('delete company')
                <a href="{{ url("/company/delete/$company->id") }}" class="btn btn-danger" title="{{ __('Delete') }}">
                    <i class="las la-trash-alt"></i>
                </a>
                @endcan
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $companies->appends(request()->query())->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
@endsection
