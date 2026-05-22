<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class OrderPdfController extends MainController
{
    public function download(int $order_number)
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->where('order_number', $order_number)
            ->firstOrFail();
        $data['order'] = $order;
        $html = view('order.print', $data)->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="order_'.$order->order_number.'.pdf"');
    }
}
