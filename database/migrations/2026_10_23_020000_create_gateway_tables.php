<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas base del gateway (F0). Todas las PK son UUIDv7 (HasUuids).
 * Las columnas cifradas son TEXT, como recomienda la documentación de Laravel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('external_id')->unique()->comment('Identificador del tenant en el SaaS');
            $table->string('name');
            $table->unsignedInteger('daily_cap')->nullable()->comment('Tope duro diario; null usa el valor por defecto');
            $table->unsignedInteger('hourly_cap')->nullable()->comment('Tope duro por hora; null usa el valor por defecto');
            $table->timestamp('suspended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('signing_secret')->comment('Secreto HMAC, cifrado con la llave de la bóveda');
            $table->text('notification_secret')->comment('Secreto para firmar notificaciones al SaaS, cifrado');
            $table->text('delivery_public_key')->nullable()->comment('Llave pública X25519 (base64) para sellar certificados');
            $table->json('allowed_ips')->nullable()->comment('IPs o CIDR permitidos; null = cualquiera dentro de la red interna');
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('provider_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider', 50);
            $table->string('environment', 20);
            $table->string('name');
            $table->string('base_url');
            $table->text('credentials')->nullable()->comment('Token u otras credenciales del proveedor, cifrado con la bóveda');
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['provider', 'environment', 'is_active']);
        });

        Schema::create('signature_requests', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('También es el referenceTransaction enviado al proveedor');
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('api_client_id')->constrained();
            $table->foreignUuid('provider_account_id')->constrained();
            $table->string('idempotency_key', 100);
            $table->string('product', 30);
            $table->string('application_type', 30);
            $table->string('period', 20);
            $table->string('status', 30)->index();
            $table->string('current_step', 30)->nullable();
            $table->string('provider_session_id')->nullable()->index();
            $table->string('provider_status', 30)->nullable();
            $table->string('provider_stage', 30)->nullable();
            $table->char('identification_hash', 64)->index()->comment('Índice ciego de la cédula');
            $table->text('applicant')->comment('Datos del titular (encrypted:array)');
            $table->text('last_error')->nullable()->comment('Último error del proveedor (encrypted:array)');
            $table->unsignedSmallInteger('remaining_liveness_attempts')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['api_client_id', 'idempotency_key']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('signature_request_transitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('signature_request_id')->constrained()->cascadeOnDelete();
            $table->string('from', 30)->nullable();
            $table->string('to', 30);
            $table->string('source', 30)->comment('api, job, reconcile, operator');
            $table->json('context')->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('terms_acceptances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('signature_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('provider_terms_id');
            $table->string('version', 30);
            $table->char('content_sha256', 64);
            $table->timestamp('accepted_at');
            $table->text('applicant_ip')->comment('encrypted');
            $table->text('user_agent')->nullable()->comment('encrypted');
            $table->timestamps();
        });

        Schema::create('certificate_vault_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('signature_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('certificate')->nullable()->comment('p12 base64, cifrado con la llave de la bóveda');
            $table->text('password')->nullable()->comment('Contraseña del p12, cifrada con otra llave');
            $table->char('certificate_sha256', 64);
            $table->string('serial_number')->nullable();
            $table->timestamp('received_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('actor');
            $table->string('action', 100)->index();
            $table->nullableUuidMorphs('subject');
            $table->string('ip', 45)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('certificate_vault_entries');
        Schema::dropIfExists('terms_acceptances');
        Schema::dropIfExists('signature_request_transitions');
        Schema::dropIfExists('signature_requests');
        Schema::dropIfExists('provider_accounts');
        Schema::dropIfExists('api_clients');
        Schema::dropIfExists('tenants');
    }
};
