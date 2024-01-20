@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Leads'), 'count' => $lead_count])

<div class="row">
    <div class="col d-flex">
        <a href="{{ url('/lead/create') }}" class="btn btn-primary d-flex flex-fill align-items-center justify-content-center">{{ __('New') }}</a>
    </div>
    <div class="col">
        <div class="btn-group d-flex" role="group" aria-label="Basic mixed styles example">
            <a href="{{ url('/lead/import') }}" class="btn btn-success d-block">
                {{ __('Import') }} <i class="las la-file-csv d-none d-sm-block"></i>
            </a>

            @can('export lead')
            <a href="{{ url('/lead/export') }}" class="btn btn-info d-block">
                {{ __('Export') }} <i class="las la-file-csv d-none d-sm-block"></i>
            </a>
            @endcan
        </div><!--./btn-group-->
    </div>
</div>

@if(session('status'))
    <div class="alert alert-{{ session('status') }} mt-2">
        @if(session('id'))
            {!! __(session('message'), ['id'=> session('id'), 'name' => "<a href=".url("/lead/update/".session('id')).">".session('name')."</a>"])  !!}
        @else
            {{ __(session('message'), ['count' => session('count')]) }}
        @endif
    </div>
@endif

<div class="row mt-2">
    <div class="col">
        <form method="get" action="{{ url("/lead") }}" class="form-inline mb-2">
            @csrf
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit" id="btn-search">
                    <i class="las la-search"></i>
                </button>
            </div>
            @include('lead_customer.partials.filters')
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-2">
            <table class="table table-striped table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>#ID</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['order_by' => 'name']) }}"
                       class="link-secondary link-underline-opacity-25 link-underline-opacity-100-hover">
                        {{ __('Name') }}
                    </a>
                </th>
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Mobile') }}</th>
                <th>Email</th>
                <th>Website</th>
                <th>{{ __('Country') }}</th>
                <th>{{ __('Province') }}</th>
                <th class="d-none d-sm-table-cell">Social</th>
                <th class="text-center d-none d-sm-table-cell">
                    <a href="{{ request()->fullUrlWithQuery(['order_by' => 'industry_id']) }}"
                       class="link-secondary link-underline-opacity-25 link-underline-opacity-100-hover">
                    {{ __('Industry') }}
                    </a>
                </th>
                <th class="text-center">{{ __('Seller') }}</th>
                <th class="text-center d-none d-sm-table-cell">{{ __('Tags') }}</th>
                <th class="d-none d-sm-table-cell">{{ __('Status') }}</th>
                <th class="d-none d-sm-table-cell">
                    <a href="{{ request()->fullUrlWithQuery(['order_by' => 'created_at']) }}"
                       class="link-secondary link-underline-opacity-25 link-underline-opacity-100-hover">
                        {{ __('Created at') }}
                    </a>
                </th>
                <th class="d-none d-sm-table-cell">
                    <a href="{{ request()->fullUrlWithQuery(['order_by' => 'updated_at']) }}"
                       class="link-secondary link-underline-opacity-25 link-underline-opacity-100-hover">
                        {{ __('Updated at') }}
                    </a>
                </th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($leads as $lead)
            <tr>
                <td class="text-nowrap text-center">{{ $lead->id }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/lead/show/$lead->id") }}" title="{{ __('Show') }}">{{ $lead->name }}</a>
                </td>
                <td class="text-nowrap">{{ $lead->business_name }}</td>
                <td class="text-nowrap text-center">
                    @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}@isset($lead->extension),{{$lead->extension}}@endisset" title="{{ \App\Helpers\PhoneHelper::format($lead->phone) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-phone fs-4"></i>
                        </a>
                        @if($lead->phone_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif

                        <a href="sip:{{ $lead->phone }}@isset($lead->extension),{{$lead->extension}}@endisset" title="{{ \App\Helpers\PhoneHelper::format($lead->phone) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-headset fs-4"></i>
                        </a>
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($lead->mobile)
                        <a href="tel:{{ $lead->mobile }}"
                           title="{{ \App\Helpers\PhoneHelper::format($lead->mobile) }}" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="las la-mobile fs-4"></i>
                        </a>

                        @if($lead->mobile_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif

                        <a href="sip:{{ $lead->mobile }}" title="{{ \App\Helpers\PhoneHelper::format($lead->mobile) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-headset fs-4"></i>
                        </a>

                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}"
                           title="{{ \App\Helpers\PhoneHelper::format($lead->mobile) }}" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="lab la-whatsapp fs-4"></i>
                        </a>

                        <a href="https://telegram.me/{{ $lead->mobile }}"
                           title="{{ \App\Helpers\PhoneHelper::format($lead->mobile) }}" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="lab la-telegram-plane fs-4"></i>
                        </a>
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" title="{{ $lead->email }}" class="link-secondary text-decoration-none"><i class="las la-envelope fs-4"></i></a>
                        @if($lead->email_verified)
                            <i class="las la-check-circle text-success"></i>
                        @elseif($lead->email_verified == 3)
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($lead->website)
                    <a href="{{ $lead->website }}" title="{{ $lead->website }}" target="_blank" class="link-secondary text-decoration-none">
                        <i class="las la-globe fs-4"></i>
                    </a>
                    @endif
                </td>
                <td class="text-center d-sm-table-cell link-secondary text-decoration-none"
                    title="{{ (!empty($lead->country)) ? $lead->country->name : '' }}">
                    @if(!empty($lead->country))
                    {{ $lead->country->flag }}
                    @endif
                </td>
                <td class="text-nowrap d-sm-table-cell">
                    {{ $lead->province }}
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    @if($lead->facebook)
                        <a href="{{ $lead->facebook }}" target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-facebook-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->instagram)
                        <a href="{{ $lead->instagram }}" target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-instagram fs-3"></i>
                        </a>
                    @endif

                    @if($lead->linkedin)
                        <a href="{{ $lead->linkedin }}" target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-linkedin fs-3"></i>
                        </a>
                    @endif

                    @if($lead->youtube)
                        <a href="{{ $lead->youtube }}" target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-youtube-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->twitter)
                        <a href="{{ $lead->twitter }}" target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-twitter-square fs-3"></i>
                        </a>
                    @endif

                    @if($lead->tiktok)
                        <a href="{{ $lead->tiktok }}" target="_blank" class="text-decoration-none link-secondary">
                            <span class="tiktok"><i class="fa-brands fa-tiktok"></i></span>
                        </a>
                    @endif

                    @if($lead->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}"
                           target="_blank" class="text-decoration-none link-secondary">
                            <i class="lab la-whatsapp fs-3"></i>
                        </a>
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">{{ ($lead->industry) ? __($lead->industry->name) : '' }}</td>
                <td class="text-center text-nowrap">
                    @isset($lead->seller)
                    {{ $lead->seller->first_name }}
                    @endisset
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    @if($lead->tags)
                        @foreach($lead->tags as $tag)
                            <a href="{{ url("/lead?search=$tag") }}" class="badge {{ $bootstrap_colors[array_rand($bootstrap_colors)] }} text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status) }}">{{ __(ucfirst($lead->status)) }}</span>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    <small class="text-muted">{{ $lead->created_at->format('d/m/y H:i') }}</small>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    <small class="text-muted">{{ $lead->updated_at->format('d/m/y H:i') }}</small>
                </td>
                <td class="text-nowrap">
                    <a href="{{ url("/lead/update/$lead->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    <a href="{{  url("/lead/promote/$lead->id") }}" title="{{ __('Promote') }}" class="btn btn-xs btn-success text-white">
                        <i class="las la-user-tie"></i>
                    </a>

                    @can('delete lead')
                    <a onclick="Lead.delete({{ $lead->id }}, '{{ $lead->name }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                    @endcan
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $leads->appends(request()->query())->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
<script>
    const Lead = {
        delete : function(id, name) {
            let message = `{{ __('Are you sure you want to delete the lead: ') }}${name}?`;
            let res = confirm(message);
            if(res)
                window.location = '{{ url('/lead/delete') }}/'+id;
        }
    };
</script>
@endsection
