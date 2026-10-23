<?php

namespace App\Signing\Drivers\FirmaSegura;

use App\Enums\ProductType;
use App\Exceptions\ProviderRequestException;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\Contracts\SignatureProvider;
use App\Signing\Data\CertificateData;
use App\Signing\Data\CivilRegistryResult;
use App\Signing\Data\LivenessCredentials;
use App\Signing\Data\LivenessResult;
use App\Signing\Data\SessionData;
use App\Signing\Data\TermsData;
use App\Signing\Drivers\FirmaSegura\Enums\TermsType;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use LogicException;

/**
 * Driver de FirmaSeguraEC.
 *
 * Flujo por API: https://docs.firmaseguraec.com/flujo-por-api-2110187m0
 * Ningún POST se reintenta automáticamente: cada job decide si es seguro reintentar.
 */
class FirmaSeguraDriver implements DrivesApiFlow, SignatureProvider
{
    private ?ProviderAccount $account = null;

    public function name(): string
    {
        return 'firmasegura';
    }

    public function supports(ProductType $product): bool
    {
        return $product->equals(ProductType::headless());
    }

    public function withAccount(ProviderAccount $account): static
    {
        $driver = clone $this;
        $driver->account = $account;

        return $driver;
    }

    /**
     * Cliente HTTP con la URL y el token de la cuenta.
     */
    public function http(): PendingRequest
    {
        $account = $this->account();

        return $this->baseRequest()
            ->withToken($account->credentials['token'] ?? throw new LogicException("La cuenta {$account->getKey()} no tiene token."));
    }

    /**
     * GET /gateway/collector/terms-and-conditions/by-type (sin autenticación).
     */
    public function currentTerms(): TermsData
    {
        $response = $this->ensureSuccessful(
            $this->baseRequest()->get('/gateway/collector/terms-and-conditions/by-type', ['type' => TermsType::traditionalSignature()->value]),
        );

        return new TermsData($response->json('id'), (string) $response->json('version'), (string) $response->json('content'));
    }

    /**
     * POST /signature/api/request. No se envía contraseña: el proveedor genera una de 16 caracteres.
     */
    public function createSession(SignatureRequest $request): SessionData
    {
        $applicant = $request->applicant;

        $payload = array_filter([
            'referenceTransaction' => $request->referenceTransaction(),
            'identificationNumber' => $applicant['identification_number'],
            'fingerCode' => $applicant['finger_code'],
            'applicationType' => $request->application_type->value,
            'personRuc' => $applicant['person_ruc'] ?? null,
            'email' => $applicant['email'],
            'cellphoneNumber' => $applicant['cellphone'],
            'address' => $applicant['address'],
            'province' => $applicant['province'],
            'city' => $applicant['city'],
            'period' => $request->period->value,
            'termsAndConditionsId' => $request->termsAcceptance->provider_terms_id,
        ], fn ($value) => $value !== null);

        $response = $this->ensureSuccessful($this->http()->post('/signature/api/request', $payload));

        return new SessionData($response->json('sessionId'), (string) $response->json('status'));
    }

    /**
     * POST /signature/api/session/{sessionId}/civil-registry
     */
    public function validateCivilRegistry(SignatureRequest $request): CivilRegistryResult
    {
        $response = $this->ensureSuccessful(
            $this->http()->post("/signature/api/session/{$this->sessionId($request)}/civil-registry"),
        );

        return new CivilRegistryResult((bool) $response->json('verified'));
    }

    /**
     * GET /signature/api/face-liveness/partner/session
     */
    public function requestLivenessSession(): LivenessCredentials
    {
        $response = $this->ensureSuccessful($this->http()->get('/signature/api/face-liveness/partner/session'));

        return new LivenessCredentials(
            $response->json('sessionId'),
            $response->json('token.accessKeyId'),
            $response->json('token.secretAccessKey'),
            $response->json('token.sessionToken'),
            $response->json('token.region'),
            CarbonImmutable::parse($response->json('token.expiration'))->utc(),
        );
    }

    /**
     * POST /signature/api/session/{sessionId}/face-liveness
     */
    public function submitLiveness(SignatureRequest $request, string $livenessSessionId): LivenessResult
    {
        $response = $this->ensureSuccessful(
            $this->http()->post("/signature/api/session/{$this->sessionId($request)}/face-liveness", [
                'livenessSessionId' => $livenessSessionId,
            ]),
        );

        return new LivenessResult(
            (bool) $response->json('biometricMatch'),
            $response->json('livenessConfidence'),
            $response->json('faceMatchSimilarity'),
            $response->json('sessionStatus'),
        );
    }

    /**
     * POST /signature/api/session/{sessionId}/issuance: devuelve el p12 y su contraseña.
     */
    public function issueCertificate(SignatureRequest $request): CertificateData
    {
        $response = $this->ensureSuccessful(
            $this->http()->timeout(60)->post("/signature/api/session/{$this->sessionId($request)}/issuance"),
        );

        return new CertificateData(
            $response->json('certificateBase64'),
            $response->json('password'),
            $response->json('serialNumber'),
        );
    }

    /**
     * PUT /signature/request/cancel?referenceTransaction=…
     */
    public function cancel(SignatureRequest $request): void
    {
        $this->ensureSuccessful(
            $this->http()->put('/signature/request/cancel?'.http_build_query(['referenceTransaction' => $request->referenceTransaction()])),
        );
    }

    private function baseRequest(): PendingRequest
    {
        return Http::baseUrl($this->account()->base_url)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(30);
    }

    private function account(): ProviderAccount
    {
        return $this->account ?? throw new LogicException('El driver de FirmaSegura no tiene una cuenta asignada.');
    }

    private function sessionId(SignatureRequest $request): string
    {
        return $request->provider_session_id ?? throw new LogicException("La solicitud {$request->getKey()} aún no tiene sesión en el proveedor.");
    }

    /**
     * @throws ProviderRequestException
     */
    private function ensureSuccessful(Response $response): Response
    {
        if ($response->failed()) {
            throw ProviderRequestException::fromResponse($response);
        }

        return $response;
    }
}
