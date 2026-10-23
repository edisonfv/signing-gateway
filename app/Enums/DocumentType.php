<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Documentos que el SaaS envía en Firmas Regulares (base64 en el JSON de la solicitud).
 *
 * @method static self identificationFront()
 * @method static self identificationBack()
 * @method static self identificationSelfie()
 * @method static self companyRucPdf()
 * @method static self representativeAppointmentPdf()
 * @method static self appointmentAcceptancePdf()
 * @method static self companyConstitutionPdf()
 * @method static self authorizationVideo()
 */
final class DocumentType extends Enum
{
    private const IMAGE_MIMES = ['image/jpeg', 'image/png'];

    /**
     * @return array<int, string>
     */
    public function allowedMimes(): array
    {
        return match (true) {
            $this->isImage() => self::IMAGE_MIMES,
            $this->equals(self::authorizationVideo()) => ['video/mp4'],
            default => ['application/pdf'],
        };
    }

    public function maxBytes(): int
    {
        return match (true) {
            $this->isImage() => 5 * 1024 * 1024,
            $this->equals(self::authorizationVideo()) => 20 * 1024 * 1024,
            default => 5 * 1024 * 1024,
        };
    }

    public function isImage(): bool
    {
        return $this->equals(self::identificationFront(), self::identificationBack(), self::identificationSelfie());
    }

    protected static function values(): array
    {
        return [
            'identificationFront' => 'identification_front',
            'identificationBack' => 'identification_back',
            'identificationSelfie' => 'identification_selfie',
            'companyRucPdf' => 'company_ruc_pdf',
            'representativeAppointmentPdf' => 'representative_appointment_pdf',
            'appointmentAcceptancePdf' => 'appointment_acceptance_pdf',
            'companyConstitutionPdf' => 'company_constitution_pdf',
            'authorizationVideo' => 'authorization_video',
        ];
    }

    protected static function labels(): array
    {
        return [
            'identificationFront' => 'Cédula (anverso), JPG o PNG',
            'identificationBack' => 'Cédula (reverso), JPG o PNG',
            'identificationSelfie' => 'Selfie del titular, JPG o PNG',
            'companyRucPdf' => 'RUC de la empresa o persona natural, PDF',
            'representativeAppointmentPdf' => 'Nombramiento del representante legal, PDF',
            'appointmentAcceptancePdf' => 'Aceptación del nombramiento, PDF',
            'companyConstitutionPdf' => 'Constitución de la compañía, PDF',
            'authorizationVideo' => 'Video de autorización (mayores de 65 años), MP4',
        ];
    }
}
