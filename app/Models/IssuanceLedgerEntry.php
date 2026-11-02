<?php

namespace App\Models;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use App\Enums\SignatureFlow;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Un certificado emitido por el proveedor. Fuente de verdad para conciliar con el proveedor y
 * para que la plataforma facture: solo se inserta (ver IssuanceLedger), nunca se edita ni borra.
 */
#[Fillable([
    'sequence', 'signature_request_id', 'tenant_id', 'api_client_id', 'provider_account_id', 'provider',
    'environment', 'product', 'flow', 'application_type', 'period', 'provider_reference', 'serial_number',
    'identification_hash', 'issued_at', 'source', 'previous_hash', 'hash', 'created_at',
])]
class IssuanceLedgerEntry extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $table = 'issuance_ledger';

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('El registro de emisiones solo admite inserciones.'));
        static::deleting(fn () => throw new LogicException('El registro de emisiones solo admite inserciones.'));
    }

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'product' => ProductType::class,
            'flow' => SignatureFlow::class,
            'application_type' => ApplicationType::class,
            'period' => CertificatePeriod::class,
            'issued_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }

    /**
     * Contenido que sella el hash: todos los hechos de la emisión más el hash anterior.
     */
    public function canonicalContent(): string
    {
        return json_encode([
            'sequence' => $this->sequence,
            'signature_request_id' => $this->signature_request_id,
            'tenant_id' => $this->tenant_id,
            'api_client_id' => $this->api_client_id,
            'provider_account_id' => $this->provider_account_id,
            'provider' => $this->provider,
            'environment' => $this->environment,
            'product' => $this->product->value,
            'flow' => $this->flow->value,
            'application_type' => $this->application_type->value,
            'period' => $this->period->value,
            'provider_reference' => $this->provider_reference,
            'serial_number' => $this->serial_number,
            'identification_hash' => $this->identification_hash,
            'issued_at' => $this->issued_at->utc()->format('Y-m-d\TH:i:s\Z'),
            'source' => $this->source,
            'previous_hash' => $this->previous_hash,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    public function computeHash(): string
    {
        return hash('sha256', $this->canonicalContent());
    }

    /**
     * @return BelongsTo<SignatureRequest, $this>
     */
    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<ProviderAccount, $this>
     */
    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class);
    }
}
