<?php

namespace App\Signing;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Models\Tenant;

/**
 * Productos implementados y sus requisitos, para que el frontend del SaaS elija el flujo
 * según las condiciones comerciales del tenant y arme el formulario correcto.
 */
class ProductCatalog
{
    public const ELDERLY_AGE = 65;

    /**
     * Texto que el titular de 65 años o más lee en el video de autorización (Firmas Regulares).
     */
    public const ELDERLY_VIDEO_TEMPLATE = 'Hoy [FECHA], Yo [NOMBRES_APELLIDOS] con CI. [CEDULA] autorizo a FIRMASEGURA emitir mi firma electrónica, sabiendo que tiene la misma validez legal que la firma manuscrita y sea enviada a mi correo electrónico [CORREO].';

    /**
     * @return array<int, ProductType>
     */
    public function implemented(): array
    {
        return [ProductType::headless(), ProductType::regular()];
    }

    public function isImplemented(ProductType $product): bool
    {
        return $product->equals(...$this->implemented());
    }

    /**
     * @return array<int, ProductType>
     */
    public function availableFor(Tenant $tenant): array
    {
        return array_values(array_filter($tenant->enabledProducts(), fn (ProductType $product) => $this->isImplemented($product)));
    }

    /**
     * @return array<int, ApplicationType>
     */
    public function applicationTypes(ProductType $product): array
    {
        return $product->equals(ProductType::regular())
            ? ApplicationType::cases()
            : [ApplicationType::naturalPerson(), ApplicationType::naturalPersonRuc()];
    }

    /**
     * @return array<int, CertificatePeriod>
     */
    public function periods(ProductType $product): array
    {
        return array_values(array_filter(CertificatePeriod::cases(), fn (CertificatePeriod $period) => ! $period->equals(CertificatePeriod::oneDay())));
    }

    /**
     * Documentos obligatorios según producto y tipo de solicitante (sin contar el video por edad).
     *
     * @return array<int, DocumentType>
     */
    public function requiredDocuments(ProductType $product, ApplicationType $applicationType): array
    {
        if (! $product->equals(ProductType::regular())) {
            return [];
        }

        $documents = [DocumentType::identificationFront(), DocumentType::identificationBack(), DocumentType::identificationSelfie()];

        return match ($applicationType->value) {
            ApplicationType::naturalPersonRuc()->value => [...$documents, DocumentType::companyRucPdf()],
            ApplicationType::legalRepresentative()->value => [
                ...$documents,
                DocumentType::companyRucPdf(),
                DocumentType::representativeAppointmentPdf(),
                DocumentType::companyConstitutionPdf(),
            ],
            default => $documents,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function describe(ProductType $product): array
    {
        $isRegular = $product->equals(ProductType::regular());

        return [
            'product' => $product->value,
            'label' => $product->label,
            'delivery_mode' => $product->deliveryMode()->value,
            'identity_validation' => $isRegular ? 'DOCUMENTS' : 'LIVENESS',
            'requires_liveness' => ! $isRegular,
            'requires_birth_date' => $isRegular,
            'application_types' => array_map(fn (ApplicationType $type) => [
                'value' => $type->value,
                'label' => $type->label,
                'required_documents' => array_map(fn (DocumentType $document) => $document->value, $this->requiredDocuments($product, $type)),
            ], $this->applicationTypes($product)),
            'periods' => array_map(fn (CertificatePeriod $period) => ['value' => $period->value, 'label' => $period->label], $this->periods($product)),
            'documents' => $isRegular ? array_map(fn (DocumentType $document) => [
                'type' => $document->value,
                'label' => $document->label,
                'mimes' => $document->allowedMimes(),
                'max_bytes' => $document->maxBytes(),
            ], DocumentType::cases()) : [],
            'guides' => $isRegular ? [
                'selfie' => 'Rostro descubierto y centrado, con buena luz de frente, sin lentes oscuros, gorra ni mascarilla. Fondo claro y sin otras personas.',
                'identification' => 'Cédula completa y legible, sin reflejos ni dedos sobre los datos, fotografiada sobre una superficie plana.',
                'elderly_video' => [
                    'applies_from_age' => self::ELDERLY_AGE,
                    'instructions' => 'Grabe un video corto, de frente y con buena luz, leyendo el texto en voz alta y claro.',
                    'template' => self::ELDERLY_VIDEO_TEMPLATE,
                ],
            ] : [],
        ];
    }
}
