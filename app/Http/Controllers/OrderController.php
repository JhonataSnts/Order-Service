<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\SendOrderNotification;
use App\Services\CreateOrderService;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CreateOrderService $createOrderService)
    {
        $validated = $request->validated();

        $savedOrder = $createOrderService->create($validated);

        SendOrderNotification::dispatch($savedOrder);

        return response()->json([
            'message' => 'Pedido criado com sucesso!',
            'order_id' => $savedOrder->id
        ], 201);
    }
}

