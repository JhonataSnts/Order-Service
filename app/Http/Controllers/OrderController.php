<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\CreateOrderService;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CreateOrderService $createOrderService)
    {
        $validated = $request->validated();

        $savedOrder = $createOrderService->create($validated);

        return response()->json([
            'message' => 'Pedido criado com sucesso!',
            'order_id' => $savedOrder->id
        ], 201);
    }
}

