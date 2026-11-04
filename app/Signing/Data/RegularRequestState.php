<?php

namespace App\Signing\Data;

/**
 * Estado de una solicitud de Firmas Regulares según el proveedor.
 */
final readonly class RegularRequestState
{
    /**
     * @param  array<int, array{code: string, reason: string|null}>  $errors  Errores de validación (A01–A04, D01–D06)
     * @param  array{operator_assigned_at: string|null, observations: array<int, array{type: string, message: string, created_at: string|null}>, updated_at: string|null}|null  $review  Lo que el proveedor cuenta mientras revisa
     */
    public function __construct(
        public string $requestId,
        public string $status,
        public ?string $validationStatus,
        public array $errors,
        public ?string $automaticCancellationReason,
        public ?string $revocationStatus,
        public ?array $review = null,
    ) {}

    public function errorMessage(): ?string
    {
        if ($this->errors === []) {
            return $this->automaticCancellationReason;
        }

        return implode(' ', array_map(fn (array $error) => trim("{$error['code']}: ".($error['reason'] ?? '')), $this->errors));
    }

    /**
     * @return array<int, string>
     */
    public function errorCodes(): array
    {
        return array_values(array_unique(array_map(fn (array $error) => $error['code'], $this->errors)));
    }
}
