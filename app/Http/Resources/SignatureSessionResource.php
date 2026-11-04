<?php

namespace App\Http\Resources;

use App\Enums\SignatureFlow;
use App\Models\SignatureRequest;
use App\Signing\NextAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Estado de una solicitud de firma, igual para todo producto y proveedor. Nunca incluye datos del titular,
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
            'product' => $this->product->value,
            'flow' => $this->flow->value,
            'delivery_mode' => $this->flow->deliveryMode()->value,
            'status' => $this->status->value,
            'step' => $this->current_step?->value,
            'next_action' => NextAction::for($this->resource),
            'application_type' => $this->application_type->value,
            'period' => $this->period->value,
            'provider_status' => $this->provider_status,
            // Firmas Regulares: en qué está la revisión del proveedor. Null en el Flujo por API.
            'provider_review' => $this->flow->equals(SignatureFlow::documentReview()) ? [
                'stage' => $this->provider_stage,
                'operator_assigned_at' => $this->provider_review['operator_assigned_at'] ?? null,
                'observations' => $this->provider_review['observations'] ?? [],
                'updated_at' => $this->provider_review['updated_at'] ?? null,
            ] : null,
            'remaining_liveness_attempts' => $this->remaining_liveness_attempts,
            'error' => $this->last_error === null ? null : [
                'message' => $this->last_error['message'] ?? null,
                'provider_status' => $this->last_error['session_status'] ?? null,
                'codes' => $this->last_error['codes'] ?? [],
            ],
            'certificate_available' => $this->status->value === 'CERTIFICATE_STORED'
                && $this->flow->deliveryMode()->value === 'CLIENT_SEALED',
            'created_at' => $this->created_at?->toIso8601String(),
            'issued_at' => $this->issued_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
        ];
    }
}
