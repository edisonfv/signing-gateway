<?php

namespace App\Signing\Drivers\FirmaSegura;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Enums\SignatureFlow;
use App\Signing\Data\ProductOffering;

/**
 * Productos que FirmaSegura ofrece por este gateway y sus requisitos.
 *
 * Fuentes: https://docs.firmaseguraec.com/flujo-por-api-2110187m0 y
 * https://docs.firmaseguraec.com/firmas-regulares-1820343m0
 */
final class FirmaSeguraOfferings
{
    public const ELDERLY_AGE = 65;

    /**
     * Texto que el titular de 65 años o más lee en el video de autorización (Firmas Regulares).
     */
    public const ELDERLY_VIDEO_TEMPLATE = 'Hoy [FECHA], Yo [NOMBRES_APELLIDOS] con CI. [CEDULA] autorizo a FIRMASEGURA emitir mi firma electrónica, sabiendo que tiene la misma validez legal que la firma manuscrita y sea enviada a mi correo electrónico [CORREO].';

    /**
     * @return array<int, ProductOffering>
     */
    public static function all(): array
    {
        return [self::headless(), self::regular()];
    }

    private static function headless(): ProductOffering
    {
        return new ProductOffering(
            product: ProductType::headless(),
            flow: SignatureFlow::livenessApi(),
            applicationTypes: [ApplicationType::naturalPerson(), ApplicationType::naturalPersonRuc()],
            periods: self::periods(),
            allowsHolderPassword: true,
        );
    }

    private static function regular(): ProductOffering
    {
        $identity = [DocumentType::identificationFront(), DocumentType::identificationBack(), DocumentType::identificationSelfie()];

        return new ProductOffering(
            product: ProductType::regular(),
            flow: SignatureFlow::documentReview(),
            applicationTypes: ApplicationType::cases(),
            periods: self::periods(),
            requiredDocuments: [
                ApplicationType::naturalPerson()->value => $identity,
                ApplicationType::naturalPersonRuc()->value => [...$identity, DocumentType::companyRucPdf()],
                ApplicationType::legalRepresentative()->value => [
                    ...$identity,
                    DocumentType::companyRucPdf(),
                    DocumentType::representativeAppointmentPdf(),
                    DocumentType::companyConstitutionPdf(),
                ],
            ],
            requiresBirthDate: true,
            elderlyVideo: [
                'applies_from_age' => self::ELDERLY_AGE,
                'instructions' => 'Grabe un video corto, de frente y con buena luz, leyendo el texto en voz alta y claro.',
                'template' => self::ELDERLY_VIDEO_TEMPLATE,
            ],
            guides: [
                'selfie' => 'Rostro descubierto y centrado, con buena luz de frente, sin lentes oscuros, gorra ni mascarilla. Fondo claro y sin otras personas.',
                'identification' => 'Cédula completa y legible, sin reflejos ni dedos sobre los datos, fotografiada sobre una superficie plana.',
            ],
        );
    }

    /**
     * El proveedor no emite certificados de un día por estos flujos.
     *
     * @return array<int, CertificatePeriod>
     */
    private static function periods(): array
    {
        return array_values(array_filter(CertificatePeriod::cases(), fn (CertificatePeriod $period) => ! $period->equals(CertificatePeriod::oneDay())));
    }
}
