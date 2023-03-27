<?php

declare(strict_types=1);

namespace App\Services;

use Cezpdf;

class OrderPDFService
{
    public function getPDF($order): void
    {
        $pdf = new Cezpdf('A4', 'portrait');

        $pdf->ezSetMargins(80, 300, 80, 80);

        $mainFont = 'Times-Roman';
        $pdf->selectFont($mainFont);

        $pdf->openHere('Fit');

        $pdf->ezText(__('PEDIDO').' número: '.$order->id, 18);

        $pdf->ezSetY(600);

        $pdf->rectangle(80, 620, 200, 100);
        $pdf->rectangle(300, 620, 200, 100);

        $cols = [
            'sku' => 'SKU',
            'name' => __('Product'),
            'price' => __('Price'),
            'tax' => __('Tax'),
            'quantity' => __('Quantity'),
        ];

        $items = $order->items->map(function ($item) {
            return [
                'sku' => $item['product']['sku'],
                'name' => $item['product']['name'],
                'price' => $item['product']['price'],
                'tax' => $item['product']['tax'],
                'quantity' => $item['quantity'],
            ];
        })->toArray();

        $pdf->ezTable($items, $cols);

        $pdf->ezStream(['compress' => 0]);
    }
}
