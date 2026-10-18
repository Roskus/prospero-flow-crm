<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 12mm; }
        table { width: 100%; border-collapse: collapse; }
        .layout-table td { vertical-align: top; padding: 4px 0; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .report-title { font-size: 20px; font-weight: bold; margin-top: 6px; }
        .info-label { font-weight: bold; }
        .muted { color: #777; }
        .text-right { text-align: right; }
        .divider { border-top: 1px solid #ddd; margin: 12px 0; }
        .kpi-table th { background: #f0f0f0; border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        .kpi-table td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: middle; }
        .kpi-value { font-size: 13px; font-weight: bold; }
        .positive { color: #198754; }
        .negative { color: #dc3545; }
        .bar-track { background: #eee; height: 6px; width: 100%; margin: 2px 0; }
        .bar-current { background: #0d6efd; height: 6px; }
        .bar-previous { background: #6c757d; height: 6px; }
        .legend-swatch { display: inline-block; width: 10px; height: 10px; vertical-align: middle; }
        .logo { max-width: 200px; max-height: 80px; }
        .footer { margin-top: 16px; font-size: 9px; }
    </style>
</head>
<body>

@php
    $company = Auth::user()->company;
    $logoPath = storage_path('app/public/company/'.Illuminate\Support\Str::slug($company->name, '_').'/'.$company->logo);
@endphp

<table class="layout-table">
    <tr>
        <td width="60%">
            @if(!empty($company->logo) && file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" class="logo" alt="{{ $company->name }}">
            @else
                <div class="company-name">{{ $company->name }}</div>
            @endif
            <div class="report-title">{{ __('Sales report') }}</div>
        </td>
        <td width="40%" class="text-right">
            <div><span class="info-label">{{ __('From') }}:</span> {{ $from->format('d/m/Y') }}</div>
            <div><span class="info-label">{{ __('To') }}:</span> {{ $to->format('d/m/Y') }}</div>
            <div>
                <span class="info-label">{{ __('Seller') }}:</span>
                {{ $seller ? trim($seller->first_name.' '.$seller->last_name) : __('All') }}
            </div>
        </td>
    </tr>
</table>

<div class="divider"></div>

<p class="muted">
    {{ __('Compared with the previous period') }}:
    {{ $report['previous_from']->format('d/m/Y') }} - {{ $report['previous_to']->format('d/m/Y') }}
</p>

<table class="kpi-table" style="margin-top: 8px">
    <thead>
        <tr>
            <th width="28%">{{ __('Indicator') }}</th>
            <th width="20%" class="text-right">{{ __('Current period') }}</th>
            <th width="20%" class="text-right">{{ __('Previous period') }}</th>
            <th width="12%" class="text-right">{{ __('Change') }}</th>
            <th width="20%"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($kpis as $key => $kpi)
            @php
                $current = $report['current'][$key];
                $previous = $report['previous'][$key];
                $change = $report['change'][$key];
                $max = max($current, $previous);
                $currentWidth = $max > 0 ? round($current / $max * 100) : 0;
                $previousWidth = $max > 0 ? round($previous / $max * 100) : 0;
                $isImprovement = $change !== null && ($kpi['higher_is_better'] ? $change >= 0 : $change <= 0);
                $format = fn ($value) => $kpi['currency'] ? \App\Helpers\CurrencyHelper::format((float) $value) : $value;
            @endphp
            <tr>
                <td>{{ $kpi['label'] }}</td>
                <td class="text-right kpi-value">{{ $format($current) }}</td>
                <td class="text-right">{{ $format($previous) }}</td>
                <td class="text-right {{ $change === null ? 'muted' : ($isImprovement ? 'positive' : 'negative') }}">
                    {{ $change === null ? '-' : ($change > 0 ? '+' : '').$change.'%' }}
                </td>
                <td>
                    <div class="bar-track"><div class="bar-current" style="width: {{ $currentWidth }}%"></div></div>
                    <div class="bar-track"><div class="bar-previous" style="width: {{ $previousWidth }}%"></div></div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="muted" style="margin-top: 6px">
    <span class="legend-swatch" style="background: #0d6efd"></span> {{ __('Current period') }}
    &nbsp;&nbsp;
    <span class="legend-swatch" style="background: #6c757d"></span> {{ __('Previous period') }}
</p>

<div class="footer muted">
    <p>{{ __('Sales include confirmed and completed orders.') }}</p>
    <p>{{ __('Generated on') }} {{ now()->format('d/m/Y H:i') }} · {{ config('app.name') }}</p>
</div>

</body>
</html>
