<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Códigos de error de los pasos del Flujo por API.
 *
 * Fuente: https://docs.firmaseguraec.com/solicitud-de-firma-34136100e0
 *
 * @method static self underageCitizen()
 * @method static self ineligibleCitizenStatus()
 * @method static self expiredIdentification()
 * @method static self taskAlreadyCompleted()
 * @method static self taskOrderViolation()
 * @method static self weakPassword()
 * @method static self duplicateReferenceTransaction()
 * @method static self faceLivenessFailed()
 */
final class SessionErrorCode extends Enum
{
    protected static function values(): array
    {
        return [
            'underageCitizen' => 'UNDERAGE_CITIZEN',
            'ineligibleCitizenStatus' => 'INELIGIBLE_CITIZEN_STATUS',
            'expiredIdentification' => 'EXPIRED_IDENTIFICATION',
            'taskAlreadyCompleted' => 'TASK_ALREADY_COMPLETED',
            'taskOrderViolation' => 'TASK_ORDER_VIOLATION',
            'weakPassword' => 'WEAK_PASSWORD',
            'duplicateReferenceTransaction' => 'DUPLICATE_REFERENCE_TRANSACTION',
            'faceLivenessFailed' => 'FACE_LIVENESS_FAILED',
        ];
    }

    protected static function labels(): array
    {
        return [
            'underageCitizen' => 'El titular es menor de edad',
            'ineligibleCitizenStatus' => 'El estado civil del titular no permite firmar',
            'expiredIdentification' => 'Cédula caducada',
            'taskAlreadyCompleted' => 'Este paso ya fue completado',
            'taskOrderViolation' => 'Hay pasos previos pendientes',
            'weakPassword' => 'La contraseña no cumple la política',
            'duplicateReferenceTransaction' => 'Ya existe una solicitud en proceso con esa referencia',
            'faceLivenessFailed' => 'El rostro no coincide con la cédula; puede reintentar (observado en sandbox, no documentado)',
        ];
    }
}
