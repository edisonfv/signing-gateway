<?php

namespace App\Console\Commands;

use App\Actions\SuspendTenant as SuspendTenantAction;
use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:tenant:suspend {tenant : external_id del afiliado} {--block : Incidente: revoca todas sus credenciales y no puede retirar nada} {--resume : Reactiva al afiliado} {--reason= : Motivo, queda en la auditoría}')]
#[Description('Suspende, bloquea o reactiva a un afiliado de inmediato')]
class SuspendTenant extends Command
{
    public function handle(SuspendTenantAction $action): int
    {
        $tenant = Tenant::query()->where('external_id', $this->argument('tenant'))->firstOrFail();

        if ($this->option('resume')) {
            $wasBlocked = $tenant->isBlocked();
            $action->resume($tenant);
            $this->components->info("{$tenant->name}: reactivado.".($wasBlocked ? ' Emita credenciales nuevas: las anteriores siguen revocadas.' : ''));

            return self::SUCCESS;
        }

        $this->option('block')
            ? $action->block($tenant, reason: $this->option('reason'))
            : $action->suspend($tenant, reason: $this->option('reason'));

        $this->components->warn("{$tenant->name}: ".($this->option('block') ? 'bloqueado; credenciales revocadas.' : 'suspendido.').' Sus solicitudes abiertas se cancelan en el proveedor.');

        return self::SUCCESS;
    }
}
