<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La plataforma deja de ser un "tenant": sus credenciales (clients.manage, platform.service) no
     * tienen tenant. Se mueven las existentes y se borra el tenant que solo existía para ellas.
     */
    public function up(): void
    {
        Schema::table('api_clients', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->comment('null = credencial de la plataforma')->change();
        });

        $platformClients = DB::table('personal_access_tokens')
            ->where('tokenable_type', 'like', '%ApiClient')
            ->where(fn ($query) => $query->where('abilities', 'like', '%clients.manage%')->orWhere('abilities', 'like', '%platform.service%'))
            ->pluck('tokenable_id')
            ->unique();

        $formerTenants = DB::table('api_clients')->whereIn('id', $platformClients)->whereNotNull('tenant_id')->pluck('tenant_id')->unique();
        DB::table('api_clients')->whereIn('id', $platformClients)->update(['tenant_id' => null]);

        foreach ($formerTenants as $tenantId) {
            $inUse = DB::table('api_clients')->where('tenant_id', $tenantId)->exists()
                || DB::table('signature_requests')->where('tenant_id', $tenantId)->exists();

            if (! $inUse) {
                DB::table('tenant_products')->where('tenant_id', $tenantId)->delete();
                DB::table('tenants')->where('id', $tenantId)->delete();
            }
        }
    }

    /**
     * No se puede volver atrás sin un tenant: las credenciales de la plataforma se revocan.
     */
    public function down(): void
    {
        DB::table('personal_access_tokens')
            ->whereIn('tokenable_id', DB::table('api_clients')->whereNull('tenant_id')->select('id'))
            ->delete();
        DB::table('api_clients')->whereNull('tenant_id')->delete();

        Schema::table('api_clients', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable(false)->change();
        });
    }
};
