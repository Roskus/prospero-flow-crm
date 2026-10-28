@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Invoices')])

<form method="get" action="{{ route('invoice.index') }}" class="mb-2 d-flex flex-wrap gap-2 align-items-center">
    @if($branches->isNotEmpty())
    <label for="branch_id" class="visually-hidden">{{ __('Branch') }}</label>
    <select name="branch_id" id="branch_id" class="form-select w-auto" onchange="this.form.submit()">
        <option value="">{{ __('All branches') }}</option>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" @selected($filters['branch_id'] === $branch->id)>{{ $branch->code }} - {{ $branch->name }}</option>
        @endforeach
    </select>
    @endif
    <label for="status" class="visually-hidden">{{ __('Status') }}</label>
    <select name="status" id="status" class="form-select w-auto" onchange="this.form.submit()">
        <option value="">{{ __('All statuses') }}</option>
        @foreach([\App\Models\Invoice::ISSUED => 'Issued', \App\Models\Invoice::PARTIALLY_PAID => 'Partially paid', \App\Models\Invoice::PAID => 'Paid', 'overdue' => 'Overdue', \App\Models\Invoice::CANCELLED => 'Cancelled'] as $value => $label)
            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ __($label) }}</option>
        @endforeach
    </select>
    <label for="customer_id" class="visually-hidden">{{ __('Customer') }}</label>
    <select name="customer_id" id="customer_id" class="form-select w-auto" onchange="this.form.submit()">
        <option value="">{{ __('All customers') }}</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected($filters['customer_id'] === $customer->id)>{{ $customer->displayName() }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th>{{ __('Number') }}</th>
                @if($branches->isNotEmpty())
                <th>{{ __('Branch') }}</th>
                @endif
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Issue date') }}</th>
                <th>{{ __('Due date') }}</th>
                <th class="text-end">{{ __('Total') }}</th>
                <th class="text-end">{{ __('Balance') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="text-nowrap"><a href="{{ route('invoice.show', $invoice->id) }}">{{ $invoice->fullNumber() }}</a></td>
                @if($branches->isNotEmpty())
                <td class="text-nowrap">{{ $invoice->branch?->name }}</td>
                @endif
                <td>{{ trim(($invoice->customer_data['name'] ?? '').' '.($invoice->customer_data['business_name'] ?? '')) }}</td>
                <td class="text-nowrap">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                <td class="text-nowrap">{{ $invoice->due_date->format('d/m/Y') }}</td>
                <td class="text-end text-nowrap">{{ \App\Helpers\CurrencyHelper::formatIn($invoice->total, $invoice->currency) }}</td>
                <td class="text-end text-nowrap">{{ $invoice->isCancelled() ? '—' : \App\Helpers\CurrencyHelper::formatIn($invoice->balance(), $invoice->currency) }}</td>
                <td class="text-nowrap">
                    <span class="badge bg-{{ $invoice->getStatusBadgeClass() }}">{{ __($invoice->getStatusLabel()) }}</span>
                    @if($invoice->isOverdue())
                        <span class="badge bg-danger">{{ __('Overdue') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted">{{ __('No invoices') }}</td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>
        <div>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
