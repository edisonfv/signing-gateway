<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Permisos (spatie/laravel-permission) y abilities de los tokens Sanctum.
 *
 * spatie/laravel-permission solo reconoce \BackedEnum, así que siempre se pasa ->value.
 *
 * @method static self signatureSessionsCreate()
 * @method static self signatureSessionsView()
 * @method static self signatureSessionsCancel()
 * @method static self certificatesRetrieve()
 * @method static self catalogsView()
 * @method static self limitsManage()
 * @method static self providersManage()
 * @method static self clientsManage()
 * @method static self auditView()
 */
final class PermissionName extends Enum
{
    /**
     * Permisos que puede tener la credencial de un cliente del SaaS (guard api).
     *
     * @return array<int, self>
     */
    public static function forApiClients(): array
    {
        return [
            self::signatureSessionsCreate(),
            self::signatureSessionsView(),
            self::signatureSessionsCancel(),
            self::certificatesRetrieve(),
            self::catalogsView(),
        ];
    }

    protected static function values(): array
    {
        return [
            'signatureSessionsCreate' => 'signature-sessions.create',
            'signatureSessionsView' => 'signature-sessions.view',
            'signatureSessionsCancel' => 'signature-sessions.cancel',
            'certificatesRetrieve' => 'certificates.retrieve',
            'catalogsView' => 'catalogs.view',
            'limitsManage' => 'limits.manage',
            'providersManage' => 'providers.manage',
            'clientsManage' => 'clients.manage',
            'auditView' => 'audit.view',
        ];
    }
}
