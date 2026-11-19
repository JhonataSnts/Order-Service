<?php

namespace App\Services;

use App\Jobs\SendOrderNotification;
use App\Models\Order;
use Illuminate\Queue\DatabaseQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

class CreateOrderService
{
    private const MAX_TOTAL_CENTS = 9_999_999_999;

    private const PRICES_IN_CENTS = [
        101 => 15_000,
        205 => 5_000,
        310 => 8_000,
    ];

    public function create(array $validated): Order
    {
        $totalCents = 0;
        $orderItems = [];

        foreach ($validated['items'] as $index => $item) {
            $productId = $item['product_id'];
            $unitPriceCents = self::PRICES_IN_CENTS[$productId] ?? null;

            if ($unitPriceCents === null) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_id" => "O produto com ID {$productId} não possui preço cadastrado.",
                ]);
            }

            $quantity = $item['quantity'];
            if ($quantity > intdiv(self::MAX_TOTAL_CENTS - $totalCents, $unitPriceCents)) {
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => 'O total do pedido deve ser de até 99.999.999,99.',
                ]);
            }

            $totalCents += $unitPriceCents * $quantity;
            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPriceCents / 100,
            ];
        }

        return DB::transaction(function () use ($validated, $totalCents, $orderItems): Order {
            $order = new Order;
            $order->customer_id = $validated['customer_id'];
            $order->status = 'draft';
            $order->total = $totalCents / 100;
            $order->save();

            $order->items()->createMany($orderItems);
            $notification = $order->notification()->create(['status' => 'pending']);

            $queue = Queue::connection();
            $usesOrderDatabase = $queue instanceof DatabaseQueue && $queue->getDatabase() === $order->getConnection();

            $job = new SendOrderNotification($notification);
            dispatch($usesOrderDatabase ? $job->beforeCommit() : $job->afterCommit());

            return $order;
        });
    }
}
