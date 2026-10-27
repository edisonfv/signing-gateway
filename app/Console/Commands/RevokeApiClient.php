<?php

namespace App\Console\Commands;

use App\Actions\RevokeApiClient as RevokeApiClientAction;
use App\Models\ApiClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('gateway:client:revoke {client : id del cliente (lo muestra gateway:client:create y --list)} {--list : Solo lista los clientes de un tenant: pase su external_id como client}')]
#[Description('Revoca la credencial de un cliente del SaaS: sus tokens dejan de funcionar al instante')]
class RevokeApiClient extends Command
{
    public function handle(RevokeApiClientAction $revoke): int
    {
        if ($this->option('list')) {
            $clients = ApiClient::query()
                ->whereHas('tenant', fn ($query) => $query->where('external_id', $this->argument('client')))
                ->with('tokens')
                ->oldest()
                ->get();

            $this->table(['Cliente', 'Nombre', 'Abilities', 'Revocado', 'Creado'], $clients->map(fn (ApiClient $client) => [
                $client->getKey(),
                $client->name,
                implode(', ', $client->tokens->flatMap->abilities->unique()->all()),
                $client->revoked_at?->toDateTimeString() ?? '—',
                $client->created_at?->toDateTimeString(),
            ])->all());

            return self::SUCCESS;
        }

        $client = ApiClient::query()->findOrFail($this->argument('client'));

        if (! $revoke($client)) {
            $this->components->info("El cliente «{$client->name}» ya estaba revocado.");

            return self::SUCCESS;
        }

        $this->components->info("Cliente «{$client->name}» revocado.");

        return self::SUCCESS;
    }
}
