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
        $order = Order::create([]);

        $this->assertDatabaseHas('orders', [
            'status' => 'draft',
        ]);

        
    }
}
