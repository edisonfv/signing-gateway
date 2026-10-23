<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:tenant:create {external_id : Identificador del cliente en el SaaS} {name} {--daily-cap=} {--hourly-cap=}')]
#[Description('Registra un cliente del SaaS (tenant) con sus topes duros')]
class CreateTenant extends Command
{
    public function handle(): int
    {
        $tenant = Tenant::create([
            'external_id' => $this->argument('external_id'),
            'name' => $this->argument('name'),
            'daily_cap' => $this->option('daily-cap'),
            'hourly_cap' => $this->option('hourly-cap'),
        ]);

        AuditLog::record('tenant.created', subject: $tenant, ip: 'cli');

        $this->components->info("Tenant {$tenant->name} creado: {$tenant->getKey()}");

        return self::SUCCESS;
    }
}
