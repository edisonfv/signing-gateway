<?php

namespace App\Signing\Drivers\FirmaSegura;

use App\Enums\ApplicationType;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Exceptions\ProviderRequestException;
use App\Models\ProviderAccount;
use App\Models\SignatureDocument;
use App\Models\SignatureRequest;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\Contracts\RegistersRegularRequests;
use App\Signing\Contracts\SignatureProvider;
use App\Signing\Data\CertificateData;
use App\Signing\Data\CivilRegistryResult;
use App\Signing\Data\LivenessCredentials;
use App\Signing\Data\LivenessResult;
use App\Signing\Data\ProductOffering;
use App\Signing\Data\RegularRequestState;
use App\Signing\Data\SessionData;
use App\Signing\Data\SessionTracking;
use App\Signing\Data\TermsData;
use App\Signing\Drivers\FirmaSegura\Enums\TermsType;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use LogicException;
use Throwable;

/**
 * Driver de FirmaSeguraEC.
 *
 * Flujo por API: https://docs.firmaseguraec.com/flujo-por-api-2110187m0
 * Ningún POST se reintenta automáticamente: cada job decide si es seguro reintentar.
 */
class FirmaSeguraDriver implements DrivesApiFlow, RegistersRegularRequests, SignatureProvider
{
    /** Zona de las fechas sin zona que devuelve el proveedor. */
    private const PROVIDER_TIMEZONE = 'America/Guayaquil';

    private ?ProviderAccount $account = null;

    public function name(): string
    {
        return 'firmasegura';
    }

    public function offerings(): array
    {
        return FirmaSeguraOfferings::all();
    }

    public function offering(ProductType $product): ?ProductOffering
    {
        return collect($this->offerings())->first(fn (ProductOffering $offering) => $offering->product->equals($product));
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
     * POST /signature/api/request. Si el titular no eligió contraseña, el proveedor genera una de 16 caracteres.
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
            'password' => $request->applicant_password,
            'callbackUrl' => $request->providerAccount->webhookUrl(),
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

    /**
     * GET /signature/traditional/session/{sessionId}/tracking
     * (verificado en sandbox: también aplica a sesiones del Flujo por API).
     */
    public function trackSession(SignatureRequest $request): SessionTracking
    {
        $response = $this->ensureSuccessful(
            $this->http()->get("/signature/traditional/session/{$this->sessionId($request)}/tracking"),
        );

        return new SessionTracking(
            (string) $response->json('status.code'),
            $response->json('stage.code'),
            $response->json('error.code'),
            $response->json('error.detail') ?? $response->json('error.message'),
        );
    }

    /**
     * POST /collector/request (Firmas Regulares). Las imágenes, PDFs y video van en base64.
     */
    public function registerRegularRequest(SignatureRequest $request): string
    {
        $applicant = $request->applicant;
        $company = $applicant['company'] ?? null;
        $isLegalRepresentative = $request->application_type->equals(ApplicationType::legalRepresentative());
        $documents = $request->documents()->whereNull('purged_at')->get()->keyBy(fn (SignatureDocument $document) => $document->type->value);
        $document = fn (DocumentType $type): ?string => $documents->get($type->value)?->base64Content();

        $payload = array_filter([
            'referenceTransaction' => $request->referenceTransaction(),
            'fingerCode' => $applicant['finger_code'],
            'city' => $applicant['city'],
            'province' => $applicant['province'],
            'address' => $applicant['address'],
            'identificationNumber' => $applicant['identification_number'],
            'emailAddress' => $applicant['email'],
            'cellphoneNumber' => $applicant['cellphone'],
            'countryCode' => 'ECU',
            'documentType' => 'CI',
            'applicationType' => $isLegalRepresentative ? ApplicationType::legalRepresentative()->value : ApplicationType::naturalPerson()->value,
            'period' => $request->period->value,
            'termsAndConditionsId' => $request->termsAcceptance->provider_terms_id,
            'identificationFront' => $document(DocumentType::identificationFront()),
            'identificationBack' => $document(DocumentType::identificationBack()),
            'identificationSelfie' => $document(DocumentType::identificationSelfie()),
            'companyRuc' => $isLegalRepresentative ? $company['ruc'] : ($applicant['person_ruc'] ?? null),
            'positionCompany' => $company['position'] ?? null,
            'companySocialReason' => $company['name'] ?? null,
            'appointmentExpirationDate' => isset($company['appointment_expires_at']) ? "{$company['appointment_expires_at']} 23:59:59" : null,
            'pdfCompanyRuc' => $document(DocumentType::companyRucPdf()),
            'pdfRepresentativeAppointment' => $document(DocumentType::representativeAppointmentPdf()),
            'pdfAppointmentAcceptance' => $document(DocumentType::appointmentAcceptancePdf()),
            'pdfCompanyConstitution' => $document(DocumentType::companyConstitutionPdf()),
            'authorizationVideo' => $document(DocumentType::authorizationVideo()),
        ], fn ($value) => $value !== null);

        $response = $this->ensureSuccessful($this->http()->timeout(120)->post('/collector/request', $payload));

        return $response->json('requestId')
            ?? throw new ProviderRequestException(409, (string) ($response->json('message') ?? 'El proveedor no devolvió requestId.'));
    }

    /**
     * GET /middleware/signature-request/status (con detalles de validación).
     */
    public function regularRequestState(SignatureRequest $request): RegularRequestState
    {
        $query = $request->provider_request_id !== null
            ? ['requestId' => $request->provider_request_id]
            : ['referenceTransaction' => $request->referenceTransaction()];

        $response = $this->ensureSuccessful(
            $this->http()->get('/middleware/signature-request/status', [...$query, 'includeValidationDetails' => 'true']),
        );

        $errors = collect($response->json('validation.errors') ?? [])
            ->reject(fn (array $error) => (bool) ($error['managed'] ?? false) || ($error['active'] ?? true) === false)
            ->map(fn (array $error) => ['code' => (string) ($error['code'] ?? ''), 'reason' => $error['reason'] ?? null])
            ->values()
            ->all();

        return new RegularRequestState(
            (string) ($response->json('request.id') ?? $request->provider_request_id),
            (string) $response->json('request.status'),
            $response->json('validation.record.status') ?? $response->json('validation.status'),
            $errors,
            $response->json('validation.record.automaticCancellationReason') ?? $response->json('validation.automaticCancellationReason'),
            $response->json('revocation.status'),
            $this->review($response),
        );
    }

    /**
     * Lo que el proveedor cuenta mientras revisa: si un operador la tomó (`operatorAssignments`), sus
     * observaciones (p. ej. «otra solicitud reciente del aplicante…») y su última actualización. Las
     * fechas llegan en hora de Ecuador y sin zona; los números de cédula o RUC se tapan.
     *
     * @return array{operator_assigned_at: string|null, observations: array<int, array{type: string, message: string, created_at: string|null}>, updated_at: string|null}
     */
    private function review(Response $response): array
    {
        $date = function (mixed $value): ?string {
            if (! is_string($value) || $value === '') {
                return null;
            }

            try {
                return CarbonImmutable::parse($value, self::PROVIDER_TIMEZONE)->utc()->toIso8601String();
            } catch (Throwable) {
                return null;
            }
        };

        // La documentación las pone en `validation`; el sandbox, en `validation.record`.
        $assignments = collect($response->json('validation.record.operatorAssignments') ?? $response->json('validation.operatorAssignments') ?? [])
            ->filter(fn (mixed $assignment) => is_array($assignment));
        $assignedAt = $assignments->map(fn (array $assignment) => $date($assignment['startAssignmentDate'] ?? null))->filter()->max();

        if ($assignedAt === null && ($assignments->isNotEmpty() || filled($response->json('validation.record.operator.id')))) {
            $assignedAt = $date($response->json('validation.record.updatedDate')) ?? $date($response->json('timeDetails.requestUpdatedAt')) ?? now()->toIso8601String();
        }

        $observations = collect($response->json('validation.observations') ?? [])
            ->filter(fn (mixed $observation) => is_array($observation) && filled($observation['message'] ?? null))
            ->map(fn (array $observation) => [
                'type' => (string) ($observation['type'] ?? 'INFO'),
                'message' => Str::limit(preg_replace_callback('/\d{10,13}/', fn (array $digits) => '…'.substr($digits[0], -4), (string) $observation['message']), 300),
                'created_at' => $date($observation['createdAt'] ?? null),
            ])
            ->values()
            ->all();

        return [
            'operator_assigned_at' => $assignedAt,
            'observations' => $observations,
            'updated_at' => $date($response->json('request.updatedDate')) ?? $date($response->json('timeDetails.requestUpdatedAt')),
        ];
    }

    /**
     * PATCH /manager/validation-request/request-data/{referenceTransaction}
     */
    public function correctRegularRequest(SignatureRequest $request, ?string $fingerCode, array $documents): void
    {
        $payload = array_filter([
            'fingerCode' => $fingerCode,
            'identificationFront' => $documents[DocumentType::identificationFront()->value] ?? null,
            'identificationBack' => $documents[DocumentType::identificationBack()->value] ?? null,
            'identificationSelfie' => $documents[DocumentType::identificationSelfie()->value] ?? null,
        ], fn ($value) => $value !== null);

        $this->ensureSuccessful(
            $this->http()->patch("/manager/validation-request/request-data/{$request->referenceTransaction()}", $payload),
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
