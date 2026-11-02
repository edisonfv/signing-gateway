<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Signing\EmissionGuard;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:halt {--off : Reanuda la emisión} {--status : Solo muestra el estado} {--reason= : Motivo, queda en la auditoría}')]
#[Description('Botón de pánico: detiene toda llamada al proveedor de todos los afiliados (la consulta de estado sigue)')]
class HaltEmission extends Command
{
    public function handle(EmissionGuard $guard): int
    {
        if ($this->option('status')) {
            $details = $guard->haltDetails();
            $details === null
                ? $this->components->info('Emisión activa.')
                : $this->components->warn("Emisión DETENIDA desde {$details['at']}: {$details['reason']}");

            return self::SUCCESS;
        }

        if ($this->option('off')) {
            $guard->resume();
            AuditLog::record('emission.resumed', ip: 'cli');
            $this->components->info('Emisión reanudada.');

            return self::SUCCESS;
        }

        $reason = (string) ($this->option('reason') ?: 'Detenida por el operador');
        $guard->halt($reason);
        AuditLog::record('emission.halted', context: ['reason' => $reason], ip: 'cli');
        $this->components->warn('Emisión DETENIDA: no se crean solicitudes y los jobs en cola cancelan las suyas sin llamar al proveedor. Reanudar con gateway:halt --off.');

        return self::SUCCESS;
    }
}
