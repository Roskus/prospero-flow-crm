<?php

declare(strict_types=1);

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Http\Requests\OrderSaveRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;

class OrderSaveController extends MainController
{
    public function __construct(private OrderRepository $orderRepository) {}

    public function save(OrderSaveRequest $request)
    {
        if ($request->filled('id')) {
            $invoicedMessage = Order::where('company_id', $request->user()->company_id)
                ->find($request->integer('id'))
                ?->invoicedMessage();

            if ($invoicedMessage !== null) {
                return redirect('order')->with(['status' => 'error', 'message' => $invoicedMessage]);
            }
        }

        $this->orderRepository->save($request->validated());

        return redirect('order');
    }
}
