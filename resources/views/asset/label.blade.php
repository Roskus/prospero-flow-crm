<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Label') }} · {{ $asset->code }}</title>
    {{-- 62 × 29 mm: the most common size of thermal label rolls --}}
    <style>
        @page { size: 62mm 29mm; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #000; }
        .label { width: 62mm; height: 29mm; padding: 1.5mm 2mm; overflow: hidden; display: flex; gap: 2mm; align-items: center; }
        .label--barcode { flex-direction: column; justify-content: center; gap: .8mm; }
        .label--barcode .symbol svg { width: 58mm; height: 13mm; display: block; }
        .label--qr { padding: 5mm; }
        .label--qr .symbol svg { width: 19mm; height: 19mm; display: block; }
        .text { min-width: 0; text-align: center; }
        .label--qr .text { text-align: left; }
        .code { font-family: "Courier New", monospace; font-weight: bold; font-size: 11pt; letter-spacing: .5pt; }
        .name, .company { font-size: 7pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 58mm; }
        .label--qr .name, .label--qr .company { max-width: 31mm; white-space: normal; }
        .company { color: #444; }
        @media screen {
            body { background: #eee; padding: 20px; }
            .label { background: #fff; box-shadow: 0 0 4px rgba(0, 0, 0, .3); }
            .actions { margin-top: 12px; }
        }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="label label--{{ $format }}">
        <div class="symbol">{!! $symbol !!}</div>
        <div class="text">
            <div class="code">{{ $asset->code }}</div>
            <div class="name">{{ $asset->name }}</div>
            <div class="company">{{ $asset->company?->name }}</div>
        </div>
    </div>
    <div class="actions">
        <button type="button" onclick="window.print()">{{ __('Print') }}</button>
    </div>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
