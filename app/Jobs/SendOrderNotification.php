<?php

namespace App\Jobs;

use App\Models\SentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public SentNotification $notification)
    {
        
    }

    public int $tries = 3;

    public array $backoff = [10, 30];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->notification->status === 'sent') {
            
            return; 
        }
        
        Log::info('Notificação do pedido processada com sucesso.' . $this->notification->id);
    }
}
