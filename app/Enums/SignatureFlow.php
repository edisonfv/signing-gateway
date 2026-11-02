<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Mecánica con la que un proveedor ejecuta un producto: cómo se valida la identidad y quién
 * recibe el p12. El gateway conduce cada solicitud según su flujo, no según el proveedor.
 *
 * @method static self livenessApi()
 * @method static self documentReview()
 */
final class SignatureFlow extends Enum
{
    public function deliveryMode(): DeliveryMode
    {
        return $this->equals(self::livenessApi()) ? DeliveryMode::clientSealed() : DeliveryMode::holder();
    }

    /**
     * Cómo se valida la identidad del titular: LIVENESS o DOCUMENTS.
     */
    public function identityValidation(): string
    {
        return $this->equals(self::livenessApi()) ? 'LIVENESS' : 'DOCUMENTS';
    }

    protected static function values(): array
    {
        return [
            'livenessApi' => 'LIVENESS_API',
            'documentReview' => 'DOCUMENT_REVIEW',
        ];
    }

    protected static function labels(): array
    {
        return [
            'livenessApi' => 'Prueba de vida por API; el p12 se entrega sellado al SaaS',
            'documentReview' => 'Documentos revisados por operadores; el proveedor entrega el p12 al titular',
        ];
    }
}
