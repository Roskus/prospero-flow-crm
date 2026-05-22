<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderSaveController extends MainController
{
    public function __construct(private OrderRepository $orderRepository, Request $request)
    {
        parent::__construct($request);
    }

    public function save(Request $request)
    {
        $order = $this->orderRepository->save($request->all());

        return redirect('order');
    }
}
