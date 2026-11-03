<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {

        $savedOrder = DB::transaction(function () use ($request){
        $order = new Order();

        $validated = $request->validated();

        $order->customer_id = $validated['customer_id'];
        $order->status = 'draft';
        $order->save();

        return $order;
    });

        return response()->json([
            'message' => 'Pedido criado com sucesso!',
            'order_id' => $savedOrder->id
        ], 201);
    }
}
