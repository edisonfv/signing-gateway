<?php

namespace App\Signing\Data;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Enums\SignatureFlow;

/**
 * Cómo ofrece un proveedor un producto: flujo, solicitantes, vigencias, documentos y textos.
 * Lo declara cada driver; el gateway arma el catálogo y valida las solicitudes con esto.
 */
final readonly class ProductOffering
{
    /**
     * @param  array<int, ApplicationType>  $applicationTypes
     * @param  array<int, CertificatePeriod>  $periods
     * @param  array<string, array<int, DocumentType>>  $requiredDocuments  ApplicationType::value => documentos obligatorios
     * @param  array{applies_from_age: int, instructions: string, template: string}|null  $elderlyVideo
     * @param  array<string, string>  $guides  Textos guía para el titular (selfie, identification…)
     */
    public function __construct(
        public ProductType $product,
        public SignatureFlow $flow,
        public array $applicationTypes,
        public array $periods,
        public array $requiredDocuments = [],
        public bool $allowsHolderPassword = false,
        public bool $requiresBirthDate = false,
        public ?array $elderlyVideo = null,
        public array $guides = [],
    ) {}

    public function acceptsDocuments(): bool
    {
        return $this->flow->equals(SignatureFlow::documentReview());
    }

    public function allowsApplicationType(ApplicationType $type): bool
    {
        return $type->equals(...$this->applicationTypes);
    }

    public function allowsPeriod(CertificatePeriod $period): bool
    {
        return $period->equals(...$this->periods);
    }

    /**
     * Documentos obligatorios para el tipo de solicitante y, si se conoce, la edad del titular.
     *
     * @return array<int, DocumentType>
     */
    public function documentsFor(ApplicationType $type, ?int $age = null): array
    {
        $documents = $this->requiredDocuments[$type->value] ?? [];

        if ($age !== null && $this->elderlyVideo !== null && $age >= $this->elderlyVideo['applies_from_age']) {
            $documents[] = DocumentType::authorizationVideo();
        }

        return $documents;
    }

    /**
     * La misma oferta limitada a las vigencias que el tenant tiene contratadas.
     *
     * @param  array<int, CertificatePeriod>|null  $periods  null = sin restricción
     */
    public function restrictedTo(?array $periods): self
    {
        if ($periods === null) {
            return $this;
        }

        $allowed = array_values(array_filter($this->periods, fn (CertificatePeriod $period) => $period->equals(...$periods)));

        return new self($this->product, $this->flow, $this->applicationTypes, $allowed, $this->requiredDocuments, $this->allowsHolderPassword, $this->requiresBirthDate, $this->elderlyVideo, $this->guides);
    }
}
