@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Leads') }}</h1>
</header>

<div class="row">
    <div class="col d-flex">
        <a href="{{ url('/lead/create') }}" class="btn btn-primary d-flex flex-fill align-items-center justify-content-center">{{ __('New') }}</a>
    </div>
    <div class="col">
        <div class="btn-group d-flex" role="group" aria-label="Basic mixed styles example">
            <a href="{{ url('/lead/import') }}" class="btn btn-success d-block">{{ __('Import') }} <i class="las la-file-csv d-none d-sm-block"></i></a>
            <a href="{{ url('/lead/export') }}" class="btn btn-info d-block">{{ __('Export') }} <i class="las la-file-csv d-none d-sm-block"></i></a>
        </div><!--./btn-group-->
    </div>
</div>

@if(session('status'))
    <div class="alert alert-{{ session('status') }} mt-2">
        {!! __(session('message'), ['id'=> session('id'), 'name' => "<a href=".url("/lead/update/".session('id')).">".session('name')."</a>"])  !!}
    </div>
@endif

<div class="row mt-2">
    <div class="col">
        <form method="get" action="{{ url("/lead") }}" class="form-inline mb-2">
            @csrf
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit" id="btn-search"><i class="las la-search"></i></button>
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
            @foreach($leads as $lead)
            <tr>
                <td class="text-nowrap">
                    <a href="{{ url("/lead/update/$lead->id") }}" title="{{ __('Update') }}">{{ $lead->name }}</a>
                </td>
                <td class="text-nowrap">{{ $lead->business_name }}</td>
                <td class="text-nowrap">
                    @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" target="_blank">{{ $lead->phone }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    @if($lead->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}" target="_blank">{{ $lead->mobile }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    @if($lead->email)
                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                    @endif
                </td>
                <td class="text-nowrap">
                    <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                </td>
                <td class="text-center d-sm-table-cell" title="{{ $lead->country->name }}">
                    {{ $lead->country->flag }}
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    @if($lead->facebook)
                        <a href="{{ $lead->facebook }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-facebook-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->instagram)
                        <a href="{{ $lead->instagram }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-instagram fs-3"></i>
                        </a>
                    @endif

                    @if($lead->linkedin)
                        <a href="{{ $lead->linkedin }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-linkedin fs-3"></i>
                        </a>
                    @endif

                    @if($lead->youtube)
                        <a href="{{ $lead->youtube }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-youtube-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->twitter)
                        <a href="{{ $lead->twitter }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-twitter-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}" target="_blank" class="text-decoration-none">
                            <i class="lab la-whatsapp fs-3"></i>
                        </a>
                    @endif
                </td>
                <td class="text-center text-nowrap">{{ $lead->seller->first_name }}</td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">{{ ($lead->industry) ? __($lead->industry->name) : '' }}</td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    @if($lead->tags)
                        @foreach($lead->tags as $tag)
                            <a href="{{ url("/lead?search=$tag") }}" class="badge {{ $bootstrap_colors[array_rand($bootstrap_colors)] }} text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status) }}">{{ $lead->status }}</span>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap d-none d-sm-table-cell">{{ $lead->updated_at->format('d/m/Y H:i') }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/lead/update/$lead->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    <a href="{{  url("/lead/promote/$lead->id") }}" title="{{ __('Promote') }}" class="btn btn-xs btn-success text-white">
                        <i class="las la-user-tie"></i>
                    </a>

                    <a onclick="Lead.delete({{ $lead->id }}, '{{ $lead->name }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $leads->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    const Lead = {
        delete : function(id, name) {
            let res = confirm("{{ __('Are you sure you want to delete the lead?') }}");
            if(res)
                window.location = '{{ url('/lead/delete/') }}/'+id;
        }
    };
</script>
@endsection
