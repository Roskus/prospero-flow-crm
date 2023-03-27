<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderPDFService;
use Illuminate\Http\Request;

class OrderPdfController extends Controller
{
    public function download(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        return response((new OrderPDFService())->getPDF($order));
    }
}
