<?php

namespace Tests\Concerns;

use App\Models\City;
use App\Models\ProviderAccount;
use Illuminate\Support\Facades\Http;

/**
 * Simula la API de FirmaSegura (sandbox) para el Flujo por API.
 */
trait FakesFirmaSegura
{
    protected const FS_BASE = 'https://api.dev-firmaseguraec.com';

    protected const TERMS_ID = '37274d29-d120-4309-8804-6c755f2fbeeb';

    protected const P12_PASSWORD = 'Generada123456AB';

    protected function createFirmaSeguraAccount(): ProviderAccount
    {
        return ProviderAccount::create([
            'provider' => 'firmasegura',
            'environment' => 'sandbox',
            'name' => 'FirmaSegura sandbox',
            'base_url' => self::FS_BASE,
            'credentials' => ['token' => 'jwt-sandbox'],
        ]);
    }

    /**
     * Respuestas exitosas de todos los pasos; $overrides reemplaza la de un paso por su patrón de URL.
     *
     * @param  array<string, mixed>  $overrides
     */
    protected function fakeFirmaSegura(array $overrides = []): void
    {
        Http::preventStrayRequests();

        Http::fake([
            self::FS_BASE.'/gateway/collector/terms-and-conditions/by-type*' => Http::response([
                'id' => self::TERMS_ID, 'version' => '2.0.9', 'content' => 'Términos de prueba', 'type' => 'TRADITIONAL_SIGNATURE', 'active' => true,
            ]),
            self::FS_BASE.'/signature/api/request' => Http::response(['sessionId' => 'fs-session-1', 'status' => 'IN_PROGRESS']),
            self::FS_BASE.'/signature/api/session/*/civil-registry' => Http::response(['verified' => true, 'identificationNumber' => '1710034065', 'fingerCode' => 'E4333V2422']),
            self::FS_BASE.'/signature/api/face-liveness/partner/session' => Http::response([
                'sessionId' => 'aws-liveness-1',
                'token' => [
                    'accessKeyId' => 'ASIATEST', 'secretAccessKey' => 'secret-temporal', 'sessionToken' => 'session-token-temporal',
                    'expiration' => now()->addMinutes(15)->toIso8601String(), 'region' => 'us-east-1',
                ],
            ]),
            self::FS_BASE.'/signature/api/session/*/face-liveness' => Http::response([
                'livenessConfidence' => 98.5, 'livenessStatus' => 'SUCCEEDED', 'faceMatchSimilarity' => 97.1, 'biometricMatch' => true, 'sessionStatus' => 'IN_PROGRESS',
            ]),
            self::FS_BASE.'/signature/api/session/*/issuance' => Http::response([
                'serialNumber' => 'SN-001', 'password' => self::P12_PASSWORD, 'certificateBase64' => $this->testP12Base64(),
            ]),
            self::FS_BASE.'/signature/request/cancel*' => Http::response(status: 204),
            self::FS_BASE.'/collector/request' => Http::response([
                'requestId' => 'fs-regular-1', 'referenceTransaction' => 'ref', 'createdAt' => '2026-10-23 10:00:00',
            ]),
            self::FS_BASE.'/middleware/signature-request/status*' => Http::response([
                'request' => ['id' => 'fs-regular-1', 'status' => 'REGISTERED'],
                'validation' => ['record' => ['status' => 'PENDING'], 'errors' => []],
            ]),
            self::FS_BASE.'/manager/validation-request/request-data/*' => Http::response(['message' => 'Actualizado']),
            self::FS_BASE.'/signature/traditional/session/*/tracking' => Http::response([
                'sessionId' => 'fs-session-1', 'status' => ['code' => 'IN_PROGRESS', 'label' => 'En Progreso'],
                'stage' => ['code' => 'BIOMETRIC', 'label' => 'Biometría'], 'error' => null,
            ]),
            ...$overrides,
        ]);
    }

    /**
     * p12 autofirmado real, para comprobar que el gateway lo valida con su contraseña.
     */
    protected function testP12Base64(): string
    {
        $config = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
        $options = is_file($config) ? ['config' => $config] : [];

        $key = openssl_pkey_new(['private_key_bits' => 2048, ...$options]);
        $certificate = openssl_csr_sign(openssl_csr_new(['commonName' => 'Titular de prueba'], $key, $options), null, $key, 1, $options);
        openssl_pkcs12_export($certificate, $p12, $key, self::P12_PASSWORD);

        return base64_encode($p12);
    }

    /**
     * @return array<string, mixed>
     */
    protected function sessionPayload(array $overrides = []): array
    {
        $city = City::whereHas('province', fn ($query) => $query->where('name', 'PICHINCHA'))->where('name', 'QUITO')->firstOrFail();

        return array_replace_recursive([
            'product' => 'HEADLESS',
            'application_type' => 'NATURAL_PERSON',
            'period' => 'ONE_YEAR',
            'applicant' => [
                'identification_number' => '1710034065',
                'finger_code' => 'E4333V2422',
                'email' => 'titular@example.com',
                'cellphone' => '+593998765432',
                'address' => 'Av. Amazonas N12-34',
                'city_id' => $city->id,
            ],
            'terms' => [
                'id' => self::TERMS_ID,
                'version' => '2.0.9',
                'accepted_at' => now()->subMinute()->toIso8601String(),
                'applicant_ip' => '190.152.10.20',
                'user_agent' => 'Mozilla/5.0',
            ],
        ], $overrides);
    }
}
