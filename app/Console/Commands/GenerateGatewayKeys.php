<?php

namespace App\Console\Commands;

use App\Security\KeyRing;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Genera las llaves de la bóveda y del índice ciego. Solo escribe las que falten en .env,
 * salvo --force. En producción usar --show y cargarlas como secretos del servidor.
 */
#[Signature('gateway:keys:generate {--show : Mostrar las llaves en lugar de escribirlas en .env} {--force : Reemplazar llaves existentes (los datos cifrados con ellas quedarán ilegibles)}')]
#[Description('Genera GATEWAY_VAULT_KEY, GATEWAY_VAULT_PASSWORD_KEY y GATEWAY_BLIND_INDEX_KEY')]
class GenerateGatewayKeys extends Command
{
    private const KEYS = ['GATEWAY_VAULT_KEY', 'GATEWAY_VAULT_PASSWORD_KEY', 'GATEWAY_BLIND_INDEX_KEY'];

    public function handle(): int
    {
        if ($this->option('show')) {
            foreach (self::KEYS as $name) {
                $this->line($name.'='.KeyRing::generateKey());
            }

            return self::SUCCESS;
        }

        if ($this->laravel->isProduction() && ! $this->confirm('Está en producción. ¿Escribir llaves en .env?')) {
            return self::FAILURE;
        }

        $envPath = $this->laravel->environmentFilePath();
        $env = File::get($envPath);

        foreach (self::KEYS as $name) {
            $exists = preg_match("/^{$name}=.+$/m", $env) === 1;

            if ($exists && ! $this->option('force')) {
                $this->components->warn("{$name} ya existe; no se modificó.");

                continue;
            }

            $line = $name.'='.KeyRing::generateKey();
            $env = preg_match("/^{$name}=.*$/m", $env)
                ? preg_replace("/^{$name}=.*$/m", $line, $env)
                : rtrim($env).PHP_EOL.$line.PHP_EOL;

            $this->components->info("{$name} generada.");
        }

        File::put($envPath, $env);

        return self::SUCCESS;
    }
}
