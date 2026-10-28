<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }} {{ $invoice->fullNumber() }}</title>
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 10mm; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        td, th { padding: 4px 6px; }
        .table-bordered th, .table-bordered td { border: 1px solid #ccc; }
        .table-bordered thead th { background: #f0f0f0; text-align: left; }
        .invoice-items tfoot th { background: #f7f7f7; }
        h2 { font-size: 18px; }
        .section-title { font-size: 13px; font-weight: bold; margin: 10px 0 4px; }
    </style>
</head>
<body>

@include('invoice.partial._invoice', ['invoice' => $invoice])

@if($bankAccounts->isNotEmpty())
    <div class="section-title">{{ __('Bank transfer details') }}</div>
    <table class="table-bordered">
        @foreach($bankAccounts as $account)
            <tr>
                <td>
                    <div><strong>{{ __('Holder') }}:</strong> {{ $invoice->issuer_data['company']['business_name'] ?: ($invoice->issuer_data['company']['name'] ?? '') }}</div>
                    @if($account->bank)<div><strong>{{ __('Bank') }}:</strong> {{ $account->bank->name }}</div>@endif
                    @if($account->iban)
                        <div><strong>IBAN:</strong> {{ $account->iban }}</div>
                    @elseif($account->account_number)
                        <div><strong>{{ __('Account number') }}:</strong> {{ $account->account_number }}</div>
                    @endif
                    @if($account->swift)<div><strong>SWIFT:</strong> {{ $account->swift }}</div>@endif
                    @if($account->cbu)<div><strong>CBU:</strong> {{ $account->cbu }}</div>@endif
                    @if($account->alias)<div><strong>{{ __('Alias') }}:</strong> {{ $account->alias }}</div>@endif
                </td>
            </tr>
        @endforeach
    </table>
@endif

</body>
</html>
