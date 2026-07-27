<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Http\Requests\OrderSaveRequest;
use App\Repositories\OrderRepository;

class OrderSaveController extends MainController
{
    public function __construct(private OrderRepository $orderRepository) {}

    public function save(OrderSaveRequest $request)
    {
        $order = $this->orderRepository->save($request->validated());

        return redirect('order');
    }
}
