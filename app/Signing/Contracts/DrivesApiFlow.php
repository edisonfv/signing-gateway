<?php

namespace App\Signing\Contracts;

use App\Exceptions\ProviderRequestException;
use App\Models\SignatureRequest;
use App\Signing\Data\CertificateData;
use App\Signing\Data\CivilRegistryResult;
use App\Signing\Data\LivenessCredentials;
use App\Signing\Data\LivenessResult;
use App\Signing\Data\SessionData;
use App\Signing\Data\SessionTracking;
use App\Signing\Data\TermsData;

/**
 * Flujo por API (headless): el aliado conduce cada paso; el proveedor no ofrece frontend.
 *
 * Todos los métodos lanzan ProviderRequestException ante una respuesta de error del proveedor.
 *
 * @throws ProviderRequestException
 */
interface DrivesApiFlow
{
    public function currentTerms(): TermsData;

    public function createSession(SignatureRequest $request): SessionData;

    public function validateCivilRegistry(SignatureRequest $request): CivilRegistryResult;

    public function requestLivenessSession(): LivenessCredentials;

    public function submitLiveness(SignatureRequest $request, string $livenessSessionId): LivenessResult;

    public function issueCertificate(SignatureRequest $request): CertificateData;

    public function cancel(SignatureRequest $request): void;

    /**
     * Consulta de estado de la sesión. Cuenta contra el límite de consultas de estado del proveedor.
     */
    public function trackSession(SignatureRequest $request): SessionTracking;
}
