<?php

namespace App\Console\Commands;

use App\Enums\Environment;
use App\Enums\Provider;
use App\Models\AuditLog;
use App\Models\ProviderAccount;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Carga el token del proveedor sin que pase por el código, el repositorio ni el historial
 * de la terminal: se pide con entrada oculta y se guarda cifrado con la bóveda.
 */
#[Signature('gateway:provider:credentials {environment : sandbox|production} {--provider=firmasegura} {--base-url=} {--name=}')]
#[Description('Crea o actualiza la cuenta de un proveedor y guarda su token cifrado')]
class SetProviderCredentials extends Command
{
    private const BASE_URLS = [
        'firmasegura' => [
            'sandbox' => 'https://api.dev-firmaseguraec.com',
            'production' => 'https://api.firmaseguraec.com',
        ],
    ];

    public function handle(): int
    {
        $provider = Provider::from($this->option('provider'));
        $environment = Environment::from($this->argument('environment'));

        $token = $this->secret("Token de {$provider->label} ({$environment->value})");

        if (blank($token)) {
            $this->components->error('No se ingresó ningún token.');

            return self::FAILURE;
        }

        $account = ProviderAccount::updateOrCreate(
            ['provider' => $provider->value, 'environment' => $environment->value],
            [
                'name' => $this->option('name') ?? "{$provider->label} {$environment->value}",
                'base_url' => $this->option('base-url') ?? self::BASE_URLS[$provider->value][$environment->value],
                'credentials' => ['token' => $token],
                'is_active' => true,
            ],
        );

        AuditLog::record('provider_account.credentials_set', subject: $account, ip: 'cli');

        $this->components->info("Credenciales guardadas cifradas en la cuenta {$account->getKey()}.");

        return self::SUCCESS;
    }
}
