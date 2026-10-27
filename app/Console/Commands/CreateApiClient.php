<?php

namespace App\Console\Commands;

use App\Actions\IssueApiClientCredentials;
use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use InvalidArgumentException;

#[Signature('gateway:client:create {tenant : external_id del tenant} {name} {--ip=* : IP o CIDR permitido (repetible)} {--public-key= : Llave pública X25519 en base64 para sellar certificados} {--notification-url= : Endpoint HTTPS del SaaS para notificaciones} {--ability=* : Limita el token a estas abilities (repetible; p. ej. catalogs.view para solo leer el catálogo, o clients.manage para la credencial de alta de la plataforma). Sin la opción: las de un cliente}')]
#[Description('Emite la credencial de integración (token + secretos) de un cliente del SaaS')]
class CreateApiClient extends Command
{
    public function handle(IssueApiClientCredentials $issue): int
    {
        $tenant = Tenant::where('external_id', $this->argument('tenant'))->firstOrFail();

        try {
            $credentials = $issue($tenant, $this->argument('name'), $this->option('ip'), $this->option('public-key'), $this->option('notification-url'), $this->option('ability'));
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->warn('Copie estos valores en el admin del SaaS. No se volverán a mostrar.');
        $this->table(['Campo', 'Valor'], [
            ['Cliente', $credentials['client']->getKey()],
            ['Token de acceso', $credentials['token']],
            ['Secreto de firma', $credentials['signing_secret']],
            ['Secreto de notificaciones', $credentials['notification_secret']],
        ]);

        return self::SUCCESS;
    }
}
