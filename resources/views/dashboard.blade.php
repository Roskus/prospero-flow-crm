@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Dashboard') }}</h1>
    </header>
    <div>
        <div class="row">
            <!-- Lead Count -->
            <div class="col-md-6 col-xl-4 mb-2">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ url('/lead') }}" class="">{{ __('Leads') }}</a>
                    </div>
                    <div class="card-body">

                        <h3>{{ $lead_count }}</h3>
                    </div>
                </div><!--./card-->
            </div>
            <!-- Lead Count -->
            <!-- Last Leads -->
            <div class="col-md-6 col-xl-4 mb-2">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ url('/lead') }}" class="">{{ __('Lastest leads') }}</a>
                    </div>
                    <div class="card-body table-responsive">
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
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created at') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leads as $lead)
                        <tr>
                            <td>
                                <a href="{{ url("/lead/update/$lead->id") }}">{{ $lead->name }}</a>
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
                                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status) }}">{{ $lead->status }}</span>
                            </td>
                            <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                        </table>
                    @endif
                    </div><!--./card-body-->
                </div><!--./card-->
            </div>
            <!-- Last Leads -->
        </div>
    </div>

@endsection
