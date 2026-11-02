<?php

namespace App\Console\Commands;

use App\Models\ProviderAccount;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:install {--no-pair : No genera el código de emparejamiento al final}')]
#[Description('Instala o actualiza el gateway en este servidor (idempotente): llaves, base, proveedor, verificación y emparejamiento')]
class InstallGateway extends Command
{
    private const KEYS = ['gateway.keys.vault', 'gateway.keys.vault_password', 'gateway.keys.blind_index'];

    public function handle(): int
    {
        // 1. Llaves: se generan solo si faltan (nunca se reemplazan: los datos cifrados se perderían).
        if (collect(self::KEYS)->contains(fn (string $key) => blank(config($key))) || blank(config('app.key'))) {
            if (blank(config('app.key'))) {
                $this->call('key:generate', ['--force' => true]);
            }

            // Sin --no-interaction: en producción pide confirmación antes de escribir en .env.
            $this->call('gateway:keys:generate');
            $this->components->warn('Guarde las llaves de .env fuera del servidor (sin ellas los datos no se recuperan) y vuelva a ejecutar php artisan gateway:install para continuar.');

            return self::SUCCESS;
        }

        // 2. Base de datos y catálogos (las migraciones y los seeders son idempotentes).
        $this->components->task('Migraciones', fn () => $this->callSilently('migrate', ['--force' => true]) === 0);
        $this->components->task('Catálogos (roles, provincias y ciudades)', fn () => $this->callSilently('db:seed', ['--force' => true]) === 0);

        // 3. Cuenta del proveedor del ambiente configurado.
        $environment = (string) config('gateway.environment');
        $hasAccount = ProviderAccount::query()->where('environment', $environment)->where('is_active', true)->exists();

        if (! $hasAccount || ($this->input->isInteractive() && $this->confirm("Ya hay una cuenta de FirmaSegura ({$environment}). ¿Reemplazar su token?", false))) {
            if (! $this->input->isInteractive()) {
                $this->components->warn("Falta la cuenta de FirmaSegura ({$environment}): ejecute php artisan gateway:provider:credentials {$environment}.");
            } else {
                $this->call('gateway:provider:credentials', ['environment' => $environment]);
            }
        }

        // 4. Verificación.
        $this->newLine();
        $healthy = $this->call('gateway:doctor') === self::SUCCESS;

        if (! $healthy) {
            $this->components->error('Corrija lo indicado y vuelva a ejecutar php artisan gateway:install (no repite lo que ya está hecho).');

            return self::FAILURE;
        }

        // 5. Emparejamiento con la plataforma.
        if (! $this->option('no-pair') && $this->input->isInteractive() && $this->confirm('¿Generar ahora el código para emparejar la plataforma?', true)) {
            return $this->call('gateway:pair');
        }

        return self::SUCCESS;
    }
}
