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
            <tbody>
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Business name') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Mobile') }}</th>
                        <th>E-mail</th>
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
                    <td>
                        @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" target="_blank">{{ $lead->phone }}</a>
                        @endif
                    </td>
                    <td>
                        @if($lead->mobile)
                        <a href="https://api.whatsapp.com/send/?phone={{ $lead->mobile }}&text={{ __('Hello') }}" target="_blank">{{ $lead->mobile }}</a>
                        @endif
                    </td>
                    <td>
                        @if($lead->email)
                        <a href="mailto:{{ $lead->email }}" title="{{ $lead->email }}">
                            <i class="las la-envelope fs-4"></i>
                        </a>
                        @endif
                    </td>
                    <td class="text-center">
                        @isset($lead->website)
                        <a href="{{ $lead->website }}" title="{{ $lead->website }}" target="_blank">
                            <i class="las la-globe fs-4"></i>
                        </a>
                        @endisset
                    </td>
                    <td class="text-center">
                        <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status) }}">
                            {{ __(ucfirst($lead->status)) }}
                        </span>
                    </td>
                    <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
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
