<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_order_is_created_with_draft_status(): void
    {
        $order = new Order();
        $order->customer_id = 1;
        $order->save();

        $this->assertDatabaseHas('orders', [
            'status' => 'draft',
        ]);
    }

    public function test_order_is_created_with_items(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 101,
                    'quantity' => 2
                ],

                [
                    'product_id' => 205,
                    'quantity' => 3
                ],

                [
                    'product_id' => 310,
                    'quantity' => 1
                ]
            ]
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Pedido criado com sucesso!',
            ]);

        $orderId = $response['order_id'];

        $this->assertDatabaseHas('orders', [
            'customer_id' => 1,
            'total' => 530
        ]);

        $this->assertDatabaseCount('order_items', 3);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => 101,
            'quantity' => 2,
            'unit_price' => 150.00
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => 205,
            'quantity' => 3,
            'unit_price' => 50.00
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => 310,
            'quantity' => 1,
            'unit_price' => 80.00
        ]);
    }

    public function test_order_is_not_created_with_unknown_product(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 999,
                    'quantity' => 1
                ]
            ]
        ]);

        $response
            ->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_is_not_created_with_zero_quantity(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 101,
                    'quantity' => 0
                ]
            ]
        ]);

        $response
            ->assertStatus(422);
        
        $this->assertDatabaseCount('orders', 0);

        $this->assertDatabaseCount('order_items', 0);
    }
}
