<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * El operador detuvo la emisión (gateway:halt): no se crean solicitudes nuevas.
 */
class EmissionHaltedException extends Exception
{
    public function __construct()
    {
        parent::__construct('La emisión de firmas está detenida temporalmente. Intente más tarde.');
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], 503);
    }
}
