<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateOrderService
{
    public function create(array $validated): Order
    {
        $total = 0;
        $orderItems = [];

        $prices = [
            101 => 150.00,
            205 => 50.00,
            310 => 80.00,
        ];

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
        }
        
        $savedOrder = DB::transaction(function () use ($validated, $total, $orderItems){
        $order = new Order();
        $order->customer_id = $validated['customer_id'];
        $order->status = 'draft';
        $order->total = $total;
        $order->save();

        $order->items()->createMany($orderItems);
        
        return $order;

        });

        return $savedOrder;
    }
}
