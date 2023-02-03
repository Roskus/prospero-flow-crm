@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Dashboard')])

    <div>
        <div class="row">
            <div class="col-md-6 col-xl-6 mb-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="d-flex d-flex justify-content-between m-0">
                            <a href="{{ url('/lead') }}" class="text-decoration-none text-body">{{ __('Leads') }}</a>
                            <span class="badge rounded-pill text-bg-success">{{ $lead_count }}</span>
                        </h4>
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
                            <th>{{ __('Website') }}</th>
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
                            <td>
                                <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ App\Helpers\LeadStatus::renderBadge($lead->status->value) }}">{{ $lead->status }}</span>
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

        <!--widget.calendar-->
        <div class="col-md-6 col-xl-6 mb-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="d-flex d-flex justify-content-between m-0">
                        <a href="{{ url('/calendar') }}" class="text-decoration-none text-body">{{ __('Calendar') }}</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col text-center border">{{ __('calendar.Monday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Tuesday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Wednesday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Thursday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Friday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Saturday') }}</div>
                        <div class="col text-center border">{{ __('calendar.Sunday') }}</div>
                    </div>
                    <div class="row" style="height: 14vh;">

                        @while ($startOfCalendar <= $endOfCalendar)
                            <div class="col text-center border p-1" onclick="Calendar.scheduleEvent('{{ $startOfCalendar->toDateString() }}')">
                                <span class="mb-1 @if($startOfCalendar->isToday()) badge rounded-pill text-bg-primary @endif">{{ $startOfCalendar->format('j') }}</span>
                                <div class="d-flex flex-column">
                                    @foreach ( $events->whereBetween('start_date', [$startOfCalendar->copy()->startOfDay(), $startOfCalendar->copy()->endOfDay()]) as $event)
                                        <span role="button" class="badge text-bg-secondary mb-1 text-wrap" onclick="Calendar.read('{{ $event->id }}')">{{ $event->title }}</span>
                                    @endforeach
                                </div>
                            </div>

                            @if ($startOfCalendar->isoWeekday() == 7 && $startOfCalendar->copy()->addDay() <= $endOfCalendar)
                    </div>
                    <div class="row" style="height: 14vh;">
                        @endif

                        @php
                            $startOfCalendar->addDay();
                        @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
        <!--widget.calendar-->
        </div>
    </div>

@endsection
