<?php

namespace App\Http\Resources;

use App\Models\SignatureRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Estado de una solicitud del Flujo por API. Nunca incluye datos del titular,
 * documentos, credenciales ni el certificado.
 *
 * @mixin SignatureRequest
 */
class SignatureSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'step' => $this->current_step?->value,
            'application_type' => $this->application_type->value,
            'period' => $this->period->value,
            'provider_status' => $this->provider_status,
            'remaining_liveness_attempts' => $this->remaining_liveness_attempts,
            'error' => $this->last_error === null ? null : [
                'message' => $this->last_error['message'] ?? null,
                'provider_status' => $this->last_error['session_status'] ?? null,
            ],
            'certificate_available' => $this->status->value === 'CERTIFICATE_STORED',
            'created_at' => $this->created_at?->toIso8601String(),
            'issued_at' => $this->issued_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
        ];
    }
}
