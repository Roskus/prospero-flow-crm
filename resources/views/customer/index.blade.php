@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Customers'), 'count' => $customer_count])

<div class="row">
    <div class="col d-flex">
        <a href="{{ url('/customer/create') }}" class="btn btn-primary d-flex flex-fill align-items-center justify-content-center">{{ __('New') }}</a>
    </div>
    <div class="col">
        <div class="btn-group d-flex" role="group" aria-label="Basic mixed styles example">
            <a href="{{ url('/customer/import') }}" class="btn btn-success d-block">{{ __('Import') }} <i class="las la-file-csv d-none d-sm-block"></i></a>
            <a href="{{ url('/customer/export') }}" class="btn btn-info d-block">{{ __('Export') }} <i class="las la-file-csv d-none d-sm-block"></i></a>
        </div><!--./btn-group-->
    </div>
</div>

@if(session('status'))
    <div class="alert alert-{{ session('status') }} mt-2">
        {!! __(session('message'), ['id'=> session('id'), 'name' => "<a href=".url("/customer/update/".session('id')).">".session('name')."</a>"])  !!}
    </div>
@endif

<div class="row mt-2">
    <div class="col">
        <form method="get" action="{{ url("/customer") }}" class="form-inline mb-2">
            @csrf
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit" id="btn-search"><i class="las la-search"></i></button>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">{{ __('Advanced search') }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label for="country_id">{{ __('Country') }}</label>
                            <select name="country_id" id="country_id" class="form-select">
                                <option value=""></option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->code_2 }}" @if((!empty($country_id)) && $country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="status">{{ __('Status') }}</label>
                            <select name="status" id="status"  class="form-select">
                                <option value=""></option>
                                @foreach($statuses as $key => $status)
                                <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="seller_id">{{ __('Seller') }}</label>
                            <select name="seller_id" id="seller_id" class="form-select">
                                <option value=""></option>
                                @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}">{{ $seller->getFullName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="industry_id">{{ __('Industry') }}</label>
                            <select name="industry_id" id="industry_id" class="form-select">
                                <option value=""></option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><!--./row-->
                </div>
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
                <th>{{ __('Name') }}</th>
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Mobile') }}</th>
                <th>E-mail</th>
                <th>Website</th>
                <th>{{ __('Country') }}</th>
                <th class="d-none d-sm-table-cell">Social</th>
                <th class="text-center">{{ __('Seller') }}</th>
                <th class="text-center d-none d-sm-table-cell">{{ __('Industry') }}</th>
                <th class="text-center d-none d-sm-table-cell">{{ __('Tags') }}</th>
                <th class="d-none d-sm-table-cell">{{ __('Status') }}</th>
                <th class="d-none d-sm-table-cell">{{ __('Created at') }}</th>
                <th class="d-none d-sm-table-cell">{{ __('Updated at') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($customers as $customer)
            <tr>
                <td class="text-nowrap">
                    <a href="{{ url("/customer/update/$customer->id") }}" title="{{ __('Update') }}">{{ $customer->name }}</a>
                </td>
                <td class="text-nowrap">{{ $customer->business_name }}</td>
                <td class="text-nowrap">
                    @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" target="_blank">{{ $customer->phone }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    @if($customer->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $customer->mobile }}&text={{ __('Hello') }}" target="_blank">{{ $customer->mobile }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    @if($customer->email)
                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    <a href="{{ $customer->website }}" target="_blank">{{ $customer->website }}</a>
                </td>
                <td class="text-center d-sm-table-cell" title="{{ (!empty($customer->country)) ? $customer->country->name : '' }}">
                    @if(!empty($customer->country))
                    {{ $customer->country->flag }}
                    @endif
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    @if($customer->facebook)
                        <a href="{{ $customer->facebook }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-facebook-square fs-3"></i>
                        </a>
                    @endif

                    @if($customer->instagram)
                        <a href="{{ $customer->instagram }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-instagram fs-3"></i>
                        </a>
                    @endif

                    @if($customer->linkedin)
                        <a href="{{ $customer->linkedin }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-linkedin fs-3"></i>
                        </a>
                    @endif

                    @if($customer->youtube)
                        <a href="{{ $customer->youtube }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-youtube-square fs-3"></i>
                        </a>
                    @endif

                    @if($customer->twitter)
                        <a href="{{ $customer->twitter }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-twitter-square fs-3"></i>
                        </a>
                    @endif

                    @if($customer->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $customer->mobile }}&text={{ __('Hello') }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-whatsapp fs-3"></i>
                        </a>
                    @endif
                </td>
                <td class="text-center text-nowrap">{{ (!empty($customer->seller)) ? $customer->seller->first_name : '' }}</td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">{{ ($customer->industry) ? __($customer->industry->name) : '' }}</td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    @if($customer->tags)
                        @foreach($customer->tags as $tag)
                            <a href="{{ url("/customer?search=$tag") }}" class="badge {{ $bootstrap_colors[array_rand($bootstrap_colors)] }} text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    <span class="badge {{ App\Helpers\LeadStatus::renderBadge($customer->status) }}">{{ $customer->status }}</span>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap d-none d-sm-table-cell">{{ $customer->updated_at->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/customer/update/$customer->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    <a onclick="Customer.delete({{ $customer->id }}, '{{ $customer->name }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
<script>
    const Customer = {
        delete : function(id, name) {
            let res = confirm("{{ __('Are you sure you want to delete the customer?') }}");
            if(res)
                window.location = '{{ url('/customer/delete') }}/'+id;
        }
    };
</script>
@endsection
