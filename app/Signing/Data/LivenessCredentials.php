<?php

namespace App\Signing\Data;

use Carbon\CarbonImmutable;

/**
 * Sesión de prueba de vida y credenciales temporales que el SDK del navegador necesita.
 * Nunca se persisten en la base ni en logs.
 */
final readonly class LivenessCredentials
{
    public function __construct(
        public string $livenessSessionId,
        public string $accessKeyId,
        #[\SensitiveParameter] public string $secretAccessKey,
        #[\SensitiveParameter] public string $sessionToken,
        public string $region,
        public CarbonImmutable $expiresAt,
    ) {}

    /**
     * @return array{liveness_session_id: string, region: string, expires_at: string, credentials: array{access_key_id: string, secret_access_key: string, session_token: string}}
     */
    public function toArray(): array
    {
        return [
            'liveness_session_id' => $this->livenessSessionId,
            'region' => $this->region,
            'expires_at' => $this->expiresAt->toIso8601String(),
            'credentials' => [
                'access_key_id' => $this->accessKeyId,
                'secret_access_key' => $this->secretAccessKey,
                'session_token' => $this->sessionToken,
            ],
        ];
    }
}
