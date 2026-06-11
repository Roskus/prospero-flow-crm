@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Bank accounts')])

    <div class="mb-3">
        <a href="{{ url('/bank-account/create') }}" class="btn btn-primary">
            <i class="las la-plus"></i> {{ __('New bank account') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Bank / Account') }}</th>
                            <th>{{ __('Currency') }}</th>
                            <th>IBAN / CBU / ID</th>
                            <th>SWIFT / Sort Code</th>
                            <th>Alias</th>
                            <th class="text-end">{{ __('Balance') }}</th>
                            <th>{{ __('Notes') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bank_accounts as $account)
                            <tr>
                                <td class="text-nowrap">
                                    <span class="badge bg-secondary">{{ __($account->type) }}</span>
                                </td>
                                <td class="text-nowrap">
                                    @if($account->account_name)
                                        <strong>{{ $account->account_name }}</strong><br>
                                    @endif
                                    @if($account->bank)
                                        <span class="text-muted small">{{ $account->bank->name }}</span>
                                    @endif
                                    @if($account->country)
                                        <span title="{{ $account->country->name }}">{{ $account->country->flag }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $account->currency }}</td>
                                <td class="font-monospace small text-nowrap">
                                    {{ $account->iban ?: ($account->cbu ?: $account->account_number) }}
                                </td>
                                <td class="font-monospace small text-nowrap">
                                    {{ $account->swift ?: $account->sort_code }}
                                </td>
                                <td class="small">
                                    {{ $account->alias }}
                                    @if($account->bizum)
                                        <div class="text-muted">Bizum: {{ $account->bizum }}</div>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap fw-bold {{ $account->balance() >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($account->balance(), 2) }} {{ strtoupper($account->currency) }}
                                </td>
                                <td class="text-muted small">{{ $account->notes }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ url('/bank-account/show/' . $account->id) }}"
                                       class="btn btn-xs btn-info" title="{{ __('Show') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a href="{{ url('/bank-account/update/' . $account->id) }}"
                                       class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ url('/bank-account/delete/' . $account->id) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('{{ __('Are you sure you want to delete this account?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">{{ __('No bank accounts found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
