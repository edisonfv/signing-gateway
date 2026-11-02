<?php

namespace App\Models;

use App\Enums\CertificatePeriod;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Producto que un tenant contrató y sus condiciones: vigencias y cuenta de proveedor preferida.
 */
#[Fillable(['tenant_id', 'product', 'periods', 'preferred_provider_account_id', 'is_active'])]
class TenantProduct extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'product' => ProductType::class,
            'periods' => 'array',
            'is_active' => 'boolean',
        ];
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
    public function preferredProviderAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class, 'preferred_provider_account_id');
    }

    /**
     * Vigencias contratadas, o null si el tenant puede vender todas las que ofrece el proveedor.
     *
     * @return array<int, CertificatePeriod>|null
     */
    public function allowedPeriods(): ?array
    {
        return $this->periods === null ? null : array_map(fn (string $period) => CertificatePeriod::from($period), $this->periods);
    }
}
