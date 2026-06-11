@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', [
        'title' => $bank_account->account_name ?: $bank_account->bank?->name
    ])

    <div class="row mb-3">
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ __('Type') }}</div>
                    <div class="fw-bold">{{ __(\App\Models\Bank\Account::TYPES[$bank_account->type] ?? $bank_account->type) }}</div>

                    @if($bank_account->bank)
                        <div class="text-muted small mt-2">{{ __('Bank') }}</div>
                        <div>{{ $bank_account->bank->name }}</div>
                    @endif

                    @if($bank_account->country)
                        <div class="text-muted small mt-2">{{ __('Country') }}</div>
                        <div>{{ $bank_account->country->flag }} {{ $bank_account->country->name }}</div>
                    @endif

                    <div class="text-muted small mt-2">{{ __('Currency') }}</div>
                    <div>{{ strtoupper($bank_account->currency) }}</div>

                    @if($bank_account->iban)
                        <div class="text-muted small mt-2">IBAN</div>
                        <div class="font-monospace small">{{ $bank_account->iban }}</div>
                    @endif
                    @if($bank_account->cbu)
                        <div class="text-muted small mt-2">CBU</div>
                        <div class="font-monospace small">{{ $bank_account->cbu }}</div>
                        @if($bank_account->alias)
                            <div class="text-muted small mt-1">Alias: <span class="text-dark">{{ $bank_account->alias }}</span></div>
                        @endif
                    @endif
                    @if($bank_account->sort_code)
                        <div class="text-muted small mt-2">Sort Code</div>
                        <div class="font-monospace small">{{ $bank_account->sort_code }} &nbsp; {{ $bank_account->account_number }}</div>
                    @endif
                    @if($bank_account->swift)
                        <div class="text-muted small mt-2">SWIFT</div>
                        <div class="font-monospace small">{{ $bank_account->swift }}</div>
                    @endif
                    @if($bank_account->account_number && !$bank_account->sort_code)
                        <div class="text-muted small mt-2">{{ __('Account number') }}</div>
                        <div class="font-monospace small">{{ $bank_account->account_number }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="row h-100">
                <div class="col-12 col-md-4">
                    <div class="card border-success h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="text-muted small">{{ __('Income') }}</div>
                            @php
                                $income = $transactions->where('status', 'paid')->where('type', 'income')->sum('amount');
                            @endphp
                            <div class="fs-5 fw-bold text-success">{{ number_format($income, 2) }} {{ strtoupper($bank_account->currency) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-danger h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="text-muted small">{{ __('Expense') }}</div>
                            @php
                                $expense = $transactions->where('status', 'paid')->where('type', 'expense')->sum('amount');
                            @endphp
                            <div class="fs-5 fw-bold text-danger">{{ number_format($expense, 2) }} {{ strtoupper($bank_account->currency) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="text-muted small">{{ __('Balance') }}</div>
                            <div class="fs-4 fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($balance, 2) }} {{ strtoupper($bank_account->currency) }}
                            </div>
                            @if($bank_account->opening_balance)
                                <div class="text-muted small mt-1">
                                    {{ __('Opening balance') }}: {{ number_format($bank_account->opening_balance, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">{{ __('Transactions') }}</h5>
        <a href="{{ url('/bank-account') }}" class="btn btn-secondary btn-sm">
            <i class="las la-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Issue date') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Payment date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="text-nowrap">{{ $tx->issue_date?->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ url('/account/edit/' . $tx->id) }}">{{ $tx->name }}</a>
                                    @if($tx->customer)
                                        <div class="text-muted small">{{ $tx->customer->name }}</div>
                                    @elseif($tx->supplier)
                                        <div class="text-muted small">{{ $tx->supplier->name }}</div>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($tx->type === 'income')
                                        <span class="badge bg-success">{{ __('Income') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Expense') }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap small">{{ $tx->category?->name }}</td>
                                <td class="text-muted small">{{ $tx->reference }}</td>
                                <td class="text-end text-nowrap fw-bold {{ $tx->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type === 'expense' ? '-' : '+' }}{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="text-nowrap">
                                    @php
                                        $statusClass = match($tx->status) {
                                            'paid' => 'bg-success',
                                            'overdue' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ __(ucfirst($tx->status)) }}</span>
                                </td>
                                <td class="text-nowrap">{{ $tx->payment_date?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('No transactions found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Cards section --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h5 class="mb-0">{{ __('Cards') }}</h5>
        <a href="{{ url('/bank-card/create/' . $bank_account->id) }}" class="btn btn-primary btn-sm">
            <i class="las la-plus"></i> {{ __('Add card') }}
        </a>
    </div>

    @if($bank_account->cards->count())
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Network') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Cardholder name') }}</th>
                            <th>{{ __('Number') }}</th>
                            <th>CVV</th>
                            <th>{{ __('Expires') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bank_account->cards as $card)
                            <tr>
                                <td class="text-nowrap">
                                    @switch($card->network)
                                        @case('visa') <span class="badge bg-primary">VISA</span> @break
                                        @case('mastercard') <span class="badge bg-danger">Mastercard</span> @break
                                        @case('amex') <span class="badge bg-success">Amex</span> @break
                                        @default <span class="badge bg-secondary">{{ $card->network }}</span>
                                    @endswitch
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge {{ $card->type === 'credit' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                        {{ __(ucfirst($card->type)) }}
                                    </span>
                                </td>
                                <td>{{ $card->cardholder_name }}</td>
                                <td class="font-monospace text-nowrap">
                                    @can('bank card view number')
                                        <span class="card-number-masked">{{ $card->masked_number }}</span>
                                        <span class="card-number-full d-none">{{ $card->number }}</span>
                                        <button type="button" class="btn btn-xs btn-link p-0 ms-1"
                                                onclick="toggleCardData(this, 'card-number')"
                                                title="{{ __('Show') }}">
                                            <i class="las la-eye"></i>
                                        </button>
                                    @else
                                        {{ $card->masked_number }}
                                    @endcan
                                </td>
                                <td class="font-monospace">
                                    @if($card->cvv)
                                        @can('bank card view cvv')
                                            <span class="card-cvv-masked">***</span>
                                            <span class="card-cvv-full d-none">{{ $card->cvv }}</span>
                                            <button type="button" class="btn btn-xs btn-link p-0 ms-1"
                                                    onclick="toggleCardData(this, 'card-cvv')"
                                                    title="{{ __('Show') }}">
                                                <i class="las la-eye"></i>
                                            </button>
                                        @else
                                            ***
                                        @endcan
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $card->expires_formatted }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ url('/bank-card/edit/' . $card->id) }}"
                                       class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                    <button onclick="BankCard.delete({{ $card->id }})"
                                            class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
const BankCard = {
    delete: function(id) {
        if (confirm('{{ __('Are you sure you want to delete this card?') }}')) {
            fetch(`/bank-card/delete/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => window.location.reload());
        }
    }
};

function toggleCardData(btn, type) {
    const row = btn.closest('td');
    const masked = row.querySelector(`.${type}-masked`);
    const full   = row.querySelector(`.${type}-full`);
    const icon   = btn.querySelector('i');
    const isHidden = full.classList.contains('d-none');

    masked.classList.toggle('d-none', isHidden);
    full.classList.toggle('d-none', !isHidden);
    icon.classList.toggle('la-eye', !isHidden);
    icon.classList.toggle('la-eye-slash', isHidden);
}
</script>
@endpush
