@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Carriers'), 'count' => $carriers->count()])

    @can('update asset')
    <div class="mb-3">
        <a href="{{ url('/carrier/create') }}" class="btn btn-primary">
            <i class="las la-plus"></i> {{ __('New carrier') }}
        </a>
    </div>
    @endcan

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Country') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Website') }}</th>
                            <th>{{ __('Devices') }}</th>
                            @canany(['update asset', 'delete asset'])
                            <th></th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carriers as $carrier)
                            <tr>
                                <td>{{ $carrier->name }}</td>
                                <td class="text-nowrap">@if($carrier->country){{ $carrier->country->flag }} {{ $carrier->country->name }}@endif</td>
                                <td class="text-nowrap">{{ $carrier->phone }}</td>
                                <td>
                                    @if($carrier->website)
                                        <a href="{{ $carrier->website }}" target="_blank" rel="noopener noreferrer">{{ $carrier->website }}</a>
                                    @endif
                                </td>
                                <td>{{ $carrier->devices_count }}</td>
                                @canany(['update asset', 'delete asset'])
                                <td class="text-nowrap">
                                    @can('update asset')
                                    <a href="{{ url('/carrier/update/'.$carrier->id) }}" class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                    @endcan
                                    @can('delete asset')
                                    <form method="POST" action="{{ url('/carrier/delete/'.$carrier->id) }}" class="d-inline"
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this carrier?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('No carriers found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
