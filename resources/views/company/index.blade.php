@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Companies') }}</h1>
</header>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        @if(Auth::user()->canCreateCompany())
        <a href="{{ url("/company/create") }}" class="btn btn-primary">{{ __('New') }}</a>
        @endif
        @if($availableCompaniesQuota !== null)
        <span class="ms-2 text-muted">{{ __('Available companies: :available', ['available' => $availableCompaniesQuota]) }}</span>
        @endif
    </div>
    @role('SuperAdmin')
    <form method="GET" action="{{ route('company.index') }}">
        <select name="filter" class="form-select" onchange="this.form.submit()" aria-label="{{ __('Status') }}">
            <option value="active" @selected(in_array(request('filter'), [null, 'active'], true))>{{ __('Active') }}</option>
            <option value="deleted" @selected(request('filter') === 'deleted')>{{ __('Deleted') }}</option>
            <option value="all" @selected(request('filter') === 'all')>{{ __('All') }}</option>
        </select>
    </form>
    @endrole
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
            <caption>{{ __('Company list') }}</caption>
            <thead>
            <tr>
                <th>#ID</th>
                <th>Logo</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Tax identification') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>E-mail</th>
                <th>{{ __('Website') }}</th>
                <th>{{ __('Country') }}</th>
                <th>{{ __('Currency') }}</th>
                <th>{{ __('Status') }}</th>
                @hasanyrole('SuperAdmin|CompanyAdmin|MultiCompanyAdmin')
                <th>{{ __('Managed by') }}</th>
                <th class="text-center">{{ __('Companies quota') }}</th>
                @endhasanyrole
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $company)
            <tr @if($company->trashed()) class="table-danger" @endif>
                <td>{{ $company->id }}</td>
                <td>
                    @if(!empty($company->logo))
                        <img src="/storage/company/{{ \Illuminate\Support\Str::slug($company->name, '_') }}/{{ $company->logo }}"
                             alt="{{ $company->name }}" style="max-height:40px; max-width:80px;">
                    @endif
                </td>
                <td>
                    @if(Auth::user()->canUpdateCompany($company->id))
                        <a href="{{ url("/company/update/$company->id") }}">
                        {{ $company->name }}
                        </a>
                    @else
                        {{ $company->name }}
                    @endif
                </td>
                <td>{{ $company->business_name }}</td>
                <td>{{ $company->vat }}</td>
                <td>{{ $company->phone }}</td>
                <td>
                    <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                </td>
                <td>
                    @isset($company->website)
                    <a href="{{ $company->website }}" rel="noopener" target="_blank">{{ $company->website }}</a>
                    @endisset
                </td>
                <td class="text-center">
                    <span title="{{ (!empty($company->country)) ? $company->country->name : '' }}">
                        {{ (!empty($company->country)) ? $company->country->flag : '' }}
                    </span>
                </td>
                <td class="text-center">{{ strtoupper($company->currency) }}</td>
                <td class="text-center">
                    @if($company->status == \App\Models\Company::ACTIVE)
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                    @endif
                </td>
                @hasanyrole('SuperAdmin|CompanyAdmin|MultiCompanyAdmin')
                <td>{{ $company->parent?->name }}</td>
                <td class="text-center">
                    @if($company->managed_companies_limit > 0 || $company->client_companies_count > 0)
                        {{ $company->client_companies_count }} / {{ $company->managed_companies_limit }}
                    @else
                        0
                    @endif
                </td>
                @endhasanyrole
                <td>{{ ($company->created_at) ? $company->created_at->format('d/m/Y H:i') : '' }}</td>
                <td>{{ ($company->updated_at) ? $company->updated_at->format('d/m/Y H:i') : '' }}</td>
                <td class="text-nowrap">
                @if(Auth::user()->canUpdateCompany($company->id))
                <a href="{{ url("/company/update/$company->id") }}" class="btn btn-warning" title="{{ __('Update') }}">
                    <i class="las la-edit"></i>
                </a>
                @endif

                @can('delete company')
                @unless($company->trashed())
                <form method="POST" action="{{ url("/company/delete/$company->id") }}" class="d-inline"
                      onsubmit="return confirm('{{ __('Are you sure you want to delete this company?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" title="{{ __('Delete') }}">
                        <i class="las la-trash-alt"></i>
                    </button>
                </form>
                @else
                <form method="POST" action="{{ url("/company/restore/$company->id") }}" class="d-inline"
                      onsubmit="return confirm('{{ __('Are you sure you want to restore this company?') }}')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success" title="{{ __('Restore') }}">
                        <i class="las la-trash-restore"></i>
                    </button>
                </form>
                @endunless
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
