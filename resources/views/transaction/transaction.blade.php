@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => $transaction->id ? __('Edit transaction') . ' #' . $transaction->id : __('New transaction')])

    <form method="POST" action="{{ url('/transaction/save') }}" class="form" enctype="multipart/form-data">
        @csrf
        @if($transaction->id)
            <input type="hidden" name="id" value="{{ $transaction->id }}">
        @endif

        <div class="card mt-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $transaction->name) }}"
                               required maxlength="80" class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" required class="form-select form-select-lg">
                            <option value="">{{ __('Choose') }}</option>
                            <option value="income" @selected(old('type', $transaction->type) === 'income')>{{ __('Income') }}</option>
                            <option value="expense" @selected(old('type', $transaction->type) === 'expense')>{{ __('Expense') }}</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select name="status" id="status" required class="form-select form-select-lg">
                            <option value="pending" @selected(old('status', $transaction->status ?? 'pending') === 'pending')>{{ __('Pending') }}</option>
                            <option value="paid" @selected(old('status', $transaction->status) === 'paid')>{{ __('Paid') }}</option>
                            <option value="overdue" @selected(old('status', $transaction->status) === 'overdue')>{{ __('Overdue') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-3">
                        <label for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" id="amount" step="0.01"
                                   value="{{ old('amount', $transaction->amount) }}"
                                   required class="form-control form-control-lg">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="transaction_category_id">{{ __('Category') }}</label>
                        <select name="transaction_category_id" id="transaction_category_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('transaction_category_id', $transaction->transaction_category_id) == $category->id)>
                                    {{ __($category->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="bank_account_id">{{ __('Bank account') }}</label>
                        <select name="bank_account_id" id="bank_account_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($bank_accounts as $ba)
                                <option value="{{ $ba->id }}" @selected(old('bank_account_id', $transaction->bank_account_id) == $ba->id)>
                                    {{ $ba->account_name ?: $ba->bank?->name }} ({{ strtoupper($ba->currency) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="bank_card_id">{{ __('Card') }}</label>
                        <select name="bank_card_id" id="bank_card_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($bank_cards as $card)
                                <option value="{{ $card->id }}"
                                        data-bank-account="{{ $card->bank_account_id }}"
                                        @selected(old('bank_card_id', $transaction->bank_card_id) == $card->id)>
                                    @switch($card->network)
                                        @case('visa') VISA @break
                                        @case('mastercard') MC @break
                                        @case('amex') AMEX @break
                                        @default {{ strtoupper($card->network) }}
                                    @endswitch
                                    **** {{ $card->last_four }}
                                    · {{ $card->cardholder_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="reference">{{ __('Reference') }}</label>
                        <input type="text" name="reference" id="reference" maxlength="80"
                               value="{{ old('reference', $transaction->reference) }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="issue_date">{{ __('Issue date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" id="issue_date"
                               value="{{ old('issue_date', $transaction->issue_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                               required class="form-control form-control-lg">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-3">
                        <label for="due_date">{{ __('Due date') }}</label>
                        <input type="date" name="due_date" id="due_date"
                               value="{{ old('due_date', $transaction->due_date?->format('Y-m-d')) }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="payment_date">{{ __('Payment date') }}</label>
                        <input type="date" name="payment_date" id="payment_date"
                               value="{{ old('payment_date', $transaction->payment_date?->format('Y-m-d')) }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="customer_id">{{ __('Customer') }}</label>
                        <select name="customer_id" id="customer_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', $transaction->customer_id) == $customer->id)>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="supplier_id">{{ __('Supplier') }}</label>
                        <select name="supplier_id" id="supplier_id" class="form-select form-select-lg">
                            <option value=""></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $transaction->supplier_id) == $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-8">
                        <label for="notes">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control form-control-lg">{{ old('notes', $transaction->notes) }}</textarea>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="attachment">{{ __('Attachment') }}</label>
                        @if($transaction->attachment)
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <a href="{{ Storage::url($transaction->attachment) }}"
                                   target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="las la-file-alt"></i>
                                    {{ basename($transaction->attachment) }}
                                </a>
                                <form method="POST" action="{{ url('/transaction/attachment/' . $transaction->id) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('{{ __('Remove attachment?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Remove') }}">
                                        <i class="las la-times"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                        <input type="file" name="attachment" id="attachment"
                               accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx"
                               class="form-control form-control-lg">
                        <div class="text-muted small mt-1">PDF, {{ __('images') }}, Word, Excel · {{ __('max') }} 10MB</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg">{{ __('Save') }}</button>
            <a href="{{ url('/transactions') }}" class="btn btn-secondary btn-lg">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.getElementById('bank_card_id').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const bankAccountId = selected.dataset.bankAccount;
    if (bankAccountId) {
        document.getElementById('bank_account_id').value = bankAccountId;
    }
});
</script>
@endpush
