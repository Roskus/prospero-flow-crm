@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Banks')])

    <div class="mb-2">
        <a href="{{ url('bank/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="card mb-2">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ request('name') }}"
                           placeholder="{{ __('Search by name') }}" class="form-control">
                </div>
                <div class="col-12 col-md-4">
                    @include('components.country')
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                </div>
                @if(request('name') || request('country_id'))
                    <div class="col-12 col-md-2">
                        <a href="{{ url('bank') }}" class="btn btn-secondary w-100">{{ __('Clear') }}</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Country') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>BIC/SWIFT</th>
                            <th>{{ __('Updated at') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banks as $bank)
                            <tr>
                                <td><a href="/bank/update/{{ $bank->uuid }}">{{ $bank->name }}</a></td>
                                <td class="text-center">
                                    @if(!empty($bank->country)){{ $bank->country->flag }}@endif
                                </td>
                                <td>
                                    @isset($bank->phone)
                                        <a href="tel:{{ $bank->phone }}">{{ $bank->phone }}</a>
                                    @endisset
                                </td>
                                <td>
                                    @isset($bank->email)
                                        <a href="mailto:{{ $bank->email }}">{{ $bank->email }}</a>
                                    @endisset
                                </td>
                                <td>
                                    @isset($bank->website)
                                        <a href="{{ $bank->website }}" rel="noopener" target="_blank">{{ $bank->website }}</a>
                                    @endisset
                                </td>
                                <td>{{ $bank->bic }}</td>
                                <td class="text-nowrap">{{ $bank->updated_at?->format('d/m/Y') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ url('bank/update/' . $bank->uuid) }}" class="btn btn-warning btn-sm">
                                        <i class="las la-pen text-white"></i>
                                    </a>
                                    <a href="{{ url('bank/delete/' . $bank->uuid) }}" class="btn btn-danger btn-sm">
                                        <i class="las la-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('No results found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $banks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
