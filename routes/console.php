<?php

use App\Models\CertificateVaultEntry;
use App\Models\SignatureDocument;
use Illuminate\Support\Facades\Schedule;

/*
| Tareas de mantenimiento. onOneServer() requiere un cache store con locks atómicos
| compartido entre nodos (database o redis).
*/

// Vacía p12 y contraseñas entregados o vencidos, y documentos de Firmas Regulares ya enviados.
Schedule::command('model:prune', ['--model' => [CertificateVaultEntry::class, SignatureDocument::class]])
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping(10);

// Reconciliación con el tracking del proveedor (checklist: consulta de estado cada 10 minutos).
Schedule::command('gateway:reconcile-sessions')
    ->everyTenMinutes()
    ->onOneServer()
    ->withoutOverlapping(10);

// Elimina tokens Sanctum vencidos hace más de 24 h.
Schedule::command('sanctum:prune-expired --hours=24')->daily()->onOneServer();

Schedule::command('queue:prune-batches --hours=48')->daily()->onOneServer();
Schedule::command('queue:prune-failed --hours=720')->daily()->onOneServer();
