<?php

use App\Models\CertificateVaultEntry;
use Illuminate\Support\Facades\Schedule;

/*
| Tareas de mantenimiento. onOneServer() requiere un cache store con locks atómicos
| compartido entre nodos (database o redis).
*/

// Vacía p12 y contraseñas ya entregados o vencidos (CertificateVaultEntry::prunable()).
Schedule::command('model:prune', ['--model' => [CertificateVaultEntry::class]])
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping(10);

// Elimina tokens Sanctum vencidos hace más de 24 h.
Schedule::command('sanctum:prune-expired --hours=24')->daily()->onOneServer();

Schedule::command('queue:prune-batches --hours=48')->daily()->onOneServer();
Schedule::command('queue:prune-failed --hours=720')->daily()->onOneServer();
