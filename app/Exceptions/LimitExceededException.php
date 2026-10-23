<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class LimitExceededException extends Exception
{
    public function __construct(public readonly string $scope, public readonly int $limit)
    {
        parent::__construct("Se alcanzó el tope de emisión [{$scope}] de {$limit} solicitudes.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'scope' => $this->scope,
            'limit' => $this->limit,
        ], 429);
    }
}
