<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class OrderPdfController extends Controller
{
    public function download(Request $request, int $id)
    {
        $order = Order::findOrFail($id);
        $data['order'] = $order;
        // Debug
        // return view('order.print', $data);
        $html = view('order.print', $data)->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);

        // Opcional: configurar opciones de Dompdf (margenes, tamaño de papel, etc)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Download PDF
        return $dompdf->stream('order_'.$order->id.'.pdf');
    }
}
