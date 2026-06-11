@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Accounting')])

    <div class="row mb-3">
        <div class="col-12 col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="text-muted small">{{ __('Income') }}</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($totalIncome, 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <div class="text-muted small">{{ __('Expense') }}</div>
                    <div class="fs-4 fw-bold text-danger">{{ number_format($totalExpense, 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted small">{{ __('Balance') }}</div>
                    @php $balance = $totalIncome - $totalExpense; @endphp
                    <div class="fs-4 fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($balance, 2) }} €
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <a href="{{ url('/transaction/create') }}" class="btn btn-primary">
            <i class="las la-plus"></i> {{ __('New transaction') }}
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
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Due date') }}</th>
                            <th>{{ __('Payment date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="text-nowrap">{{ $transaction->issue_date?->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ url('/transaction/edit/' . $transaction->id) }}">{{ $transaction->name }}</a>
                                    @if($transaction->attachment)
                                        <a href="{{ Storage::url($transaction->attachment) }}" target="_blank"
                                           class="ms-1 text-muted" title="{{ __('Attachment') }}">
                                            <i class="las la-paperclip"></i>
                                        </a>
                                    @endif
                                    @if($transaction->customer)
                                        <div class="text-muted small">{{ $transaction->customer->name }}</div>
                                    @elseif($transaction->supplier)
                                        <div class="text-muted small">{{ $transaction->supplier->name }}</div>
                                    @endif
                                    @if($transaction->bankCard)
                                        <div class="text-muted small">
                                            <i class="las la-university"></i>
                                            {{ $transaction->bankCard->bankAccount?->account_name ?: $transaction->bankCard->bankAccount?->bank?->name }}
                                            &nbsp;<i class="las la-credit-card"></i> {{ $transaction->bankCard->last_four }}
                                        </div>
                                    @elseif($transaction->bankAccount)
                                        <div class="text-muted small">
                                            <i class="las la-university"></i>
                                            {{ $transaction->bankAccount->account_name ?: $transaction->bankAccount->bank?->name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($transaction->type === 'income')
                                        <span class="badge bg-success">{{ __('Income') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Expense') }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $transaction->category ? __($transaction->category->name) : '' }}</td>
                                <td class="text-nowrap text-muted small">{{ $transaction->reference }}</td>
                                <td class="text-nowrap text-end fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'expense' ? '-' : '' }}{{ number_format($transaction->amount, 2) }} €
                                </td>
                                <td class="text-nowrap">{{ $transaction->due_date?->format('d/m/Y') }}</td>
                                <td class="text-nowrap">{{ $transaction->payment_date?->format('d/m/Y') }}</td>
                                <td class="text-nowrap">
                                    @php
                                        $statusClass = match($transaction->status) {
                                            'paid' => 'bg-success',
                                            'overdue' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ __(ucfirst($transaction->status)) }}</span>
                                </td>
                                <td class="text-nowrap text-center">
                                    <a href="{{ url('/transaction/edit/' . $transaction->id) }}"
                                       class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                    <a onclick="Transaction.delete({{ $transaction->id }}, '{{ addslashes($transaction->name) }}')"
                                       class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">{{ __('No transactions found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const Transaction = {
    delete: function(id, name) {
        if (confirm(`{{ __('Are you sure you want to delete') }} "${name}"?`)) {
            fetch(`/transaction/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            }).then(() => window.location.reload());
        }
    }
};
</script>
@endpush
