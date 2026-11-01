<?php

namespace App\Exceptions;

use App\Signing\Drivers\FirmaSegura\Enums\SessionStatus;
use Illuminate\Http\Client\Response;
use RuntimeException;

/**
 * Respuesta de error del proveedor, con los datos que el gateway necesita para decidir:
 * reintentar (fallo técnico) o cerrar la solicitud (regla de negocio).
 */
class ProviderRequestException extends RuntimeException
{
    public function __construct(
        public readonly int $httpStatus,
        string $message,
        public readonly ?string $sessionStatus = null,
        public readonly ?int $remainingAttempts = null,
    ) {
        parent::__construct($message, $httpStatus);
    }

    public static function fromResponse(Response $response): self
    {
        $remaining = $response->json('remainingAttempts');

        return new self(
            $response->status(),
            (string) ($response->json('message') ?? $response->json('error') ?? $response->reason()),
            $response->json('sessionStatus'),
            is_numeric($remaining) ? (int) $remaining : null,
        );
    }

    /**
     * Fallos técnicos o transitorios: vale la pena reintentar.
     */
    /** El mismo error con un mensaje para el titular (el del proveedor puede inducir a error). */
    public function withMessage(string $message): self
    {
        return new self($this->httpStatus, $message, $this->sessionStatus, $this->remainingAttempts);
    }

    public function isRetryable(): bool
    {
        return $this->httpStatus >= 500 || $this->httpStatus === 429;
    }

    /**
     * El titular puede repetir el paso (p. ej. otra prueba de vida).
     *
     * Se decide por el estado de sesión que informa el proveedor: el sandbox responde
     * sessionStatus RETRYABLE sin remainingAttempts, aunque la documentación muestra FAILED.
     */
    public function allowsApplicantRetry(): bool
    {
        if ($this->remainingAttempts !== null) {
            return $this->remainingAttempts > 0;
        }

        return in_array($this->sessionStatus, [
            SessionStatus::retryable()->value,
            SessionStatus::error()->value,
            SessionStatus::failed()->value,
        ], true);
    }

    /**
     * @return array{http_status: int, message: string, session_status: string|null}
     */
    public function toArray(): array
    {
        return [
            'http_status' => $this->httpStatus,
            'message' => $this->getMessage(),
            'session_status' => $this->sessionStatus,
        ];
    }
}
