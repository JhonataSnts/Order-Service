<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {

        $validated = $request->validated();

        $prices = [
            101 => 150.00,
            205 => 50.00,
            310 => 80.00,
        ];

        $total = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $productId = $item['product_id'];
            
            if (!isset($prices[$productId])) {
                abort(422, 'O produto com ID ' . $productId . ' não possui preço cadastrado.');
            } 

            $unitPrice = $prices[$productId];
            $quantity = $item['quantity'];
            $subtotal = $unitPrice * $quantity;
            $total += $subtotal;

            $itemData = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ];

            $orderItems[] = $itemData;
            dump($orderItems);
        }
        
        $savedOrder = DB::transaction(function () use ($request, $validated){
        $order = new Order();
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

