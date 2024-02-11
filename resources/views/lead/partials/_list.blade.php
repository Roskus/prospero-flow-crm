<div class="card mb-3">
    <div class="card-header">
        <h4 class="d-flex d-flex justify-content-between m-0">
            <a href="{{ $url }}" class="text-decoration-none text-body">{{ $title }}</a>
            <span class="badge rounded-pill text-bg-success">{{ $leads->count() }}</span>
        </h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        @if(!empty($leads))
            <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Business name') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Mobile') }}</th>
                <th>Email</th>
                <th>{{ __('Website') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Created at') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($leads as $lead)
            <tr>
                <td>
                    <a href="{{ $url }}/update/{{ $lead->id }}">{{ $lead->name }}</a>
                </td>
                <td>{{ $lead->business_name }}</td>
                <td class="text-nowrap text-center">
                    @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" title="{{ $lead->phone }}" target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-phone fs-4"></i>
                        </a>
                        @if($lead->phone_verified)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($lead->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}"
                           title="{{ $lead->mobile }}" target="_blank" class="link-secondary text-decoration-none">
                            <i class="las la-mobile fs-4"></i>
                        </a>
                        @if($lead->mobile_verified)
                            <i class="las la-check-circle text-success"></i>
                        @else
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    @endif
                </td>
                <td class="text-nowrap text-center">
                    @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" title="{{ $lead->email }}" class="link-secondary text-decoration-none">
                            <i class="las la-envelope fs-4"></i>
                        </a>
                        @if($lead->email_verified)
                            <i class="las la-check-circle text-success"></i>
                        @elseif($lead->email_verified == 3)
                            <i class="las la-times-circle text-danger"></i>
                        @endif
                    @endif
                </td>
                <td class="text-center">
                    @isset($lead->website)
                    <a href="{{ $lead->website }}" title="{{ $lead->website }}" rel="noopener" target="_blank" class="link-secondary text-decoration-none">
                        <i class="las la-globe fs-4"></i>
                    </a>
                    @endisset
                </td>
                <td class="text-center">
                    <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status) }}">
                        {{ __(ucfirst($lead->status)) }}
                    </span>
                </td>
                <td>
                    <small class="text-muted">{{ $lead->created_at->format('d/m/Y H:i') }}</small>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
        </div>
    </div>
    <!--./card-body-->
</div>
<!--./card-->
