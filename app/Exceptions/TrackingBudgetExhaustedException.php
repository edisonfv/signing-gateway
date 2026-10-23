<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Se agotó el presupuesto horario de consultas de estado al proveedor.
 */
class TrackingBudgetExhaustedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Se agotó el límite de consultas de estado al proveedor para esta hora.');
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 429);
    }
}
