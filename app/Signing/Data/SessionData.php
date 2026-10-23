<?php

namespace App\Signing\Data;

/**
 * Sesión creada en el proveedor para el Flujo por API.
 */
final readonly class SessionData
{
    public function __construct(
        public string $sessionId,
        public string $status,
    ) {}
}
