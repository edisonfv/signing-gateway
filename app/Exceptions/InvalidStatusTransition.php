<?php

namespace App\Exceptions;

use App\Enums\RequestStatus;
use App\Models\SignatureRequest;
use LogicException;

class InvalidStatusTransition extends LogicException
{
    public static function between(SignatureRequest $request, RequestStatus $next): self
    {
        return new self("La solicitud {$request->getKey()} no puede pasar de {$request->status->value} a {$next->value}.");
    }
}
