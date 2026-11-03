<?php

namespace App\Console\Commands;

use App\Jobs\QueueHeartbeat;
use App\Models\Heartbeat;
use App\Signing\Health\GatewayHealth;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:heartbeat')]
#[Description('Señal de vida del scheduler y de cada cola (lo ejecuta el scheduler cada minuto)')]
class RecordHeartbeat extends Command
{
    public function handle(): int
    {
        Heartbeat::beat('scheduler');

        foreach (GatewayHealth::QUEUES as $queue) {
            QueueHeartbeat::dispatch($queue, now()->getTimestamp());
        }

        return self::SUCCESS;
    }
}
