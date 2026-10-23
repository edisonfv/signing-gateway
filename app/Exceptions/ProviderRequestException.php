<?php

namespace App\Exceptions;

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
    public function isRetryable(): bool
    {
        return $this->httpStatus >= 500 || $this->httpStatus === 429;
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
