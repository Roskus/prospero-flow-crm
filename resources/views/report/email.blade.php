@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Email report')])

    @php
        $percentage = fn (float $value): string => number_format($value, 1).' %';
    @endphp

    <p class="text-muted small">
        {{ __('Open rate = opens / delivered. Bounce rate = bounced / emails sent.') }}
        {{ __('Opens are an estimate: some mail clients load images by themselves and others block them.') }}
    </p>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0" id="email-report">
                    <thead>
                    <tr>
                        <th>{{ __('Campaign') }}</th>
                        <th>{{ __('Sent at') }}</th>
                        <th class="text-end">{{ __('Emails sent') }}</th>
                        <th class="text-end">{{ __('Delivered') }}</th>
                        <th class="text-end">{{ __('Opens') }}</th>
                        <th class="text-end">{{ __('Bounced') }}</th>
                        <th class="text-end">{{ __('Open rate') }}</th>
                        <th class="text-end">{{ __('Bounce rate') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>
                                @can('update campaign')
                                    <a href="{{ url('/campaign/update/'.$row['campaign']->id) }}">{{ $row['campaign']->name }}</a>
                                @else
                                    {{ $row['campaign']->name }}
                                @endcan
                                <div class="small text-muted">{{ $row['campaign']->subject }}</div>
                            </td>
                            <td>{{ $row['campaign']->send_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">{{ $row['sent'] }}</td>
                            <td class="text-end">{{ $row['delivered'] }}</td>
                            <td class="text-end">{{ $row['opened'] }}</td>
                            <td class="text-end {{ $row['bounced'] > 0 ? 'text-danger' : '' }}">{{ $row['bounced'] }}</td>
                            <td class="text-end">{{ $percentage($row['open_rate']) }}</td>
                            <td class="text-end">{{ $percentage($row['bounce_rate']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">{{ __('No campaigns sent yet') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if(count($rows) > 0)
                        <tfoot>
                        <tr class="fw-semibold">
                            <td colspan="2">{{ __('Total') }}</td>
                            <td class="text-end">{{ $totals['sent'] }}</td>
                            <td class="text-end">{{ $totals['delivered'] }}</td>
                            <td class="text-end">{{ $totals['opened'] }}</td>
                            <td class="text-end">{{ $totals['bounced'] }}</td>
                            <td class="text-end">{{ $percentage($totals['open_rate']) }}</td>
                            <td class="text-end">{{ $percentage($totals['bounce_rate']) }}</td>
                        </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            <p class="text-muted small mt-2 mb-0">{{ __('Campaigns sent before opens and bounces were recorded show only the emails sent.') }}</p>
        </div>
    </div>
@endsection
