<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $order = new Order();

        $validated = $request->validated();

        $order->customer_id = $validated['customer_id'];

        $order->save();

        return response()->json([
            $order,
        ], 201);
    }
}
