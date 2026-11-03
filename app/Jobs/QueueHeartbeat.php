<?php

namespace App\Jobs;

use App\Models\Heartbeat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Job mínimo que el scheduler encola cada minuto en cada cola: si se procesa, hay un worker
 * atendiendo esa cola. Mide también cuánto esperó en la cola.
 */
class QueueHeartbeat implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public string $queueName, public int $dispatchedAt)
    {
        $this->onQueue($queueName);
    }

    public function handle(): void
    {
        Heartbeat::beat("queue:{$this->queueName}", ['wait_seconds' => max(0, now()->getTimestamp() - $this->dispatchedAt)]);
    }
}
