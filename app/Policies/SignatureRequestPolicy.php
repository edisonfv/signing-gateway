<?php

namespace App\Policies;

use App\Models\ApiClient;
use App\Models\SignatureRequest;
use Illuminate\Auth\Access\Response;

/**
 * Aislamiento por tenant: una credencial solo ve las solicitudes de su tenant.
 * Se responde 404 (no 403) para no revelar que la solicitud existe.
 */
class SignatureRequestPolicy
{
    public function view(ApiClient $client, SignatureRequest $signatureRequest): Response
    {
        return $signatureRequest->tenant_id === $client->tenant_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function update(ApiClient $client, SignatureRequest $signatureRequest): Response
    {
        return $this->view($client, $signatureRequest);
    }
}
