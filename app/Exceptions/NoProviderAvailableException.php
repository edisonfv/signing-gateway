<?php

namespace App\Exceptions;

use App\Enums\Environment;
use App\Enums\ProductType;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class NoProviderAvailableException extends RuntimeException
{
    public function __construct(ProductType $product, Environment $environment)
    {
        parent::__construct("No hay una cuenta de proveedor activa para {$product->value} en {$environment->value}.");
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => 'Servicio de firma no disponible.'], 503);
    }
}
