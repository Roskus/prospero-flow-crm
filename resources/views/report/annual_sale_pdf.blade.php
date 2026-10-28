<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 12mm; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .layout-table td { vertical-align: top; padding: 4px 0; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .report-title { font-size: 20px; font-weight: bold; margin-top: 6px; }
        .info-label { font-weight: bold; }
        .divider { border-top: 1px solid #ddd; margin: 10px 0; }
        .table th { background: #f0f0f0; border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        .table td { border: 1px solid #ccc; padding: 4px 6px; }
        .text-end { text-align: right; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .text-muted { color: #777; }
        h3 { font-size: 12px; margin: 10px 0 4px; }
        .logo { max-width: 200px; max-height: 80px; }
    </style>
</head>
<body>

@php
    $company = Auth::user()->company;
    $logo = $company->logoDataUri();
    $money = fn (float $value): string => \App\Helpers\CurrencyHelper::format($value);
@endphp

<table class="layout-table">
    <tr>
        <td width="60%">
            @if($logo)
                <img src="{{ $logo }}" class="logo" alt="{{ $company->name }}">
            @else
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="report-title">{{ __('Annual sales report') }} {{ $year }}</div>
        </td>
        <td width="40%" class="text-end">
            <div><span class="info-label">{{ __('Sales :year', ['year' => $year]) }}:</span> {{ $money($report['total']) }}</div>
            <div>
                <span class="info-label">{{ __(':year until :date', ['year' => $year - 1, 'date' => $report['previous_until']->format('d/m/Y')]) }}:</span>
                {{ $money($report['previous_total']) }}
                @if($report['change'] !== null)
                    ({{ $report['change'] > 0 ? '+' : '' }}{{ $report['change'] }}%)
                @endif
            </div>
            <div>
                <span class="info-label">{{ __('Seller') }}:</span>
                {{ $seller ? trim($seller->first_name.' '.$seller->last_name) : __('All') }}
            </div>
        </td>
    </tr>
</table>

<div class="divider"></div>

@include('report.partial._annual_sale_tables', ['report' => $report, 'year' => $year])

</body>
</html>
