<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Evidencia de aceptación de términos y condiciones (ítem del checklist de certificación).
 */
#[Fillable(['signature_request_id', 'provider_terms_id', 'version', 'content_sha256', 'accepted_at', 'applicant_ip', 'user_agent'])]
#[Hidden(['applicant_ip', 'user_agent'])]
class TermsAcceptance extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'applicant_ip' => 'encrypted',
            'user_agent' => 'encrypted',
        ];
    }

    /**
     * @return BelongsTo<SignatureRequest, $this>
     */
    public function signatureRequest(): BelongsTo
    {
        return $this->belongsTo(SignatureRequest::class);
    }
}
