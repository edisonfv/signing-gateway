<?php

namespace App\Http\Resources;

use App\Models\IssuanceLedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una emisión del registro. Sin datos personales: la cédula solo como índice ciego.
 *
 * @mixin IssuanceLedgerEntry
 */
class IssuanceLedgerEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sequence' => $this->sequence,
            'signature_session_id' => $this->signature_request_id,
            'tenant_external_id' => $this->tenant->external_id,
            'api_client_id' => $this->api_client_id,
            'product' => $this->product->value,
            'flow' => $this->flow->value,
            'application_type' => $this->application_type->value,
            'period' => $this->period->value,
            'provider' => $this->provider,
            'provider_account_id' => $this->provider_account_id,
            'environment' => $this->environment,
            'provider_reference' => $this->provider_reference,
            'serial_number' => $this->serial_number,
            'issued_at' => $this->issued_at->toIso8601String(),
            'hash' => $this->hash,
        ];
    }
}
