<?php

namespace App\Signing\Contracts;

use App\Enums\DocumentType;
use App\Exceptions\ProviderRequestException;
use App\Models\SignatureRequest;
use App\Signing\Data\RegularRequestState;

/**
 * Firmas Regulares: el aliado envía datos y documentos; operadores del proveedor validan y
 * el proveedor entrega el certificado directamente al titular por correo.
 *
 * @throws ProviderRequestException
 */
interface RegistersRegularRequests
{
    /**
     * Registra la solicitud con sus documentos y devuelve el requestId del proveedor.
     */
    public function registerRegularRequest(SignatureRequest $request): string;

    /**
     * Estado por requestId o, si aún no se conoce, por referenceTransaction.
     * Cuenta contra el límite de consultas de estado del proveedor.
     */
    public function regularRequestState(SignatureRequest $request): RegularRequestState;

    /**
     * Corrige código dactilar y/o imágenes con error activo (solo en estado ERROR).
     *
     * @param  array<string, string>  $documents  Tipo de documento (DocumentType::value) => base64
     */
    public function correctRegularRequest(SignatureRequest $request, ?string $fingerCode, array $documents): void;
}
