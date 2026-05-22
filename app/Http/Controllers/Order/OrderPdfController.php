<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class OrderPdfController extends MainController
{
    public function download(int $id)
    {
        $order = Order::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();
        $data['order'] = $order;
        // Debug
        // return view('order.print', $data);
        $html = view('order.print', $data)->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);

        // Opcional: configurar opciones de Dompdf (margenes, tamaño de papel, etc)
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="order_'.$order->id.'.pdf"');
    }
}
