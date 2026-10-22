@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Branches'), 'count' => $branches->count()])

    @can('update company')
    <div class="mb-3">
        <a href="{{ url('/branch/create') }}" class="btn btn-primary">
            <i class="las la-plus"></i> {{ __('New branch') }}
        </a>
    </div>
    @endcan

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Address') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>E-mail</th>
                            @can('update company')
                            <th></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td class="font-monospace text-nowrap">{{ $branch->code }}</td>
                                <td class="text-nowrap">
                                    {{ $branch->name }}
                                    @if($branch->is_main)
                                        <span class="badge text-bg-success ms-1">{{ __('Main branch') }}</span>
                                    @endif
                                </td>
                                <td class="small">@include('components.address.display', ['address' => $branch->address])</td>
                                <td class="text-nowrap">{{ $branch->phone }}</td>
                                <td>{{ $branch->email }}</td>
                                @can('update company')
                                <td class="text-nowrap">
                                    <a href="{{ url('/branch/update/'.$branch->id) }}" class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                    @unless($branch->is_main)
                                        <form method="POST" action="{{ url('/branch/main/'.$branch->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-success" title="{{ __('Set as main branch') }}">
                                                <i class="las la-star"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ url('/branch/delete/'.$branch->id) }}" class="d-inline"
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete this branch?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('No branches found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
