<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        
    }

    public int $tries = 3;

    public array $backoff = [10, 30];

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        

        Log::info('Notificação do pedido processada com sucesso.' . $this->order->id);
    }
}
