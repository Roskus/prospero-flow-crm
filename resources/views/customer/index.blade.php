@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Customers'), 'count' => $customer_count])

<div class="row">
    <div class="col d-flex">
        <a href="{{ url('/customer/create') }}" class="btn btn-primary d-flex flex-fill align-items-center justify-content-center">{{ __('New') }}</a>
    </div>
    <div class="col">
        <div class="btn-group d-flex" role="group" aria-label="Basic mixed styles example">
            <a href="{{ url('/customer/import') }}" class="btn btn-success d-block">
                {{ __('Import') }} <i class="las la-file-csv d-none d-sm-block"></i>
            </a>

            @can('export customer')
            <a href="{{ url('/customer/export') }}" class="btn btn-info d-block">
                {{ __('Export') }} <i class="las la-file-csv d-none d-sm-block"></i>
            </a>
            @endcan
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
                    <button class="btn btn-outline-primary" type="submit" id="btn-search">
                        <i class="las la-search"></i>
                    </button>
                </div>
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
                <th>{{ __('External ID') }}</th>
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
            @foreach($customers as $customer)
            <tr>
                <td class="text-nowrap text-center">{{ $customer->id }}</td>
                <td class="text-nowrap text-center">{{ $customer->external_id }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/customer/show/$customer->id") }}" title="{{ __('Show') }}">{{ $customer->name }}</a>
                </td>
                <td class="text-nowrap">{{ $customer->business_name }}</td>
                <td class="text-nowrap text-center">
                    @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}@isset($customer->extension),{{$customer->extension}}@endisset"
                           title="{{ \App\Helpers\PhoneHelper::format($customer->phone) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-phone fs-4"></i>
                        </a>
                        @if($customer->phone_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif

                        <a href="sip:{{ $customer->phone }}@isset($customer->extension),{{$customer->extension}}@endisset" title="{{ \App\Helpers\PhoneHelper::format($customer->phone) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-headset fs-4"></i>
                        </a>
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($customer->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $customer->mobile }}&text={{ __('Hello') }}"
                         title="{{ \App\Helpers\PhoneHelper::format($customer->mobile) }}" rel="noopener" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="las la-mobile fs-4"></i>
                        </a>

                        @if($customer->mobile_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif

                        <a href="sip:{{ $customer->mobile }}" title="{{ \App\Helpers\PhoneHelper::format($customer->mobile) }}"
                           target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-headset fs-4"></i>
                        </a>

                        <a href="https://api.whatsapp.com/send/?phone={{ $customer->mobile }}&text={{ __('Hello') }}"
                           title="{{ \App\Helpers\PhoneHelper::format($customer->mobile) }}"
                           rel="noopener" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="lab la-whatsapp fs-4"></i>
                        </a>

                        <a href="https://telegram.me/{{ $customer->mobile }}"
                           title="{{ \App\Helpers\PhoneHelper::format($customer->mobile) }}" target="_blank"
                           class="link-secondary text-decoration-none">
                            <i class="lab la-telegram-plane fs-4"></i>
                        </a>
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($customer->email)
                        <a href="mailto:{{ $customer->email }}" title="{{ $customer->email }}" class="link-secondary"><i class="las la-envelope fs-4"></i></a>
                        @if($customer->email_verified == 1)
                            <i class="las la-check-circle text-success"></i>
                        @elseif($customer->email_verified == 3)
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($customer->website)
                    <a href="{{ $customer->website }}" title="{{ $customer->website }}" rel="noopener" target="_blank"
                       class="link-secondary text-decoration-none">
                        <i class="las la-globe fs-4"></i>
                    </a>
                    @endif
                </td>
                <td class="text-center d-sm-table-cell link-secondary text-decoration-none"
                    title="{{ (!empty($customer->country)) ? $customer->country->name : '' }}">
                    @if(!empty($customer->country))
                    {{ $customer->country->flag }}
                    @endif
                </td>
                <td class="text-center d-sm-table-cell link-secondary">
                    {{ $customer->province }}
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    @if($customer->facebook)
                    <a href="{{ $customer->facebook }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-facebook-square fs-3"></i>
                    </a>
                    @endif

                    @if($customer->instagram)
                    <a href="{{ $customer->instagram }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-instagram fs-3"></i>
                    </a>
                    @endif

                    @if($customer->linkedin)
                    <a href="{{ $customer->linkedin }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-linkedin fs-3"></i>
                    </a>
                    @endif

                    @if($customer->youtube)
                    <a href="{{ $customer->youtube }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-youtube-square fs-3"></i>
                    </a>
                    @endif

                    @if($customer->twitter)
                    <a href="{{ $customer->twitter }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-twitter-square fs-3"></i>
                    </a>
                    @endif

                    @if($customer->tiktok)
                        <a href="{{ $customer->tiktok }}" rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                            <span class="tiktok"><i class="fa-brands fa-tiktok"></i></span>
                        </a>
                    @endif

                    @if($customer->mobile)
                    <a href="https://api.whatsapp.com/send/?phone={{ $customer->mobile }}&text={{ __('Hello') }}"
                       rel="noopener" target="_blank" class="text-decoration-none link-secondary">
                        <i class="lab la-whatsapp fs-3"></i>
                    </a>
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">{{ ($customer->industry) ? __($customer->industry->name) : '' }}</td>
                <td class="text-center text-nowrap">
                    @isset($customer->seller)
                    {{ $customer->seller->first_name }}
                    @endisset
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    @if(is_array($customer->tags) || is_object($customer->tags))
                        @foreach($customer->tags as $tag)
                            <a href="{{ url("/customer?search=$tag") }}" class="badge {{ $bootstrap_colors[array_rand($bootstrap_colors)] }} text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    @endif
                </td>
                <td class="text-center text-nowrap d-none d-sm-table-cell">
                    <span class="badge {{ App\Helpers\LeadStatus::renderBadge($customer->status) }}">{{ __(ucfirst($customer->status)) }}</span>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    <small class="text-muted">{{ $customer->created_at->format('d/m/y H:i') }}</small>
                </td>
                <td class="text-nowrap d-none d-sm-table-cell">
                    <small class="text-muted">{{ $customer->updated_at->format('d/m/y H:i') }}</small>
                </td>
                <td class="text-nowrap">
                    <a href="{{ url("/customer/update/$customer->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>

                    @can('delete customer')
                    <a onclick="Customer.delete({{ $customer->id }}, '{{ $customer->name }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                    @endcan
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
            let message = `{{ __('Are you sure you want to delete the customer: ') }}${name}?`;
            let res = confirm(message);
            if(res)
                window.location = '{{ url('/customer/delete') }}/'+id;
        }
    };
</script>
@endsection
