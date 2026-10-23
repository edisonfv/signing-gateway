<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

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
 */
final class SessionErrorCode extends ScreamingSnakeEnum
{
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
        ];
    }
}
