<?php

namespace App\Signing\Data;

/**
 * Estado de una sesión según el tracking del proveedor.
 */
final readonly class SessionTracking
{
    public function __construct(
        public string $status,
        public ?string $stage,
        public ?string $errorCode,
        public ?string $errorDetail,
    ) {}
}
