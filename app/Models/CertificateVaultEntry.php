<?php

namespace App\Models;

use App\Casts\VaultEncrypted;
use App\Security\KeyRing;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * p12 y contraseña cifrados con llaves distintas entre sí y distintas de APP_KEY.
 *
 * Se vacían (no se borra la fila, que queda como trazabilidad) al confirmarse la entrega
 * o al vencer la retención; ver prunable().
 */
#[Fillable(['signature_request_id', 'certificate', 'password', 'certificate_sha256', 'serial_number', 'received_at', 'delivered_at', 'purged_at'])]
#[Hidden(['certificate', 'password'])]
class CertificateVaultEntry extends Model
{
    use HasUuids, Prunable;

    protected function casts(): array
    {
        return [
            'certificate' => VaultEncrypted::class,
            'password' => VaultEncrypted::class.':'.KeyRing::VAULT_PASSWORD,
            'received_at' => 'datetime',
            'delivered_at' => 'datetime',
            'purged_at' => 'datetime',
        ];
    }

    public function isPurged(): bool
    {
        return $this->purged_at !== null;
    }

    public function purge(): void
    {
        $this->forceFill(['certificate' => null, 'password' => null, 'purged_at' => now()])->save();
    }

    /**
     * Entradas con material sensible que ya debe eliminarse.
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereNull('purged_at')
            ->where(fn (Builder $query) => $query
                ->whereNotNull('delivered_at')
                ->orWhere('received_at', '<=', now()->subDays(config('gateway.retention.undelivered_certificate_days'))));
    }

    /**
     * En lugar de borrar la fila se vacía el material sensible.
     */
    public function prune(): ?bool
    {
        $this->purge();

        return true;
    }

    /**
     * @return BelongsTo<SignatureRequest, $this>
     */
    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }
}
