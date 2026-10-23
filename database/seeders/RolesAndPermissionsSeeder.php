<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Crea roles y permisos desde los enums. Es idempotente.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleName::cases() as $roleName) {
            $guard = $roleName->guard();

            $permissions = array_map(
                fn (PermissionName $permission) => Permission::findOrCreate($permission->value, $guard),
                $roleName->permissions(),
            );

            Role::findOrCreate($roleName->value, $guard)->syncPermissions($permissions);
        }
    }
}
