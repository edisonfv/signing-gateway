<?php

namespace App\Actions;

use App\Enums\PermissionName;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\PairingCode;
use Illuminate\Support\Facades\DB;

/**
 * Empareja la plataforma con el gateway sin copiar secretos: el operador genera un código corto
 * (gateway:pair) y la plataforma lo canjea por su credencial de alta en POST /v1/pair.
 *
 * El código es de un solo uso, vence en minutos y se guarda como hash. Cinco intentos fallidos
 * invalidan los códigos vigentes. Al emparejar, las credenciales anteriores de la plataforma se
 * revocan: emparejar de nuevo es también la forma de rotar o de mudarse de servidor.
 */
class PairPlatform
{
    public const MAX_FAILED_ATTEMPTS = 5;

    public function __construct(
        private IssueApiClientCredentials $issue,
        private RevokeApiClient $revoke,
    ) {}

    /**
     * Genera un código nuevo (invalida los anteriores sin usar) y lo devuelve en claro UNA vez.
     */
    public function generate(int $minutes = 10): string
    {
        $raw = collect(range(1, PairingCode::LENGTH))
            ->map(fn () => PairingCode::ALPHABET[random_int(0, strlen(PairingCode::ALPHABET) - 1)])
            ->implode('');

        DB::transaction(function () use ($raw, $minutes) {
            PairingCode::query()->active()->update(['expires_at' => now()]);
            PairingCode::create(['code_hash' => PairingCode::hash($raw), 'expires_at' => now()->addMinutes($minutes)]);
        });

        AuditLog::record('platform.pairing_code_created', context: ['minutes' => $minutes], ip: 'cli');

        return substr($raw, 0, 4).'-'.substr($raw, 4);
    }

    /**
     * Canjea el código por la credencial de alta de la plataforma, limitada a la IP que lo canjea.
     * Devuelve null si el código no es válido, ya se usó o venció.
     *
     * @return array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null}|null
     */
    public function redeem(string $code, string $ip): ?array
    {
        return DB::transaction(function () use ($code, $ip) {
            $pairing = PairingCode::query()->active()->where('code_hash', PairingCode::hash($code))->lockForUpdate()->first();

            if ($pairing === null) {
                $this->registerFailure($ip);

                return null;
            }

            ApiClient::query()->whereNull('tenant_id')->whereNull('revoked_at')->get()
                ->each(fn (ApiClient $client) => ($this->revoke)($client));

            $credentials = ($this->issue)(null, 'Plataforma: alta · '.now()->format('Y-m-d'), [$ip], abilities: [PermissionName::clientsManage()->value]);

            $pairing->update(['used_at' => now(), 'used_ip' => $ip, 'api_client_id' => $credentials['client']->getKey()]);
            AuditLog::record('platform.paired', subject: $credentials['client'], context: ['ip' => $ip], ip: $ip);

            return $credentials;
        });
    }

    private function registerFailure(string $ip): void
    {
        $active = PairingCode::query()->active()->get();
        $active->each->increment('failed_attempts');

        if ($active->contains(fn (PairingCode $pairing) => $pairing->failed_attempts >= self::MAX_FAILED_ATTEMPTS)) {
            PairingCode::query()->active()->update(['expires_at' => now()]);
            AuditLog::record('platform.pairing_locked', context: ['ip' => $ip], ip: $ip);

            return;
        }

        AuditLog::record('platform.pairing_failed', context: ['ip' => $ip], ip: $ip);
    }
}
