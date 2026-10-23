<?php

namespace App\Models;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Enums\SessionStep;
use App\Exceptions\InvalidStatusTransition;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * Solicitud canónica de firma. Su UUID es el referenceTransaction del proveedor.
 */
#[Fillable([
    'tenant_id', 'api_client_id', 'provider_account_id', 'idempotency_key', 'product',
    'application_type', 'period', 'status', 'current_step', 'provider_session_id', 'provider_request_id',
    'provider_status', 'provider_stage', 'identification_hash', 'applicant', 'last_error',
    'remaining_liveness_attempts', 'issued_at', 'delivered_at', 'closed_at',
])]
#[Hidden(['applicant', 'identification_hash', 'last_error'])]
class SignatureRequest extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'product' => ProductType::class,
            'application_type' => ApplicationType::class,
            'period' => CertificatePeriod::class,
            'status' => RequestStatus::class,
            'current_step' => SessionStep::class.':nullable',
            'applicant' => 'encrypted:array',
            'last_error' => 'encrypted:array',
            'remaining_liveness_attempts' => 'integer',
            'issued_at' => 'datetime',
            'delivered_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_reconciled_at' => 'datetime',
        ];
    }

    public function referenceTransaction(): string
    {
        return $this->id;
    }

    /**
     * Cambia el estado validando la máquina de estados y deja la transición registrada.
     *
     * @param  array<string, mixed>  $context
     */
    public function transitionTo(RequestStatus $next, string $source, array $context = []): void
    {
        if (! $this->status->canTransitionTo($next)) {
            throw InvalidStatusTransition::between($this, $next);
        }

        DB::transaction(function () use ($next, $source, $context) {
            $from = $this->status;

            $this->status = $next;
            $this->closed_at = $next->isTerminal() ? now() : null;
            $this->save();

            $this->transitions()->create([
                'from' => $from->value,
                'to' => $next->value,
                'source' => $source,
                'context' => $context ?: null,
            ]);
        });
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<ApiClient, $this>
     */
    public function apiClient(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class);
    }

    /**
     * @return BelongsTo<ProviderAccount, $this>
     */
    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class);
    }

    /**
     * @return HasMany<SignatureRequestTransition, $this>
     */
    public function transitions(): HasMany
    {
        return $this->hasMany(SignatureRequestTransition::class);
    }

    /**
     * @return HasOne<TermsAcceptance, $this>
     */
    public function termsAcceptance(): HasOne
    {
        return $this->hasOne(TermsAcceptance::class);
    }

    /**
     * @return HasMany<SignatureDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SignatureDocument::class);
    }

    /**
     * @return HasOne<CertificateVaultEntry, $this>
     */
    public function vaultEntry(): HasOne
    {
        return $this->hasOne(CertificateVaultEntry::class);
    }
}
