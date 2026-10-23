<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self superAdmin()
 * @method static self operator()
 * @method static self auditor()
 * @method static self saasClient()
 */
final class RoleName extends Enum
{
    public function guard(): string
    {
        return $this->equals(self::saasClient()) ? 'api' : 'web';
    }

    /**
     * @return array<int, PermissionName>
     */
    public function permissions(): array
    {
        return match ($this->value) {
            'super-admin' => PermissionName::cases(),
            'operator' => [PermissionName::limitsManage(), PermissionName::clientsManage(), PermissionName::auditView()],
            'auditor' => [PermissionName::auditView()],
            'saas-client' => PermissionName::forApiClients(),
        };
    }

    protected static function values(): array
    {
        return [
            'superAdmin' => 'super-admin',
            'operator' => 'operator',
            'auditor' => 'auditor',
            'saasClient' => 'saas-client',
        ];
    }
}
