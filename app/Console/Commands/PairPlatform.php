<?php

namespace App\Console\Commands;

use App\Actions\PairPlatform as PairPlatformAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:pair {--minutes=10 : Vigencia del código (1 a 60)}')]
#[Description('Genera un código de un solo uso para emparejar la plataforma (SaaS) con este gateway')]
class PairPlatform extends Command
{
    public function handle(PairPlatformAction $pair): int
    {
        $minutes = max(1, min(60, (int) $this->option('minutes')));

        if ($this->laravel->isProduction() && config('gateway.network.allowed_sources') === []) {
            $this->components->error('Configure GATEWAY_ALLOWED_SOURCES (la IP de la plataforma en el túnel): sin ella la plataforma no puede llegar al gateway.');

            return self::FAILURE;
        }

        $code = $pair->generate($minutes);

        $this->newLine();
        $this->line("  Código de emparejamiento:  <options=bold>{$code}</>");
        $this->line("  Vence en {$minutes} minutos y sirve una sola vez.");
        $this->newLine();
        $this->line('  En la plataforma: ficha del proveedor del gateway → URL del gateway + este código → «Emparejar».');
        $this->components->warn('Emparejar revoca las credenciales anteriores de la plataforma; la plataforma las reemplaza sola.');

        return self::SUCCESS;
    }
}
